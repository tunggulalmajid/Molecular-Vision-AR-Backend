<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchBar from '@/Components/SearchBar.vue';
import RefreshButton from '@/Components/RefreshButton.vue';
import DataTable, { type TableHeader } from '@/Components/DataTable.vue';
import Pagination, { type PaginationLink } from '@/Components/Pagination.vue';
import CategoryFormModal, { type CategoryItem } from './CategoryFormModal.vue';
import DeleteCategoryModal from './DeleteCategoryModal.vue';
import { usePermission } from '@/composables/usePermission';
import {
    Plus,
    Pencil,
    Trash2,
    CheckCircle2,
    AlertCircle,
    Layers,
    BookOpen,
    FileQuestion,
} from 'lucide-vue-next';

interface PaginatedCategories {
    data: CategoryItem[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
}

const props = defineProps<{
    categories: PaginatedCategories;
    filters: {
        search: string;
    };
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
const selectedCategory = ref<CategoryItem | null>(null);

// Table configuration (conditional on permissions)
const tableHeaders = computed<TableHeader[]>(() => {
    const headers: TableHeader[] = [
        { label: 'NAMA KATEGORI', width: '200px' },
        { label: 'DESKRIPSI', width: '280px' },
        { label: 'TOTAL MATERI', width: '130px' },
        { label: 'TOTAL SOAL', width: '120px' },
    ];

    if (can('categories.edit') || can('categories.delete')) {
        headers.push({ label: 'AKSI', align: 'center', width: '100px' });
    }

    return headers;
});

// Search & Refresh handlers
const handleSearch = (query: string) => {
    router.get(
        route('categories.index'),
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
        only: ['categories'],
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
};

// Modal Openers
const openCreateModal = () => {
    selectedCategory.value = null;
    formModalOpen.value = true;
};

const openEditModal = (category: CategoryItem) => {
    selectedCategory.value = category;
    formModalOpen.value = true;
};

const openDeleteModal = (category: CategoryItem) => {
    selectedCategory.value = category;
    deleteModalOpen.value = true;
};

// Truncate helper for long text
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
    <Head title="Manajemen Kategori" />

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
                        Manajemen Kategori
                    </h1>
                    <p class="mt-1 text-sm text-slate-400">
                        Kelola data master kategori kimia untuk pengelompokan
                        materi pembelajaran dan bank soal pada sistem MVAR.
                    </p>
                </div>

                <div v-if="can('categories.create')" class="flex items-center">
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-4 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#06101c] focus:outline-none"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Tambah Kategori</span>
                    </button>
                </div>
            </div>

            <!-- Toolbar: Search & Refresh -->
            <div
                class="flex flex-col items-center justify-between gap-3 sm:flex-row"
            >
                <div class="w-full sm:max-w-md">
                    <SearchBar
                        v-model="searchQuery"
                        placeholder="Cari nama atau deskripsi kategori..."
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
            <div
                class="rounded-xl border border-[#14263b] bg-[#091624] shadow-xl"
            >
                <DataTable
                    :headers="tableHeaders"
                    :empty="categories.data.length === 0"
                    empty-message="Tidak ada kategori ditemukan."
                >
                    <tr
                        v-for="cat in categories.data"
                        :key="cat.id_category"
                        class="transition hover:bg-[#0c1c2e]"
                    >
                        <!-- NAMA KATEGORI -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="font-semibold text-white">
                                        {{ cat.name }}
                                    </span>
                                    <span
                                        class="ml-2 rounded bg-[#06121f] px-1.5 py-0.5 font-mono text-[10px] text-slate-500"
                                    >
                                        #{{ cat.id_category }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- DESKRIPSI -->
                        <td class="max-w-sm px-6 py-4">
                            <p
                                class="text-sm break-words text-slate-300"
                                :title="cat.description || undefined"
                            >
                                {{ truncateText(cat.description, 80) }}
                            </p>
                        </td>

                        <!-- TOTAL MATERI -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-300">
                                {{ cat.materials_count || 0 }} Materi
                            </span>
                        </td>

                        <!-- TOTAL SOAL -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-300">
                                {{ cat.questions_count || 0 }} Soal
                            </span>
                        </td>

                        <!-- AKSI (Icon only) -->
                        <td
                            v-if="
                                can('categories.edit') ||
                                can('categories.delete')
                            "
                            class="px-6 py-4 whitespace-nowrap"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    v-if="can('categories.edit')"
                                    @click="openEditModal(cat)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824] focus:outline-none"
                                    title="Edit Kategori"
                                    aria-label="Edit Kategori"
                                >
                                    <Pencil class="h-4 w-4" />
                                </button>
                                <button
                                    v-if="can('categories.delete')"
                                    @click="openDeleteModal(cat)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-rose-500/50 hover:bg-rose-500/10 hover:text-rose-400 focus:outline-none"
                                    title="Hapus Kategori"
                                    aria-label="Hapus Kategori"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </DataTable>

                <!-- Pagination Component -->
                <Pagination
                    :links="categories.links"
                    :from="categories.from"
                    :to="categories.to"
                    :total="categories.total"
                    item-name="kategori"
                />
            </div>
        </div>

        <!-- Create / Edit Category Modal -->
        <CategoryFormModal
            :show="formModalOpen"
            :category="selectedCategory"
            @close="formModalOpen = false"
        />

        <!-- Delete Confirmation Modal -->
        <DeleteCategoryModal
            :show="deleteModalOpen"
            :category="selectedCategory"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
