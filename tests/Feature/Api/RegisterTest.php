<?php

use App\Models\Degree;
use App\Models\User;
use Database\Seeders\DegreeSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(DegreeSeeder::class);
});

test('api user can register successfully as a student', function () {
    $degree = Degree::first();

    $payload = [
        'name' => 'Budi Santoso',
        'email' => 'budi.santoso@example.com',
        'password' => 'password123',
        'school' => 'SMA Negeri 1 Jakarta',
        'id_degree' => $degree?->id_degree,
    ];

    $response = $this->postJson(route('api.register'), $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => [
                    'id_user',
                    'name',
                    'email',
                    'school',
                    'id_degree',
                    'id_role',
                ],
                'token',
                'token_type',
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'user' => [
                    'name' => 'Budi Santoso',
                    'email' => 'budi.santoso@example.com',
                    'school' => 'SMA Negeri 1 Jakarta',
                ],
                'token_type' => 'Bearer',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'budi.santoso@example.com',
        'name' => 'Budi Santoso',
    ]);

    $user = User::where('email', 'budi.santoso@example.com')->first();
    expect($user->hasRole('siswa (mobile)'))->toBeTrue();
});

test('api registration validates required fields and unique email', function () {
    User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $response = $this->postJson(route('api.register'), [
        'name' => '',
        'email' => 'existing@example.com',
        'password' => 'short',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});
