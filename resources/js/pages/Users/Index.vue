<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchBar from '@/Components/SearchBar.vue';
import RefreshButton from '@/Components/RefreshButton.vue';
import DataTable, { type TableHeader } from '@/Components/DataTable.vue';
import Pagination, { type PaginationLink } from '@/Components/Pagination.vue';
import UserFormModal, { type UserItem } from './UserFormModal.vue';
import DeleteUserModal from './DeleteUserModal.vue';
import { Plus, Pencil, Trash2, CheckCircle2, AlertCircle } from 'lucide-vue-next';

import { usePermission } from '@/composables/usePermission';

interface PaginatedUsers {
    data: (UserItem & { created_at: string })[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
}

const props = defineProps<{
    users: PaginatedUsers;
    filters: {
        search: string;
    };
    roles: string[];
}>();

const page = usePage();
const { can } = usePermission();

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

// State
const searchQuery = ref(props.filters.search || '');
const isRefreshing = ref(false);

// Modal state
const formModalOpen = ref(false);
const deleteModalOpen = ref(false);
const selectedUser = ref<UserItem | null>(null);

// Table configuration (conditional on edit/delete permissions)
const tableHeaders = computed<TableHeader[]>(() => {
    const headers: TableHeader[] = [
        { label: 'PENGGUNA' },
        { label: 'EMAIL' },
        { label: 'SEKOLAH / INSTITUSI' },
        { label: 'ROLE' },
        { label: 'TANGGAL DAFTAR' },
    ];

    if (can('users.edit') || can('users.delete')) {
        headers.push({ label: 'AKSI', align: 'center', width: '100px' });
    }

    return headers;
});

// Search & Refresh handlers
const handleSearch = (query: string) => {
    router.get(
        route('users.index'),
        { search: query },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const handleRefresh = () => {
    isRefreshing.value = true;
    router.reload({
        only: ['users'],
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
};

// Modal Openers
const openCreateModal = () => {
    selectedUser.value = null;
    formModalOpen.value = true;
};

const openEditModal = (user: UserItem) => {
    selectedUser.value = user;
    formModalOpen.value = true;
};

const openDeleteModal = (user: UserItem) => {
    selectedUser.value = user;
    deleteModalOpen.value = true;
};

// Helpers
const getInitials = (name: string): string => {
    if (!name) return 'U';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
};

const formatDate = (dateString?: string): string => {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(date);
    } catch {
        return dateString;
    }
};
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Flash Message Banner -->
            <div
                v-if="flashSuccess"
                class="flex items-center gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
            >
                <CheckCircle2 class="h-5 w-5 flex-shrink-0 text-emerald-400" />
                <p>{{ flashSuccess }}</p>
            </div>
            <div
                v-if="flashError"
                class="flex items-center gap-3 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-400"
            >
                <AlertCircle class="h-5 w-5 flex-shrink-0 text-rose-400" />
                <p>{{ flashError }}</p>
            </div>

            <!-- Page Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">
                        Manajemen Pengguna
                    </h1>
                    <p class="mt-1 text-sm text-slate-400">
                        Kelola data seluruh pengguna, hak akses peran, serta akun institusi yang terdaftar di platform MVAR.
                    </p>
                </div>

                <div v-if="can('users.create')" class="flex items-center">
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-4 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:outline-none focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#06101c]"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Tambah Pengguna</span>
                    </button>
                </div>
            </div>

            <!-- Toolbar: Search & Refresh -->
            <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                <div class="w-full sm:max-w-md">
                    <SearchBar
                        v-model="searchQuery"
                        placeholder="Cari berdasarkan nama, email, sekolah..."
                        @search="handleSearch"
                    />
                </div>

                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <RefreshButton
                        :loading="isRefreshing"
                        @click="handleRefresh"
                    />
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="rounded-xl border border-[#14263b] bg-[#091624] shadow-xl">
                <DataTable
                    :headers="tableHeaders"
                    :empty="users.data.length === 0"
                    empty-message="Tidak ada data pengguna ditemukan."
                >
                    <tr
                        v-for="user in users.data"
                        :key="user.id_user"
                        class="transition hover:bg-[#0c1c2e]"
                    >
                        <!-- PENGGUNA -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border border-[#1f3e61] bg-[#102438] text-xs font-bold text-[#e5a824]"
                                >
                                    {{ getInitials(user.name) }}
                                </div>
                                <div>
                                    <div class="font-medium text-white">
                                        {{ user.name }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        ID: #{{ user.id_user }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- EMAIL -->
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-300">
                                {{ user.email }}
                            </span>
                        </td>

                        <!-- SEKOLAH / INSTITUSI -->
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-300">
                                {{ user.school || '-' }}
                            </span>
                        </td>

                        <!-- ROLE (Plain text, no badge) -->
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-slate-300 capitalize">
                                {{ user.roles && user.roles.length > 0 ? user.roles[0].name : '-' }}
                            </span>
                        </td>

                        <!-- TANGGAL DAFTAR -->
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-400">
                                {{ formatDate(user.created_at) }}
                            </span>
                        </td>

                        <!-- AKSI (Icon only) -->
                        <td v-if="can('users.edit') || can('users.delete')" class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    v-if="can('users.edit')"
                                    @click="openEditModal(user)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824] focus:outline-none"
                                    title="Edit Pengguna"
                                    aria-label="Edit Pengguna"
                                >
                                    <Pencil class="h-4 w-4" />
                                </button>
                                <button
                                    v-if="can('users.delete')"
                                    @click="openDeleteModal(user)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-rose-500/50 hover:bg-rose-500/10 hover:text-rose-400 focus:outline-none"
                                    title="Hapus Pengguna"
                                    aria-label="Hapus Pengguna"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </DataTable>

                <!-- Pagination Component -->
                <Pagination
                    :links="users.links"
                    :from="users.from"
                    :to="users.to"
                    :total="users.total"
                    item-name="pengguna"
                />
            </div>
        </div>

        <!-- Create / Edit User Modal -->
        <UserFormModal
            :show="formModalOpen"
            :user="selectedUser"
            :roles="roles"
            @close="formModalOpen = false"
        />

        <!-- Delete Confirmation Modal -->
        <DeleteUserModal
            :show="deleteModalOpen"
            :user="selectedUser"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
