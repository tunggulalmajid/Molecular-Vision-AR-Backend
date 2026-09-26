<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchBar from '@/Components/SearchBar.vue';
import RefreshButton from '@/Components/RefreshButton.vue';
import DataTable, { type TableHeader } from '@/Components/DataTable.vue';
import Pagination, { type PaginationLink } from '@/Components/Pagination.vue';
import DeleteMoleculeModal, {
    type MoleculeItem,
} from './DeleteMoleculeModal.vue';
import { usePermission } from '@/composables/usePermission';
import {
    Plus,
    Pencil,
    Trash2,
    CheckCircle2,
    AlertCircle,
    Atom,
    Box,
    ExternalLink,
    Filter,
} from 'lucide-vue-next';

interface PaginatedMolecules {
    data: MoleculeItem[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
}

const props = defineProps<{
    molecules: PaginatedMolecules;
    availableShapes: string[];
    filters: {
        search: string;
        shape: string;
    };
}>();

const page = usePage();
const { can } = usePermission();

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

// Filters State
const searchQuery = ref(props.filters.search || '');
const selectedShape = ref(props.filters.shape || '');
const isRefreshing = ref(false);

// Delete Modal State
const deleteModalOpen = ref(false);
const moleculeToDelete = ref<MoleculeItem | null>(null);

// Table configuration
const tableHeaders = computed<TableHeader[]>(() => {
    const headers: TableHeader[] = [
        { label: 'NAMA MOLEKUL', width: '220px' },
        { label: 'BENTUK GEOMETRI', width: '180px' },
        { label: 'SUDUT & IKATAN', width: '180px' },
        { label: 'MODEL 3D S3', width: '160px' },
        { label: 'DESKRIPSI', width: '260px' },
    ];

    if (can('molecules.edit') || can('molecules.delete')) {
        headers.push({ label: 'AKSI', align: 'center', width: '100px' });
    }

    return headers;
});

// Search & Filter handlers
const applyFilters = () => {
    router.get(
        route('molecules.index'),
        {
            search: searchQuery.value || undefined,
            shape: selectedShape.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const handleSearch = (query: string) => {
    searchQuery.value = query;
    applyFilters();
};

const handleShapeChange = () => {
    applyFilters();
};

const handleRefresh = () => {
    isRefreshing.value = true;
    router.reload({
        only: ['molecules'],
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
};

const openDeleteModal = (molecule: MoleculeItem) => {
    moleculeToDelete.value = molecule;
    deleteModalOpen.value = true;
};

// Truncate helper for long descriptions with ellipsis
const truncateText = (
    text: string | null | undefined,
    maxLength = 80,
): string => {
    if (!text) return '-';
    return text.length > maxLength
        ? text.substring(0, maxLength) + '...'
        : text;
};
</script>

<template>
    <Head title="Manajemen Katalog Molekul" />

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
                        Manajemen Katalog Molekul
                    </h1>
                    <p class="mt-1 text-sm text-slate-400">
                        Kelola data struktur geometri molekul, rumus kimia, dan
                        berkas 3D AR (.glb) di Cloud Object Storage.
                    </p>
                </div>

                <div v-if="can('molecules.create')" class="flex items-center">
                    <Link
                        :href="route('molecules.create')"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-4 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#06101c] focus:outline-none"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Tambah Molekul</span>
                    </Link>
                </div>
            </div>

            <!-- Toolbar: Search, Shape Filter & Refresh (Persis dengan Modul Materi) -->
            <div
                class="flex flex-col items-stretch justify-between gap-3 sm:flex-row sm:items-center"
            >
                <div
                    class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center"
                >
                    <!-- Search Input -->
                    <div class="w-full sm:max-w-xs">
                        <SearchBar
                            v-model="searchQuery"
                            placeholder="Cari nama, rumus molekul..."
                            @search="handleSearch"
                        />
                    </div>

                    <!-- Shape Filter Dropdown -->
                    <div
                        v-if="availableShapes.length > 0"
                        class="relative w-full sm:w-60"
                    >
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
                        >
                            <Filter class="h-4 w-4" />
                        </div>
                        <select
                            v-model="selectedShape"
                            @change="handleShapeChange"
                            class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] py-2.5 pr-8 pl-9 text-sm text-white focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                        >
                            <option value="">Semua Bentuk</option>
                            <option
                                v-for="shape in availableShapes"
                                :key="shape"
                                :value="shape"
                            >
                                {{ shape }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <RefreshButton
                        :loading="isRefreshing"
                        @click="handleRefresh"
                    />
                </div>
            </div>

            <!-- Data Table Card -->
            <div
                class="rounded-xl border border-[#14263b] bg-[#091624] shadow-xl"
            >
                <DataTable
                    :headers="tableHeaders"
                    :empty="molecules.data.length === 0"
                    empty-message="Tidak ada model molekul ditemukan."
                >
                    <tr
                        v-for="mol in molecules.data"
                        :key="mol.id_molecule"
                        class="transition hover:bg-[#0c1c2e]"
                    >
                        <!-- NAMA MOLEKUL & RUMUS -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-white">
                                            {{ mol.name }}
                                        </span>
                                        <span
                                            class="rounded bg-[#06121f] px-1.5 py-0.5 font-mono text-[10px] text-slate-500"
                                        >
                                            #{{ mol.id_molecule }}
                                        </span>
                                    </div>
                                    <span
                                        class="mt-1 inline-block rounded-md border border-[#e5a824]/20 bg-[#e5a824]/10 px-2 py-0.5 font-mono text-[11px] font-bold text-[#e5a824]"
                                    >
                                        {{ mol.formula }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- BENTUK GEOMETRI -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-flex items-center rounded-lg border border-[#1b344d] bg-[#0c1a28] px-2.5 py-1 text-xs font-medium text-slate-200"
                            >
                                {{ mol.shape }}
                            </span>
                        </td>

                        <!-- SUDUT & IKATAN -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-semibold text-white">
                                {{ mol.bent }}
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ mol.bond_type }}
                            </p>
                        </td>

                        <!-- MODEL 3D S3 -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a
                                v-if="mol.model_3d_url"
                                :href="mol.model_3d_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-400 transition hover:bg-emerald-500/20"
                                title="Unduh / Lihat Berkas 3D S3"
                            >
                                <Box class="h-3.5 w-3.5" />
                                <span>Berkas 3D</span>
                                <ExternalLink class="h-3 w-3 opacity-70" />
                            </a>
                            <span v-else class="text-xs text-slate-500">-</span>
                        </td>

                        <!-- DESKRIPSI -->
                        <td class="max-w-sm px-6 py-4">
                            <p
                                class="text-sm break-words text-slate-300"
                                :title="mol.description || undefined"
                            >
                                {{ truncateText(mol.description, 80) }}
                            </p>
                        </td>

                        <!-- AKSI (Icon only persis Modul Materi) -->
                        <td
                            v-if="
                                can('molecules.edit') || can('molecules.delete')
                            "
                            class="px-6 py-4 whitespace-nowrap"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Link
                                    v-if="can('molecules.edit')"
                                    :href="
                                        route('molecules.edit', mol.id_molecule)
                                    "
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824] focus:outline-none"
                                    title="Edit Molekul"
                                    aria-label="Edit Molekul"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                                <button
                                    v-if="can('molecules.delete')"
                                    @click="openDeleteModal(mol)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-rose-500/50 hover:bg-rose-500/10 hover:text-rose-400 focus:outline-none"
                                    title="Hapus Molekul"
                                    aria-label="Hapus Molekul"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </DataTable>

                <!-- Pagination Component -->
                <Pagination
                    :links="molecules.links"
                    :from="molecules.from"
                    :to="molecules.to"
                    :total="molecules.total"
                    item-name="molekul"
                />
            </div>

            <!-- Delete Confirmation Modal -->
            <DeleteMoleculeModal
                :show="deleteModalOpen"
                :molecule="moleculeToDelete"
                @close="deleteModalOpen = false"
            />
        </div>
    </AuthenticatedLayout>
</template>
