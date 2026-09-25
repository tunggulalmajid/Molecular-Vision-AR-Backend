<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * Handle incoming API registration for users (mobile/siswa).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default()],
            'school' => ['nullable', 'string', 'max:255'],
            'id_degree' => ['nullable', 'integer', 'exists:degrees,id_degree'],
        ];

        if ($request->has('password_confirmation')) {
            $rules['password'][] = 'confirmed';
        }

        $validated = $request->validate($rules);

        $user = DB::transaction(function () use ($validated) {
            $siswaRole = Role::where('name', 'siswa (mobile)')->first();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'school' => $validated['school'] ?? null,
                'id_degree' => $validated['id_degree'] ?? null,
                'id_role' => $siswaRole?->id,
            ]);

            if ($siswaRole) {
                $user->assignRole($siswaRole);
            }

            return $user;
        });

        $token = $user->createToken('mobile_auth_token')->plainTextToken;

        $user->load(['role:id,name', 'degree:id_degree,name']);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }
}
