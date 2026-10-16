<?php

use App\Models\User;
use Database\Seeders\DegreeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(DegreeSeeder::class);
});

test('student with role id 3 can login successfully', function () {
    $studentRole = Role::find(User::ROLE_SISWA);

    $user = User::factory()->create([
        'email' => 'student@example.com',
        'password' => Hash::make('password123'),
        'id_role' => User::ROLE_SISWA,
    ]);
    if ($studentRole) {
        $user->assignRole($studentRole);
    }

    $response = $this->postJson(route('api.login'), [
        'email' => 'student@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Login berhasil.',
        ])
        ->assertJsonStructure([
            'data' => [
                'user' => ['id_user', 'name', 'email', 'id_role'],
                'token',
                'token_type',
            ],
        ]);
});

test('superadmin with role id 1 is rejected from mobile login', function () {
    $superAdminRole = Role::find(User::ROLE_SUPER_ADMIN);

    $superAdmin = User::factory()->create([
        'email' => 'superadmin@example.com',
        'password' => Hash::make('password123'),
        'id_role' => User::ROLE_SUPER_ADMIN,
    ]);
    if ($superAdminRole) {
        $superAdmin->assignRole($superAdminRole);
    }

    $response = $this->postJson(route('api.login'), [
        'email' => 'superadmin@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'message' => 'Akses ditolak. Akun ini tidak diizinkan login melalui aplikasi mobile.',
        ]);
});

test('admin with role id 2 is rejected from mobile login', function () {
    $adminRole = Role::find(User::ROLE_ADMIN);

    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'id_role' => User::ROLE_ADMIN,
    ]);
    if ($adminRole) {
        $admin->assignRole($adminRole);
    }

    $response = $this->postJson(route('api.login'), [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'message' => 'Akses ditolak. Akun ini tidak diizinkan login melalui aplikasi mobile.',
        ]);
});

test('student can still login even if role name was changed', function () {
    // Rename role ID 3 to something else
    $studentRole = Role::find(User::ROLE_SISWA);
    $studentRole?->update(['name' => 'pelajar kimia']);

    $user = User::factory()->create([
        'email' => 'pelajar@example.com',
        'password' => Hash::make('password123'),
        'id_role' => User::ROLE_SISWA,
    ]);

    $response = $this->postJson(route('api.login'), [
        'email' => 'pelajar@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});
