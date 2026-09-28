<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Degree;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class DegreeController extends Controller
{
    /**
     * Display a listing of degrees (jenjang kelas).
     */
    #[OA\Get(
        path: '/degrees',
        summary: 'Daftar Jenjang Kelas (Degrees)',
        description: 'Mengambil seluruh master data jenjang kelas (misal: Kelas 10, Kelas 11, Kelas 12) untuk kebutuhan opsi registrasi atau pembaruan profil.',
        tags: ['Master Data'],
        security: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar jenjang kelas berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Daftar jenjang kelas berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id_degree', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Kelas 10'),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $degrees = Degree::query()
            ->select(['id_degree', 'name'])
            ->orderBy('id_degree')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar jenjang kelas berhasil diambil.',
            'data' => $degrees,
        ]);
    }
}
