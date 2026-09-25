<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchBar from '@/Components/SearchBar.vue';
import RefreshButton from '@/Components/RefreshButton.vue';
import DataTable, { type TableHeader } from '@/Components/DataTable.vue';
import Pagination, { type PaginationLink } from '@/Components/Pagination.vue';
import TabNav, { type TabItem } from '@/Components/TabNav.vue';
import RoleFormModal, { type RoleItem } from './RoleFormModal.vue';
import RolePermissionsModal from './RolePermissionsModal.vue';
import DeleteRoleModal from './DeleteRoleModal.vue';
import type { PermissionDefinition } from '@/Components/PermissionGroupCard.vue';
import {
    Plus,
    Pencil,
    Trash2,
    ShieldCheck,
    CheckCircle2,
    AlertCircle,
    Users,
    KeyRound,
    Lock,
} from 'lucide-vue-next';

import { usePermission } from '@/composables/usePermission';

interface PaginatedRoles {
    data: RoleItem[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
}

const props = defineProps<{
    roles: PaginatedRoles;
    filters: {
        search: string;
    };
    grouped_permissions: Record<string, PermissionDefinition[]>;
    total_permissions_count: number;
}>();

const page = usePage();
const { can } = usePermission();

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

// Tab Navigation State
const activeTab = ref<'roles' | 'permissions'>('roles');
const tabs = computed<TabItem[]>(() => [
    {
        id: 'roles',
        label: 'Daftar Peran (Roles)',
        icon: Users,
        count: props.roles.total,
    },
    {
        id: 'permissions',
        label: 'Katalog Hak Akses',
        icon: KeyRound,
        count: props.total_permissions_count,
    },
]);

// Search & Refresh State
const searchQuery = ref(props.filters.search || '');
const isRefreshing = ref(false);

// Modal States
const formModalOpen = ref(false);
const permissionsModalOpen = ref(false);
const deleteModalOpen = ref(false);
const selectedRole = ref<RoleItem | null>(null);

// Table configuration (conditional on permissions)
const tableHeaders = computed<TableHeader[]>(() => {
    const headers: TableHeader[] = [
        { label: 'NAMA PERAN' },
        { label: 'GUARD' },
        { label: 'TOTAL PENGGUNA' },
        { label: 'HAK AKSES AKTIF' },
        { label: 'TANGGAL DIBUAT' },
    ];

    if (can('permissions.manage') || can('roles.manage')) {
        headers.push({ label: 'AKSI', align: 'center', width: '130px' });
    }

    return headers;
});

// System role check
const isSystemRole = (name: string): boolean => {
    return ['super admin', 'admin', 'siswa (mobile)'].includes(
        name.toLowerCase(),
    );
};

const isSuperAdmin = (name: string): boolean => {
    return name.toLowerCase() === 'super admin';
};

// Handlers
const handleSearch = (query: string) => {
    router.get(
        route('roles.index'),
        { search: query },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const handleRefresh = () => {
    isRefreshing.value = true;
    router.reload({
        only: ['roles'],
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
};

// Modal Openers
const openCreateModal = () => {
    selectedRole.value = null;
    formModalOpen.value = true;
};

const openEditModal = (role: RoleItem) => {
    selectedRole.value = role;
    formModalOpen.value = true;
};

const openPermissionsModal = (role: RoleItem) => {
    selectedRole.value = role;
    permissionsModalOpen.value = true;
};

const openDeleteModal = (role: RoleItem) => {
    selectedRole.value = role;
    deleteModalOpen.value = true;
};

// Date Formatter
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
    <Head title="Manajemen Peran & Hak Akses" />

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
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">
                        Manajemen Peran & Hak Akses (RBAC)
                    </h1>
                    <p class="mt-1 text-sm text-slate-400">
                        Atur peran pengguna, batasan akses per modul, dan
                        konfigurasi izin otorisasi sistem MVAR.
                    </p>
                </div>

                <div v-if="can('roles.manage')" class="flex items-center">
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-4 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#06101c] focus:outline-none"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Tambah Peran</span>
                    </button>
                </div>
            </div>

            <!-- Tab Navigation & Actions Toolbar -->
            <div
                class="flex flex-col items-start justify-between gap-4 border-b border-[#14263b] pb-4 sm:flex-row sm:items-center"
            >
                <TabNav :tabs="tabs" v-model="activeTab" />

                <div
                    v-if="activeTab === 'roles'"
                    class="flex items-center gap-2 sm:w-auto"
                >
                    <div class="w-full sm:w-64">
                        <SearchBar
                            v-model="searchQuery"
                            placeholder="Cari nama peran..."
                            @search="handleSearch"
                        />
                    </div>
                    <RefreshButton
                        :loading="isRefreshing"
                        @click="handleRefresh"
                    />
                </div>
            </div>

            <!-- TAB 1: DAFTAR PERAN (ROLES) -->
            <div
                v-if="activeTab === 'roles'"
                class="rounded-xl border border-[#14263b] bg-[#091624] shadow-xl"
            >
                <DataTable
                    :headers="tableHeaders"
                    :empty="roles.data.length === 0"
                    empty-message="Tidak ada peran ditemukan."
                >
                    <tr
                        v-for="role in roles.data"
                        :key="role.id"
                        class="transition hover:bg-[#0c1c2e]"
                    >
                        <!-- NAMA PERAN -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="font-semibold text-white capitalize"
                                >
                                    {{ role.name }}
                                </span>
                                <span
                                    v-if="isSuperAdmin(role.name)"
                                    class="rounded bg-[#e5a824]/20 px-2 py-0.5 text-[10px] font-bold tracking-wider text-[#e5a824] uppercase"
                                >
                                    System
                                </span>
                                <span
                                    v-else-if="isSystemRole(role.name)"
                                    class="rounded bg-[#122840] px-2 py-0.5 text-[10px] font-medium text-slate-400"
                                >
                                    Default
                                </span>
                            </div>
                        </td>

                        <!-- GUARD -->
                        <td class="px-6 py-4">
                            <code
                                class="rounded bg-[#06121f] px-2 py-1 font-mono text-xs text-slate-300"
                            >
                                {{ role.guard_name }}
                            </code>
                        </td>

                        <!-- TOTAL PENGGUNA -->
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-slate-300">
                                {{ role.users_count || 0 }} Pengguna
                            </span>
                        </td>

                        <!-- HAK AKSES AKTIF -->
                        <td class="px-6 py-4">
                            <span
                                v-if="isSuperAdmin(role.name)"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#e5a824]"
                            >
                                <ShieldCheck class="h-4 w-4" />
                                <span>Akses Penuh (Bypass)</span>
                            </span>
                            <span
                                v-else
                                class="text-sm font-medium text-slate-300"
                            >
                                {{ role.permissions?.length || 0 }} /
                                {{ total_permissions_count }} Izin
                            </span>
                        </td>

                        <!-- TANGGAL DIBUAT -->
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-400">
                                {{ formatDate(role.created_at) }}
                            </span>
                        </td>

                        <!-- AKSI (Icon only) -->
                        <td
                            v-if="
                                can('permissions.manage') || can('roles.manage')
                            "
                            class="px-6 py-4"
                        >
                            <div
                                class="flex items-center justify-center gap-1.5"
                            >
                                <!-- Configure Permissions Button -->
                                <button
                                    v-if="can('permissions.manage')"
                                    @click="openPermissionsModal(role)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824] focus:outline-none"
                                    title="Konfigurasi Hak Akses"
                                    aria-label="Konfigurasi Hak Akses"
                                >
                                    <ShieldCheck class="h-4 w-4" />
                                </button>

                                <!-- Edit Role Button (Disabled for super admin) -->
                                <template v-if="can('roles.manage')">
                                    <button
                                        v-if="!isSuperAdmin(role.name)"
                                        @click="openEditModal(role)"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824] focus:outline-none"
                                        title="Edit Nama Peran"
                                        aria-label="Edit Nama Peran"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </button>
                                    <span
                                        v-else
                                        class="inline-flex h-8 w-8 cursor-not-allowed items-center justify-center text-slate-600"
                                        title="Peran bawaan sistem terkunci"
                                    >
                                        <Lock class="h-3.5 w-3.5" />
                                    </span>
                                </template>

                                <!-- Delete Role Button (Hidden/Disabled for system roles) -->
                                <button
                                    v-if="
                                        can('roles.manage') &&
                                        !isSystemRole(role.name) &&
                                        (role.users_count || 0) === 0
                                    "
                                    @click="openDeleteModal(role)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-rose-500/50 hover:bg-rose-500/10 hover:text-rose-400 focus:outline-none"
                                    title="Hapus Peran"
                                    aria-label="Hapus Peran"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </DataTable>

                <!-- Pagination Component -->
                <Pagination
                    :links="roles.links"
                    :from="roles.from"
                    :to="roles.to"
                    :total="roles.total"
                    item-name="peran"
                />
            </div>

            <!-- TAB 2: KATALOG MASTER HAK AKSES -->
            <div v-else class="space-y-6">
                <div
                    class="rounded-xl border border-[#14263b] bg-[#091624] p-5 shadow-xl"
                >
                    <div class="mb-4">
                        <h2 class="text-base font-bold text-white">
                            Katalog Master Hak Akses (Permissions)
                        </h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Berikut adalah seluruh permission terstandar yang
                            terdaftar di sistem MVAR, dikelompokkan berdasarkan
                            modul fungsional.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(perms, moduleName) in grouped_permissions"
                            :key="moduleName"
                            class="overflow-hidden rounded-xl border border-[#14263b] bg-[#0c1c2e]"
                        >
                            <div
                                class="border-b border-[#14263b] bg-[#081523] px-4 py-3"
                            >
                                <span
                                    class="text-sm font-bold tracking-wide text-white"
                                >
                                    {{ moduleName }}
                                </span>
                            </div>

                            <div
                                class="grid grid-cols-1 divide-y divide-[#13253b] sm:grid-cols-2 sm:divide-x sm:divide-y-0 sm:divide-[#13253b]"
                            >
                                <div
                                    v-for="p in perms"
                                    :key="p.name"
                                    class="flex items-start gap-3 p-4"
                                >
                                    <div
                                        class="rounded-lg bg-[#14283f] p-2 text-[#e5a824]"
                                    >
                                        <KeyRound class="h-4 w-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="flex items-center justify-between gap-2"
                                        >
                                            <p
                                                class="text-xs font-semibold text-white"
                                            >
                                                {{ p.label }}
                                            </p>
                                            <code
                                                class="rounded bg-[#071320] px-1.5 py-0.5 font-mono text-[10px] text-slate-400"
                                            >
                                                {{ p.name }}
                                            </code>
                                        </div>
                                        <p
                                            v-if="p.description"
                                            class="mt-1 text-[11px] text-slate-400"
                                        >
                                            {{ p.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Form Modal (Create / Edit Name) -->
        <RoleFormModal
            :show="formModalOpen"
            :role="selectedRole"
            @close="formModalOpen = false"
        />

        <!-- Role Permissions Matrix Modal -->
        <RolePermissionsModal
            :show="permissionsModalOpen"
            :role="selectedRole"
            :grouped-permissions="grouped_permissions"
            :total-permissions-count="total_permissions_count"
            @close="permissionsModalOpen = false"
        />

        <!-- Delete Role Modal -->
        <DeleteRoleModal
            :show="deleteModalOpen"
            :role="selectedRole"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
