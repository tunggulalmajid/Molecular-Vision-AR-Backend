<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Authenticate user and issue Sanctum token.
     */
    #[OA\Post(
        path: '/login',
        summary: 'Login Pengguna',
        description: 'Melakukan autentikasi menggunakan email dan kata sandi, kemudian mengembalikan data user serta personal access token Sanctum.',
        tags: ['Auth'],
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Kredensial login pengguna',
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'budi@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Rahasia123!'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login berhasil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Login berhasil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id_user', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Budi Santoso'),
                                        new OA\Property(property: 'email', type: 'string', example: 'budi@example.com'),
                                        new OA\Property(property: 'school', type: 'string', nullable: true, example: 'SMAN 1 Jakarta'),
                                        new OA\Property(property: 'id_degree', type: 'integer', nullable: true, example: 1),
                                        new OA\Property(property: 'id_role', type: 'integer', nullable: true, example: 2),
                                        new OA\Property(
                                            property: 'role',
                                            type: 'object',
                                            nullable: true,
                                            properties: [
                                                new OA\Property(property: 'id', type: 'integer', example: 2),
                                                new OA\Property(property: 'name', type: 'string', example: 'siswa (mobile)'),
                                            ]
                                        ),
                                        new OA\Property(
                                            property: 'degree',
                                            type: 'object',
                                            nullable: true,
                                            properties: [
                                                new OA\Property(property: 'id_degree', type: 'integer', example: 1),
                                                new OA\Property(property: 'name', type: 'string', example: 'SMA / MA'),
                                            ]
                                        ),
                                    ]
                                ),
                                new OA\Property(property: 'token', type: 'string', example: '1|xyz123abc456...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Kredensial tidak valid (email atau kata sandi salah).',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Email atau kata sandi tidak valid.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi form gagal.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The email field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'email',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The email field is required.')
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi tidak valid.',
            ], 401);
        }

        $token = $user->createToken('mobile_auth_token')->plainTextToken;

        $user->load(['role:id,name', 'degree:id_degree,name']);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Register a new student user.
     */
    #[OA\Post(
        path: '/register',
        summary: 'Registrasi Pengguna Baru',
        description: 'Mendaftarkan akun siswa baru melalui aplikasi mobile dan mengembalikan personal access token (Sanctum) untuk autentikasi selanjutnya.',
        tags: ['Auth'],
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Data pendaftaran akun siswa baru',
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Budi Santoso'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'budi@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Rahasia123!'),
                    new OA\Property(property: 'school', type: 'string', maxLength: 255, nullable: true, example: 'SMAN 1 Jakarta'),
                    new OA\Property(property: 'id_degree', type: 'integer', nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Registrasi berhasil dan token autentikasi dibuat.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Registrasi berhasil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id_user', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Budi Santoso'),
                                        new OA\Property(property: 'email', type: 'string', example: 'budi@example.com'),
                                        new OA\Property(property: 'school', type: 'string', nullable: true, example: 'SMAN 1 Jakarta'),
                                        new OA\Property(property: 'id_degree', type: 'integer', nullable: true, example: 1),
                                        new OA\Property(property: 'id_role', type: 'integer', nullable: true, example: 2),
                                        new OA\Property(
                                            property: 'role',
                                            type: 'object',
                                            nullable: true,
                                            properties: [
                                                new OA\Property(property: 'id', type: 'integer', example: 2),
                                                new OA\Property(property: 'name', type: 'string', example: 'siswa (mobile)'),
                                            ]
                                        ),
                                        new OA\Property(
                                            property: 'degree',
                                            type: 'object',
                                            nullable: true,
                                            properties: [
                                                new OA\Property(property: 'id_degree', type: 'integer', example: 1),
                                                new OA\Property(property: 'name', type: 'string', example: 'SMA / MA'),
                                            ]
                                        ),
                                    ]
                                ),
                                new OA\Property(property: 'token', type: 'string', example: '1|xyz123abc456...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi data registrasi gagal.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'email',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The email has already been taken.')
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default()],
            'school' => ['nullable', 'string', 'max:255'],
            'id_degree' => ['nullable', 'integer', 'exists:degrees,id_degree'],
        ];

        $validated = $request->validate($rules);

        $user = DB::transaction(function () use ($validated) {
            $siswaRole = Role::where('name', 'siswa (mobile)')->first();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'school' => $validated['school'] ?? null,
                'id_degree' => $validated['id_degree'] ?? null,
                'id_role' => $siswaRole?->id,
            ]);

            if ($siswaRole) {
                $user->assignRole($siswaRole);
            }

            return $user;
        });

        $token = $user->createToken('mobile_auth_token')->plainTextToken;

        $user->load(['role:id,name', 'degree:id_degree,name']);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * Get authenticated user profile.
     */
    #[OA\Get(
        path: '/me',
        summary: 'Ambil Profil Pengguna (Me)',
        description: 'Mengembalikan detail profil pengguna yang sedang login berdasarkan Bearer Token Sanctum.',
        tags: ['Auth'],
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profil pengguna berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Data profil berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id_user', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Budi Santoso'),
                                        new OA\Property(property: 'email', type: 'string', example: 'budi@example.com'),
                                        new OA\Property(property: 'school', type: 'string', nullable: true, example: 'SMAN 1 Jakarta'),
                                        new OA\Property(property: 'id_degree', type: 'integer', nullable: true, example: 1),
                                        new OA\Property(property: 'id_role', type: 'integer', nullable: true, example: 2),
                                        new OA\Property(
                                            property: 'role',
                                            type: 'object',
                                            nullable: true,
                                            properties: [
                                                new OA\Property(property: 'id', type: 'integer', example: 2),
                                                new OA\Property(property: 'name', type: 'string', example: 'siswa (mobile)'),
                                            ]
                                        ),
                                        new OA\Property(
                                            property: 'degree',
                                            type: 'object',
                                            nullable: true,
                                            properties: [
                                                new OA\Property(property: 'id_degree', type: 'integer', example: 1),
                                                new OA\Property(property: 'name', type: 'string', example: 'SMA / MA'),
                                            ]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated - Token tidak valid atau tidak disertakan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user->load(['role:id,name', 'degree:id_degree,name']);

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil.',
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * Refresh personal access token (rotate token).
     */
    #[OA\Post(
        path: '/refresh',
        summary: 'Pembaruan Token Autentikasi (Refresh)',
        description: 'Memperbarui token autentikasi lama (termasuk yang sudah kedaluwarsa) dengan token baru. Token lama dapat dikirimkan melalui Authorization Bearer header atau body { "token": "..." }.',
        tags: ['Auth'],
        security: [],
        requestBody: new OA\RequestBody(
            required: false,
            description: 'Token lama (opsional jika sudah dikirim via header Authorization Bearer)',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'token', type: 'string', example: '1|xyz123abc456...'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token berhasil diperbarui.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Token autentikasi berhasil diperbarui.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: '2|new_token_string...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid atau tidak ditemukan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Token tidak valid atau sudah tidak terdaftar.'),
                    ]
                )
            ),
        ]
    )]
    public function refresh(Request $request): JsonResponse
    {
        $rawToken = $request->bearerToken() ?? $request->input('token');

        if (! $rawToken || ! is_string($rawToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Sertakan token pada Authorization header atau body request.',
            ], 401);
        }

        $tokenInstance = PersonalAccessToken::findToken($rawToken);

        if (! $tokenInstance) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah tidak terdaftar.',
            ], 401);
        }

        /** @var User|null $user */
        $user = $tokenInstance->tokenable;

        if (! $user) {
            $tokenInstance->delete();

            return response()->json([
                'success' => false,
                'message' => 'Pengguna untuk token ini tidak ditemukan.',
            ], 401);
        }

        // Hapus token lama
        $tokenInstance->delete();

        // Terbitkan token baru
        $newToken = $user->createToken('mobile_auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token autentikasi berhasil diperbarui.',
            'data' => [
                'token' => $newToken,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout and revoke current token.
     */
    #[OA\Delete(
        path: '/logout',
        summary: 'Logout Pengguna (Cabut Token)',
        description: 'Mencabut (menghapus) token personal access Sanctum yang sedang aktif digunakan.',
        tags: ['Auth'],
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout berhasil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout berhasil. Token telah dicabut.'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated - Token tidak valid atau sudah tidak aktif.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        /** @var PersonalAccessToken|null $currentToken */
        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $currentToken->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dicabut.',
        ]);
    }
}
