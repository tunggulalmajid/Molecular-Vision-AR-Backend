<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    /**
     * Get authenticated user profile.
     */
    #[OA\Get(
        path: '/user',
        summary: 'Ambil Profil Pengguna yang Sedang Login',
        description: 'Mengembalikan detail profil pengguna yang sedang login berdasarkan Bearer Token Sanctum.',
        tags: ['User'],
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
                        new OA\Property(
                            property: 'data',
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
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()?->load(['role:id,name', 'degree:id_degree,name']),
        ]);
    }
}
