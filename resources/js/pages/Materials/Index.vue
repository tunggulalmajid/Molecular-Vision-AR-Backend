<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchBar from '@/Components/SearchBar.vue';
import RefreshButton from '@/Components/RefreshButton.vue';
import DataTable, { type TableHeader } from '@/Components/DataTable.vue';
import Pagination, { type PaginationLink } from '@/Components/Pagination.vue';
import DeleteMaterialModal, {
    type MaterialItem,
} from './DeleteMaterialModal.vue';
import { usePermission } from '@/composables/usePermission';
import {
    Plus,
    Pencil,
    Trash2,
    CheckCircle2,
    AlertCircle,
    Layers,
    BookOpen,
    Calendar,
    Filter,
} from 'lucide-vue-next';

interface CategoryOption {
    id_category: number;
    name: string;
}

interface PaginatedMaterials {
    data: MaterialItem[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
}

const props = defineProps<{
    materials: PaginatedMaterials;
    categories: CategoryOption[];
    filters: {
        search: string;
        category_id: string | number;
    };
}>();

const page = usePage();
const { can } = usePermission();

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

// Filters State
const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category_id || '');
const isRefreshing = ref(false);

// Delete Modal State
const deleteModalOpen = ref(false);
const materialToDelete = ref<MaterialItem | null>(null);

// Table configuration (conditional on permissions)
const tableHeaders = computed<TableHeader[]>(() => {
    const headers: TableHeader[] = [
        { label: 'NAMA MATERI' },
        { label: 'KATEGORI' },
        { label: 'DESKRIPSI' },
        { label: 'TANGGAL' },
    ];

    if (can('materials.edit') || can('materials.delete')) {
        headers.push({ label: 'AKSI', align: 'center', width: '100px' });
    }

    return headers;
});

// Search & Filter handlers
const applyFilters = () => {
    router.get(
        route('materials.index'),
        {
            search: searchQuery.value || undefined,
            category_id: selectedCategory.value || undefined,
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

const handleCategoryChange = () => {
    applyFilters();
};

const handleRefresh = () => {
    isRefreshing.value = true;
    router.reload({
        only: ['materials'],
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
};

const openDeleteModal = (material: MaterialItem) => {
    materialToDelete.value = material;
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

// Date formatter
const formatDate = (dateString?: string): string => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <Head title="Manajemen Materi Pembelajaran" />

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
                        Manajemen Materi Pembelajaran
                    </h1>
                    <p class="mt-1 text-sm text-slate-400">
                        Kelola data materi pembelajaran kimia, modul bacaan, dan
                        media penunjang berbasis 3D AR.
                    </p>
                </div>

                <div v-if="can('materials.create')" class="flex items-center">
                    <Link
                        :href="route('materials.create')"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-4 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#06101c] focus:outline-none"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Tambah Materi</span>
                    </Link>
                </div>
            </div>

            <!-- Toolbar: Search, Category Filter & Refresh -->
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
                            placeholder="Cari judul materi..."
                            @search="handleSearch"
                        />
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="relative w-full sm:w-60">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
                        >
                            <Filter class="h-4 w-4" />
                        </div>
                        <select
                            v-model="selectedCategory"
                            @change="handleCategoryChange"
                            class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] py-2.5 pr-8 pl-9 text-sm text-white focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                        >
                            <option value="">Semua Kategori</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id_category"
                                :value="cat.id_category"
                            >
                                {{ cat.name }}
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
                    :empty="materials.data.length === 0"
                    empty-message="Tidak ada materi pembelajaran ditemukan."
                >
                    <tr
                        v-for="mat in materials.data"
                        :key="mat.id_material"
                        class="transition hover:bg-[#0c1c2e]"
                    >
                        <!-- NAMA MATERI -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="font-semibold text-white">
                                        {{ mat.name }}
                                    </span>
                                    <span
                                        class="ml-2 rounded bg-[#06121f] px-1.5 py-0.5 font-mono text-[10px] text-slate-500"
                                    >
                                        #{{ mat.id_material }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- KATEGORI -->
                        <td class="px-6 py-4">
                            <div
                                class="text-sm font-medium break-all text-slate-300"
                            >
                                {{ mat.category?.name || 'Tanpa Kategori' }}
                            </div>
                        </td>

                        <!-- DESKRIPSI (Truncated with Ellipsis and break-all) -->
                        <td class="max-w-sm px-6 py-4">
                            <p
                                class="text-sm break-all text-slate-300"
                                :title="mat.description || undefined"
                            >
                                {{ truncateText(mat.description, 80) }}
                            </p>
                        </td>

                        <!-- TANGGAL DIBUAT -->
                        <td class="px-6 py-4">
                            <div
                                class="inline-flex items-center gap-1.5 text-xs text-slate-400"
                            >
                                <Calendar class="h-3.5 w-3.5 text-slate-500" />
                                <span>{{ formatDate(mat.created_at) }}</span>
                            </div>
                        </td>

                        <!-- AKSI (Icon only) -->
                        <td
                            v-if="
                                can('materials.edit') || can('materials.delete')
                            "
                            class="px-6 py-4"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Link
                                    v-if="can('materials.edit')"
                                    :href="
                                        route('materials.edit', mat.id_material)
                                    "
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824] focus:outline-none"
                                    title="Edit Materi"
                                    aria-label="Edit Materi"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                                <button
                                    v-if="can('materials.delete')"
                                    @click="openDeleteModal(mat)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-rose-500/50 hover:bg-rose-500/10 hover:text-rose-400 focus:outline-none"
                                    title="Hapus Materi"
                                    aria-label="Hapus Materi"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </DataTable>

                <!-- Pagination Component -->
                <Pagination
                    :links="materials.links"
                    :from="materials.from"
                    :to="materials.to"
                    :total="materials.total"
                    item-name="materi"
                />
            </div>
        </div>

        <!-- Delete Modal Component -->
        <DeleteMaterialModal
            :show="deleteModalOpen"
            :material="materialToDelete"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
