<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    /**
     * Update authenticated user profile.
     */
    #[OA\Put(
        path: '/profile',
        summary: 'Pembaruan Profil Pengguna',
        description: 'Memperbarui data profil pengguna yang sedang login (nama, email, sekolah, dan jenjang kelas).',
        tags: ['Profile'],
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Data profil yang akan diperbarui',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Budi Santoso'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'budi_baru@example.com'),
                    new OA\Property(property: 'school', type: 'string', maxLength: 255, nullable: true, example: 'SMAN 1 Jakarta'),
                    new OA\Property(property: 'id_degree', type: 'integer', nullable: true, example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profil berhasil diperbarui.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Profil berhasil diperbarui.'),
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
                                        new OA\Property(property: 'email', type: 'string', example: 'budi_baru@example.com'),
                                        new OA\Property(property: 'school', type: 'string', nullable: true, example: 'SMAN 1 Jakarta'),
                                        new OA\Property(property: 'id_degree', type: 'integer', nullable: true, example: 2),
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
                                                new OA\Property(property: 'id_degree', type: 'integer', example: 2),
                                                new OA\Property(property: 'name', type: 'string', example: 'Kelas 11'),
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
            new OA\Response(
                response: 422,
                description: 'Validasi form gagal.',
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
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'school' => ['nullable', 'string', 'max:255'],
            'id_degree' => ['nullable', 'integer', 'exists:degrees,id_degree'],
        ]);

        $user->update($validated);
        $user->load(['role:id,name', 'degree:id_degree,name']);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * Update authenticated user password.
     */
    #[OA\Put(
        path: '/profile/password',
        summary: 'Pembaruan Kata Sandi (Password)',
        description: 'Memperbarui kata sandi akun pengguna dengan memverifikasi kata sandi lama terlebih dahulu.',
        tags: ['Profile'],
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Data kata sandi lama dan baru',
            content: new OA\JsonContent(
                required: ['current_password', 'password'],
                properties: [
                    new OA\Property(property: 'current_password', type: 'string', format: 'password', example: 'Rahasia123!'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'PasswordBaru123!'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Kata sandi berhasil diperbarui.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Kata sandi berhasil diperbarui.'),
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
            new OA\Response(
                response: 422,
                description: 'Kata sandi lama tidak sesuai atau format password baru tidak valid.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Kata sandi lama tidak sesuai.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'current_password',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'Kata sandi lama tidak sesuai.')
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::default()],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama tidak sesuai.',
                'errors' => [
                    'current_password' => ['Kata sandi lama tidak sesuai.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui.',
        ]);
    }
}
