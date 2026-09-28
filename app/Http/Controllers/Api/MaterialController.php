<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MaterialController extends Controller
{
    /**
     * Display a listing of learning materials.
     */
    #[OA\Get(
        path: '/materials',
        summary: 'Daftar Materi Pembelajaran Kimia',
        description: 'Mengambil daftar ringkas materi pembelajaran kimia dengan opsi filter kategori dan pencarian judul materi.',
        tags: ['Materi'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'id_category',
                in: 'query',
                description: 'Filter materi berdasarkan ID kategori kimia (misal: 1 untuk Stoikiometri)',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
            new OA\Parameter(
                name: 'search',
                in: 'query',
                description: 'Kata kunci pencarian judul atau deskripsi materi',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'Hukum Dasar')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar materi berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Daftar materi berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id_material', type: 'integer', example: 1),
                                    new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Hukum Dasar Kimia & Konsep Mol'),
                                    new OA\Property(property: 'description', type: 'string', example: 'Pengantar perhitungan kimia dan stoikiometri dasar.'),
                                    new OA\Property(
                                        property: 'category',
                                        type: 'object',
                                        nullable: true,
                                        properties: [
                                            new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'Stoikiometri'),
                                        ]
                                    ),
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
    public function index(Request $request): JsonResponse
    {
        $materials = Material::query()
            ->with(['category:id_category,name'])
            ->when($request->filled('id_category'), function ($q) use ($request) {
                $q->where('id_category', $request->input('id_category'));
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->input('search'));
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->select(['id_material', 'id_category', 'name', 'description'])
            ->orderBy('id_material')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar materi berhasil diambil.',
            'data' => $materials,
        ]);
    }

    /**
     * Display the specified material details.
     */
    #[OA\Get(
        path: '/materials/{id}',
        summary: 'Detail Konten Materi Pembelajaran',
        description: 'Mengambil detail lengkap materi pembelajaran kimia termasuk isi konten teori (HTML / teks).',
        tags: ['Materi'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID materi pembelajaran',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail materi berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Detail materi berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id_material', type: 'integer', example: 1),
                                new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Hukum Dasar Kimia & Konsep Mol'),
                                new OA\Property(property: 'description', type: 'string', example: 'Pengantar perhitungan kimia dan stoikiometri dasar.'),
                                new OA\Property(property: 'content', type: 'string', example: '<h2>Konsep Mol</h2><p>Mol adalah satuan dasar dalam SI yang menyatakan jumlah zat...</p>'),
                                new OA\Property(
                                    property: 'category',
                                    type: 'object',
                                    nullable: true,
                                    properties: [
                                        new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Stoikiometri'),
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
                response: 404,
                description: 'Materi pembelajaran tidak ditemukan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Materi pembelajaran tidak ditemukan.'),
                    ]
                )
            ),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $material = Material::with(['category:id_category,name'])->find($id);

        if (! $material) {
            return response()->json([
                'success' => false,
                'message' => 'Materi pembelajaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail materi berhasil diambil.',
            'data' => $material,
        ]);
    }
}
