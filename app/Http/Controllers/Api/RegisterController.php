<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * Handle incoming API registration for users (mobile/siswa).
     */
    #[OA\Post(
        path: '/register',
        summary: 'Registrasi Pengguna Baru (Mobile / Siswa)',
        description: 'Mendaftarkan akun siswa baru melalui aplikasi mobile dan mengembalikan personal access token (Sanctum) untuk autentikasi selanjutnya.',
        tags: ['Authentication'],
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
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'Rahasia123!'),
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
                description: 'Validasi data gagal.',
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
    public function __invoke(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default()],
            'school' => ['nullable', 'string', 'max:255'],
            'id_degree' => ['nullable', 'integer', 'exists:degrees,id_degree'],
        ];

        if ($request->has('password_confirmation')) {
            $rules['password'][] = 'confirmed';
        }

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
}
