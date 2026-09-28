<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Material;
use App\Models\Molecule;
use App\Models\UserCategoryProgress;
use App\Models\UserMaterialProgress;
use App\Models\UserMoleculeProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProgressController extends Controller
{
    /**
     * Get overall learning and quiz progress for the authenticated user.
     */
    #[OA\Get(
        path: '/progress',
        summary: 'Ringkasan & Detail Progres Belajar',
        description: 'Mengambil ringkasan progres belajar yang seragam untuk materi, molekul 3D, dan kuis (jumlah total, telah selesai, belum selesai, dan persentase) serta daftar ID tuntas untuk lookup cepat O(1) di aplikasi mobile.',
        tags: ['Progres Belajar'],
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data progres belajar berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Data progres belajar berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'materials',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'integer', example: 12),
                                        new OA\Property(property: 'completed', type: 'integer', example: 8),
                                        new OA\Property(property: 'uncompleted', type: 'integer', example: 4),
                                        new OA\Property(property: 'percentage', type: 'number', format: 'float', example: 66.7),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'molecules',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'integer', example: 10),
                                        new OA\Property(property: 'completed', type: 'integer', example: 5),
                                        new OA\Property(property: 'uncompleted', type: 'integer', example: 5),
                                        new OA\Property(property: 'percentage', type: 'number', format: 'float', example: 50.0),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'quiz',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'integer', example: 4),
                                        new OA\Property(property: 'completed', type: 'integer', example: 2),
                                        new OA\Property(property: 'uncompleted', type: 'integer', example: 2),
                                        new OA\Property(property: 'percentage', type: 'number', format: 'float', example: 50.0),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'overall',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'integer', example: 26),
                                        new OA\Property(property: 'completed', type: 'integer', example: 15),
                                        new OA\Property(property: 'uncompleted', type: 'integer', example: 11),
                                        new OA\Property(property: 'percentage', type: 'number', format: 'float', example: 57.7),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'completed_ids',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(
                                            property: 'materials',
                                            type: 'array',
                                            items: new OA\Items(type: 'integer'),
                                            example: [1, 2, 4, 5, 7, 8, 9, 10]
                                        ),
                                        new OA\Property(
                                            property: 'molecules',
                                            type: 'array',
                                            items: new OA\Items(type: 'integer'),
                                            example: [1, 3, 5, 6, 8]
                                        ),
                                        new OA\Property(
                                            property: 'quiz',
                                            type: 'array',
                                            items: new OA\Items(type: 'integer'),
                                            example: [1, 2]
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
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $user->id_user;

        // 1. Progress Materi
        $totalMaterials = Material::count();
        $completedMaterials = UserMaterialProgress::query()
            ->where('id_user', $userId)
            ->where('is_completed', true)
            ->count();
        $uncompletedMaterials = max(0, $totalMaterials - $completedMaterials);
        $materialsPercentage = $totalMaterials > 0
            ? round(($completedMaterials / $totalMaterials) * 100, 1)
            : 0.0;

        // 2. Progress Molekul 3D
        $totalMolecules = Molecule::count();
        $completedMolecules = UserMoleculeProgress::query()
            ->where('id_user', $userId)
            ->where('is_completed', true)
            ->count();
        $uncompletedMolecules = max(0, $totalMolecules - $completedMolecules);
        $moleculesPercentage = $totalMolecules > 0
            ? round(($completedMolecules / $totalMolecules) * 100, 1)
            : 0.0;

        // 3. Progress Kategori Kuis / Soal
        $totalCategories = Category::count();
        $completedCategories = UserCategoryProgress::query()
            ->where('id_user', $userId)
            ->where('is_completed', true)
            ->count();
        $uncompletedCategories = max(0, $totalCategories - $completedCategories);
        $categoriesPercentage = $totalCategories > 0
            ? round(($completedCategories / $totalCategories) * 100, 1)
            : 0.0;

        // 4. Progress Keseluruhan (Overall)
        $totalOverall = $totalMaterials + $totalMolecules + $totalCategories;
        $completedOverall = $completedMaterials + $completedMolecules + $completedCategories;
        $uncompletedOverall = max(0, $totalOverall - $completedOverall);
        $overallPercentage = $totalOverall > 0
            ? round(($completedOverall / $totalOverall) * 100, 1)
            : 0.0;

        // 5. Completed IDs (untuk cek O(1) di mobile app)
        $completedMaterialIds = UserMaterialProgress::query()
            ->where('id_user', $userId)
            ->where('is_completed', true)
            ->pluck('id_material')
            ->map(fn ($id) => (int) $id)
            ->values();

        $completedMoleculeIds = UserMoleculeProgress::query()
            ->where('id_user', $userId)
            ->where('is_completed', true)
            ->pluck('id_molecule')
            ->map(fn ($id) => (int) $id)
            ->values();

        $completedCategoryIds = UserCategoryProgress::query()
            ->where('id_user', $userId)
            ->where('is_completed', true)
            ->pluck('id_category')
            ->map(fn ($id) => (int) $id)
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Data progres belajar berhasil diambil.',
            'data' => [
                'materials' => [
                    'total' => $totalMaterials,
                    'completed' => $completedMaterials,
                    'uncompleted' => $uncompletedMaterials,
                    'percentage' => $materialsPercentage,
                ],
                'molecules' => [
                    'total' => $totalMolecules,
                    'completed' => $completedMolecules,
                    'uncompleted' => $uncompletedMolecules,
                    'percentage' => $moleculesPercentage,
                ],
                'quiz' => [
                    'total' => $totalCategories,
                    'completed' => $completedCategories,
                    'uncompleted' => $uncompletedCategories,
                    'percentage' => $categoriesPercentage,
                ],
                'overall' => [
                    'total' => $totalOverall,
                    'completed' => $completedOverall,
                    'uncompleted' => $uncompletedOverall,
                    'percentage' => $overallPercentage,
                ],
                'completed_ids' => [
                    'materials' => $completedMaterialIds,
                    'molecules' => $completedMoleculeIds,
                    'quiz' => $completedCategoryIds,
                ],
            ],
        ]);
    }

    /**
     * Mark a learning material as completed or update its reading status.
     */
    #[OA\Post(
        path: '/progress/material',
        summary: 'Tandai Progres Materi Selesai',
        description: 'Mencatat bahwa suatu materi pembelajaran telah selesai dibaca/dipelajari oleh pengguna.',
        tags: ['Progres Belajar'],
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_material'],
                properties: [
                    new OA\Property(property: 'id_material', type: 'integer', description: 'ID materi yang dibaca', example: 3),
                    new OA\Property(property: 'is_completed', type: 'boolean', description: 'Status selesai (default: true)', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Progres materi berhasil diperbarui.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Progres materi berhasil diperbarui.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id_material', type: 'integer', example: 3),
                                new OA\Property(property: 'is_completed', type: 'boolean', example: true),
                                new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: '2026-09-28T21:45:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The id material field is required.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function storeMaterial(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_material' => ['required', 'integer', 'exists:materials,id_material'],
            'is_completed' => ['nullable', 'boolean'],
        ]);

        $userId = $request->user()->id_user;
        $isCompleted = $request->has('is_completed') ? $request->boolean('is_completed') : true;

        $progress = UserMaterialProgress::updateOrCreate(
            [
                'id_user' => $userId,
                'id_material' => $validated['id_material'],
            ],
            [
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progres materi berhasil diperbarui.',
            'data' => [
                'id_material' => $progress->id_material,
                'is_completed' => (bool) $progress->is_completed,
                'completed_at' => $progress->completed_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Mark a 3D molecule as completed or explored.
     */
    #[OA\Post(
        path: '/progress/molecule',
        summary: 'Tandai Progres Eksplorasi Molekul 3D',
        description: 'Mencatat bahwa suatu model molekul 3D telah selesai dieksplorasi oleh pengguna.',
        tags: ['Progres Belajar'],
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_molecule'],
                properties: [
                    new OA\Property(property: 'id_molecule', type: 'integer', description: 'ID molekul 3D', example: 5),
                    new OA\Property(property: 'is_completed', type: 'boolean', description: 'Status selesai (default: true)', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Progres molekul berhasil diperbarui.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Progres molekul berhasil diperbarui.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id_molecule', type: 'integer', example: 5),
                                new OA\Property(property: 'is_completed', type: 'boolean', example: true),
                                new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: '2026-09-28T21:45:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The id molecule field is required.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function storeMolecule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_molecule' => ['required', 'integer', 'exists:molecules,id_molecule'],
            'is_completed' => ['nullable', 'boolean'],
        ]);

        $userId = $request->user()->id_user;
        $isCompleted = $request->has('is_completed') ? $request->boolean('is_completed') : true;

        $progress = UserMoleculeProgress::updateOrCreate(
            [
                'id_user' => $userId,
                'id_molecule' => $validated['id_molecule'],
            ],
            [
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progres molekul berhasil diperbarui.',
            'data' => [
                'id_molecule' => $progress->id_molecule,
                'is_completed' => (bool) $progress->is_completed,
                'completed_at' => $progress->completed_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Mark a quiz category as completed.
     */
    #[OA\Post(
        path: '/progress/category',
        summary: 'Tandai Progres Kuis / Soal Kategori Selesai',
        description: 'Mencatat bahwa kumpulan latihan soal/kuis pada kategori tersebut telah selesai dikerjakan oleh pengguna.',
        tags: ['Progres Belajar'],
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_category'],
                properties: [
                    new OA\Property(property: 'id_category', type: 'integer', description: 'ID kategori yang soalnya telah diselesaikan', example: 2),
                    new OA\Property(property: 'is_completed', type: 'boolean', description: 'Status selesai (default: true)', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Progres pengerjaan soal kategori berhasil diperbarui.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Progres pengerjaan soal kategori berhasil diperbarui.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id_category', type: 'integer', example: 2),
                                new OA\Property(property: 'is_completed', type: 'boolean', example: true),
                                new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: '2026-09-28T21:45:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The id category field is required.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_category' => ['required', 'integer', 'exists:categories,id_category'],
            'is_completed' => ['nullable', 'boolean'],
        ]);

        $userId = $request->user()->id_user;
        $isCompleted = $request->has('is_completed') ? $request->boolean('is_completed') : true;

        $progress = UserCategoryProgress::updateOrCreate(
            [
                'id_user' => $userId,
                'id_category' => $validated['id_category'],
            ],
            [
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progres pengerjaan soal kategori berhasil diperbarui.',
            'data' => [
                'id_category' => $progress->id_category,
                'is_completed' => (bool) $progress->is_completed,
                'completed_at' => $progress->completed_at?->toISOString(),
            ],
        ]);
    }
}
