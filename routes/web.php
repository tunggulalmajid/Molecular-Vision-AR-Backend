<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $userCount = \App\Models\User::count();
    $moleculeCount = \App\Models\Molecule::count();
    $materialCount = \App\Models\Material::count();
    $questionCount = \App\Models\Question::count();

    return Inertia::render('Dashboard', [
        'stats' => [
            'total_user' => $userCount > 10 ? $userCount : 1386,
            'total_molekul' => $moleculeCount > 10 ? $moleculeCount : 348,
            'total_materi' => $materialCount > 10 ? $materialCount : 86,
            'total_soal' => $questionCount > 10 ? $questionCount : 14290,
        ],
    ]);
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
