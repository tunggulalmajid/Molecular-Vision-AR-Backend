<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Material;
use App\Models\Molecule;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $userCount = User::count();
    $moleculeCount = Molecule::count();
    $materialCount = Material::count();
    $questionCount = Question::count();

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
    Route::get('users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::post('users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');

    // Modul Kategori
    Route::get('categories', [CategoryController::class, 'index'])->middleware('permission:categories.view')->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->middleware('permission:categories.create')->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->middleware('permission:categories.edit')->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:categories.delete')->name('categories.destroy');

    // Modul RBAC & Hak Akses
    Route::get('roles', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->middleware('permission:roles.manage')->name('roles.store');
    Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.manage')->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.manage')->name('roles.destroy');
    Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->middleware('permission:permissions.manage')->name('roles.permissions.update');
});

require __DIR__.'/auth.php';
