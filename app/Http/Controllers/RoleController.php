<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Master module groupings with descriptive labels.
     */
    private array $modulePermissions = [
        'Modul User' => [
            ['name' => 'users.view', 'label' => 'Lihat Data Pengguna', 'description' => 'Melihat daftar dan detail profil pengguna'],
            ['name' => 'users.create', 'label' => 'Tambah Pengguna Baru', 'description' => 'Membuat akun pengguna dan memilih peran'],
            ['name' => 'users.edit', 'label' => 'Ubah Data Pengguna', 'description' => 'Mengubah data nama, email, sekolah, dan role'],
            ['name' => 'users.delete', 'label' => 'Hapus Pengguna', 'description' => 'Menghapus akun pengguna dari sistem'],
        ],
        'Modul Molekul' => [
            ['name' => 'molecules.view', 'label' => 'Lihat Katalog Molekul', 'description' => 'Melihat daftar molekul kimia dan visualisasi 3D'],
            ['name' => 'molecules.create', 'label' => 'Tambah Molekul Baru', 'description' => 'Mengunggah struktur molekul kimia baru'],
            ['name' => 'molecules.edit', 'label' => 'Ubah Data Molekul', 'description' => 'Mengubah deskripsi dan parameter molekul'],
            ['name' => 'molecules.delete', 'label' => 'Hapus Molekul', 'description' => 'Menghapus data molekul dari katalog'],
        ],
        'Modul Materi' => [
            ['name' => 'materials.view', 'label' => 'Lihat Daftar Materi', 'description' => 'Mengakses modul materi pembelajaran kimia'],
            ['name' => 'materials.create', 'label' => 'Tambah Materi Pembelajaran', 'description' => 'Menambahkan bab atau materi ajar baru'],
            ['name' => 'materials.edit', 'label' => 'Ubah Konten Materi', 'description' => 'Memperbarui ringkasan atau isi materi'],
            ['name' => 'materials.delete', 'label' => 'Hapus Materi', 'description' => 'Menghapus materi pembelajaran dari platform'],
        ],
        'Modul Bank Soal & Kuis' => [
            ['name' => 'questions.view', 'label' => 'Lihat Bank Soal', 'description' => 'Melihat inventaris soal kuis dan latihan'],
            ['name' => 'questions.create', 'label' => 'Buat Soal Baru', 'description' => 'Menambahkan butir soal dan opsi jawaban'],
            ['name' => 'questions.edit', 'label' => 'Ubah Soal & Jawaban', 'description' => 'Mengedit redaksi soal dan kunci jawaban'],
            ['name' => 'questions.delete', 'label' => 'Hapus Soal', 'description' => 'Menghapus butir soal dari bank soal'],
        ],
        'Modul RBAC & Akses' => [
            ['name' => 'roles.view', 'label' => 'Lihat Daftar Peran', 'description' => 'Melihat daftar peran dan hak akses pengguna'],
            ['name' => 'roles.manage', 'label' => 'Kelola Peran Pengguna', 'description' => 'Membuat, mengubah, atau menghapus peran'],
            ['name' => 'permissions.manage', 'label' => 'Ubah Matriks Hak Akses', 'description' => 'Mengonfigurasi izin akses per peran'],
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $roles = Role::query()
            ->with(['permissions:id,name'])
            ->withCount('users')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $totalPermissions = Permission::count();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $search ?? '',
            ],
            'grouped_permissions' => $this->modulePermissions,
            'total_permissions_count' => $totalPermissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
        ]);

        Role::create([
            'name' => strtolower(trim($validated['name'])),
            'guard_name' => 'web',
        ]);

        return redirect()->route('roles.index')->with('success', 'Peran baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'super admin') {
            return redirect()->route('roles.index')->with('error', 'Peran Super Admin tidak dapat diubah namanya.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name,' . $role->id],
        ]);

        $role->update([
            'name' => strtolower(trim($validated['name'])),
        ]);

        return redirect()->route('roles.index')->with('success', 'Nama peran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $systemRoles = ['super admin', 'admin', 'siswa (mobile)'];
        if (in_array(strtolower($role->name), $systemRoles)) {
            return redirect()->route('roles.index')->with('error', 'Peran inti sistem tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Peran tidak dapat dihapus karena masih digunakan oleh ' . $role->users()->count() . ' pengguna aktif.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Peran berhasil dihapus.');
    }

    /**
     * Synchronize permissions for a specific role.
     */
    public function syncPermissions(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'super admin') {
            return redirect()->route('roles.index')->with('error', 'Peran Super Admin secara bawaan memiliki seluruh hak akses sistem.');
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Hak akses untuk peran "' . $role->name . '" berhasil diperbarui.');
    }
}
