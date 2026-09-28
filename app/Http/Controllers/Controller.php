<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Molecular Vision AR (MVAR) API Documentation',
    description: 'Dokumentasi RESTful API untuk Molecular Vision AR (MVAR) Backend. API ini digunakan oleh aplikasi mobile (Flutter / Unity) dan client lainnya.',
    contact: new OA\Contact(
        name: 'Tim Pengembang MVAR',
        email: 'dev@mvar.local'
    )
)]
#[OA\Server(
    url: '/api',
    description: 'Current API Server'
)]
#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'Local Development Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    bearerFormat: 'JWT',
    scheme: 'bearer',
    description: 'Masukkan Sanctum Token dengan format: Bearer <token>'
)]
abstract class Controller
{
    //
}
