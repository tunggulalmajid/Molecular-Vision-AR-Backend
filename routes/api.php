<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DegreeController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\MoleculeController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned to the "api"
| middleware group. Enjoy building your API!
|
*/

// Endpoint: Auth (Publik)
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.refresh');

// Endpoint: Master Data (Publik)
Route::get('/degrees', [DegreeController::class, 'index'])->name('api.degrees.index');

// Endpoint Terproteksi (Memerlukan Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profil
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::delete('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'me'])->name('api.user');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('api.profile.password.update');
    Route::put('/profile', [ProfileController::class, 'update'])->name('api.profile.update');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('api.profile.update.alias');

    // Pembelajaran & Kategori
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');

    // Modul Molekul 3D
    Route::get('/molecules', [MoleculeController::class, 'index'])->name('api.molecules.index');
    Route::get('/molecules/{id}', [MoleculeController::class, 'show'])->name('api.molecules.show');

    // Modul Materi Pembelajaran
    Route::get('/materials', [MaterialController::class, 'index'])->name('api.materials.index');
    Route::get('/materials/{id}', [MaterialController::class, 'show'])->name('api.materials.show');
});
