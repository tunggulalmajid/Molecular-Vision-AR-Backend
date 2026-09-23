<?php

namespace Database\Seeders;

use App\Models\Degree;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DegreeSeeder::class,
            CategorySeeder::class,
            MoleculeSeeder::class,
            MaterialSeeder::class,
            QuestionSeeder::class,
        ]);

        $superAdminRole = Role::where('name', 'super admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $siswaRole = Role::where('name', 'siswa (mobile)')->first();
        $degreeKelas10 = Degree::where('name', 'Kelas 10')->first();

        // 1. Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mvar.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'id_role' => $superAdminRole?->id,
                'school' => 'Kemendikbudristek',
                'email_verified_at' => now(),
            ],
        );
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@mvar.id'],
            [
                'name' => 'Admin Guru',
                'password' => Hash::make('password'),
                'id_role' => $adminRole?->id,
                'school' => 'SMA Negeri 1 Jakarta',
                'email_verified_at' => now(),
            ],
        );
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // 3. Siswa Mobile User
        $siswa = User::firstOrCreate(
            ['email' => 'siswa@mvar.id'],
            [
                'name' => 'Siswa MVAR',
                'password' => Hash::make('password'),
                'id_role' => $siswaRole?->id,
                'id_degree' => $degreeKelas10?->id_degree,
                'school' => 'SMA Negeri 1 Jakarta',
                'email_verified_at' => now(),
            ],
        );
        if ($siswaRole) {
            $siswa->assignRole($siswaRole);
        }

        // 4. Test User (for standard Laravel test suites)
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'id_role' => $siswaRole?->id,
                'id_degree' => $degreeKelas10?->id_degree,
                'school' => 'SMA Test',
                'email_verified_at' => now(),
            ],
        );
        if ($siswaRole) {
            $testUser->assignRole($siswaRole);
        }
    }
}
