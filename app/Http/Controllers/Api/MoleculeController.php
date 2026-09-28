<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Molecule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MoleculeController extends Controller
{
    /**
     * Display a listing of 3D molecules.
     */
    #[OA\Get(
        path: '/molecules',
        summary: 'Daftar Katalog Molekul 3D',
        description: 'Mengambil daftar seluruh molekul kimia 3D dengan opsi pencarian (berdasarkan nama atau rumus kimia) dan filter bentuk geometri.',
        tags: ['Molekul'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'search',
                in: 'query',
                description: 'Kata kunci pencarian nama atau rumus molekul (misal: Air, H2O, CH4)',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'H2O')
            ),
            new OA\Parameter(
                name: 'shape',
                in: 'query',
                description: 'Filter berdasarkan bentuk geometri molekul (misal: Linear, Bent, Tetrahedral)',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'Bent')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar molekul berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Daftar molekul berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id_molecule', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Air'),
                                    new OA\Property(property: 'formula', type: 'string', example: 'H2O'),
                                    new OA\Property(property: 'shape', type: 'string', example: 'Bengkok (Bent)'),
                                    new OA\Property(property: 'bent', type: 'string', example: '104.5°'),
                                    new OA\Property(property: 'bond_type', type: 'string', example: 'Kovalen Polar'),
                                    new OA\Property(property: 'model_3d_url', type: 'string', example: 'https://storage.googleapis.com/.../h2o.glb'),
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
        $molecules = Molecule::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->input('search'));
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('formula', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('shape'), function ($q) use ($request) {
                $q->where('shape', $request->input('shape'));
            })
            ->select(['id_molecule', 'name', 'formula', 'shape', 'bent', 'bond_type', 'model_3d_url'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar molekul berhasil diambil.',
            'data' => $molecules,
        ]);
    }

    /**
     * Display the specified molecule details.
     */
    #[OA\Get(
        path: '/molecules/{id}',
        summary: 'Detail Molekul 3D',
        description: 'Mengambil detail lengkap suatu molekul kimia termasuk deskripsi teori dan URL model 3D.',
        tags: ['Molekul'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID molekul kimia',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail molekul berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Detail molekul berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id_molecule', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Air'),
                                new OA\Property(property: 'formula', type: 'string', example: 'H2O'),
                                new OA\Property(property: 'shape', type: 'string', example: 'Bengkok (Bent)'),
                                new OA\Property(property: 'bent', type: 'string', example: '104.5°'),
                                new OA\Property(property: 'bond_type', type: 'string', example: 'Kovalen Polar'),
                                new OA\Property(property: 'description', type: 'string', example: 'Molekul air terdiri atas dua atom hidrogen yang berikatan kovalen dengan satu atom oksigen...'),
                                new OA\Property(property: 'model_3d_url', type: 'string', example: 'https://storage.googleapis.com/.../h2o.glb'),
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
                description: 'Molekul kimia tidak ditemukan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Molekul kimia tidak ditemukan.'),
                    ]
                )
            ),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $molecule = Molecule::find($id);

        if (! $molecule) {
            return response()->json([
                'success' => false,
                'message' => 'Molekul kimia tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail molekul berhasil diambil.',
            'data' => $molecule,
        ]);
    }
}
