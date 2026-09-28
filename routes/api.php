<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DegreeController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\MoleculeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProgressController;
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

    // Modul Progres Belajar (Materi, Molekul 3D, Kuis Kategori)
    Route::get('/progress', [ProgressController::class, 'index'])->name('api.progress.index');
    Route::post('/progress/material', [ProgressController::class, 'storeMaterial'])->name('api.progress.material.store');
    Route::post('/progress/materials', [ProgressController::class, 'storeMaterial'])->name('api.progress.materials.store');
    Route::post('/progress/molecule', [ProgressController::class, 'storeMolecule'])->name('api.progress.molecule.store');
    Route::post('/progress/molecules', [ProgressController::class, 'storeMolecule'])->name('api.progress.molecules.store');
    Route::post('/progress/category', [ProgressController::class, 'storeCategory'])->name('api.progress.category.store');
    Route::post('/progress/categories', [ProgressController::class, 'storeCategory'])->name('api.progress.categories.store');

    // Modul Latihan Soal & Kuis (Exercise)
    Route::get('/exercises', [ExerciseController::class, 'index'])->name('api.exercises.index');
    Route::get('/exercise', [ExerciseController::class, 'index'])->name('api.exercise.index');
    Route::get('/exercises/{id_category}', [ExerciseController::class, 'show'])->name('api.exercises.show');
    Route::get('/exercise/{id_category}', [ExerciseController::class, 'show'])->name('api.exercise.show');
    Route::post('/exercises/{id_category}', [ExerciseController::class, 'store'])->name('api.exercises.store');
    Route::post('/exercise/{id_category}', [ExerciseController::class, 'store'])->name('api.exercise.store');
    Route::get('/exercises/{id_category}/result', [ExerciseController::class, 'result'])->name('api.exercises.result');
    Route::get('/exercise/{id_category}/result', [ExerciseController::class, 'result'])->name('api.exercise.result');
});
