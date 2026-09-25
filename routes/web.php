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

    // Modul User
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::post('users', [\App\Http\Controllers\UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::put('users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
    Route::delete('users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');

    // Modul RBAC & Hak Akses
    Route::get('roles', [\App\Http\Controllers\RoleController::class, 'index'])->middleware('permission:roles.view')->name('roles.index');
    Route::post('roles', [\App\Http\Controllers\RoleController::class, 'store'])->middleware('permission:roles.manage')->name('roles.store');
    Route::put('roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->middleware('permission:roles.manage')->name('roles.update');
    Route::delete('roles/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->middleware('permission:roles.manage')->name('roles.destroy');
    Route::put('roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'syncPermissions'])->middleware('permission:permissions.manage')->name('roles.permissions.update');
});

require __DIR__.'/auth.php';
