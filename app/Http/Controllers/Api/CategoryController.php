<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    /**
     * Display a listing of chemistry learning categories.
     */
    #[OA\Get(
        path: '/categories',
        summary: 'Daftar Kategori Pembelajaran Kimia',
        description: 'Mengambil daftar kategori materi kimia beserta jumlah materi dan latihan soal yang tersedia.',
        tags: ['Kategori'],
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar kategori berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Daftar kategori kimia berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Stoikiometri'),
                                    new OA\Property(property: 'order', type: 'integer', example: 1),
                                    new OA\Property(property: 'description', type: 'string', example: 'Mempelajari konsep mol, rumus empiris, dan stoikiometri larutan.'),
                                    new OA\Property(property: 'materials_count', type: 'integer', example: 4),
                                    new OA\Property(property: 'questions_count', type: 'integer', example: 15),
                                ]
                            )
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
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->withCount(['materials', 'questions'])
            ->orderBy('order', 'asc')
            ->orderBy('id_category', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori kimia berhasil diambil.',
            'data' => $categories,
        ]);
    }
}
