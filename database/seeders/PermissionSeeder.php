<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByModule = [
            'Modul User' => [
                'users.view' => 'Lihat Data Pengguna',
                'users.create' => 'Tambah Pengguna Baru',
                'users.edit' => 'Ubah Data Pengguna',
                'users.delete' => 'Hapus Pengguna',
            ],
            'Modul Molekul' => [
                'molecules.view' => 'Lihat Katalog Molekul',
                'molecules.create' => 'Tambah Molekul Baru',
                'molecules.edit' => 'Ubah Data Molekul',
                'molecules.delete' => 'Hapus Molekul',
            ],
            'Modul Materi' => [
                'materials.view' => 'Lihat Daftar Materi',
                'materials.create' => 'Tambah Materi Pembelajaran',
                'materials.edit' => 'Ubah Konten Materi',
                'materials.delete' => 'Hapus Materi',
            ],
            'Modul Bank Soal & Kuis' => [
                'questions.view' => 'Lihat Bank Soal',
                'questions.create' => 'Buat Soal Baru',
                'questions.edit' => 'Ubah Soal & Jawaban',
                'questions.delete' => 'Hapus Soal',
            ],
            'Modul RBAC & Akses' => [
                'roles.view' => 'Lihat Daftar Peran',
                'roles.manage' => 'Kelola Peran Pengguna',
                'permissions.manage' => 'Ubah Matriks Hak Akses',
            ],
        ];

        $allPermissionNames = [];

        foreach ($permissionsByModule as $moduleName => $permissions) {
            foreach ($permissions as $name => $label) {
                $allPermissionNames[] = $name;
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web']
                );
            }
        }

        // Assign all permissions to super admin
        $superAdminRole = Role::where('name', 'super admin')->where('guard_name', 'web')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions($allPermissionNames);
        }

        // Assign standard content permissions to admin
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->syncPermissions([
                'users.view',
                'molecules.view',
                'molecules.create',
                'molecules.edit',
                'molecules.delete',
                'materials.view',
                'materials.create',
                'materials.edit',
                'materials.delete',
                'questions.view',
                'questions.create',
                'questions.edit',
                'questions.delete',
                'roles.view',
            ]);
        }
    }
}
