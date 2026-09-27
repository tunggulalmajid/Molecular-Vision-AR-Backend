<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchBar from '@/Components/SearchBar.vue';
import RefreshButton from '@/Components/RefreshButton.vue';
import DataTable, { type TableHeader } from '@/Components/DataTable.vue';
import Pagination, { type PaginationLink } from '@/Components/Pagination.vue';
import DeleteQuestionModal, {
    type QuestionItem,
} from './DeleteQuestionModal.vue';
import { usePermission } from '@/composables/usePermission';
import {
    Plus,
    Pencil,
    Trash2,
    CheckCircle2,
    AlertCircle,
    FileQuestion,
    Filter,
} from 'lucide-vue-next';

interface CategoryOption {
    id_category: number;
    name: string;
}

interface PaginatedQuestions {
    data: QuestionItem[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
}

const props = defineProps<{
    questions: PaginatedQuestions;
    categories: CategoryOption[];
    filters: {
        search: string;
        category_id: string | number;
        question_type: string;
    };
}>();

const page = usePage();
const { can } = usePermission();

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

// Filters State
const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category_id || '');
const selectedType = ref(props.filters.question_type || '');
const isRefreshing = ref(false);

// Delete Modal State
const deleteModalOpen = ref(false);
const questionToDelete = ref<QuestionItem | null>(null);

// Table configuration
const tableHeaders = computed<TableHeader[]>(() => {
    const headers: TableHeader[] = [
        { label: 'PERTANYAAN SOAL', width: '320px' },
        { label: 'KATEGORI', width: '160px' },
        { label: 'TIPE SOAL', width: '150px' },
        { label: 'KUNCI JAWABAN', width: '220px' },
    ];

    if (can('questions.edit') || can('questions.delete')) {
        headers.push({ label: 'AKSI', align: 'center', width: '100px' });
    }

    return headers;
});

// Search & Filter handlers
const applyFilters = () => {
    router.get(
        route('questions.index'),
        {
            search: searchQuery.value || undefined,
            category_id: selectedCategory.value || undefined,
            question_type: selectedType.value || undefined,
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

const handleTypeChange = () => {
    applyFilters();
};

const handleRefresh = () => {
    isRefreshing.value = true;
    router.reload({
        only: ['questions'],
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
};

const openDeleteModal = (question: QuestionItem) => {
    questionToDelete.value = question;
    deleteModalOpen.value = true;
};

// Truncate helper with ellipsis
const truncateText = (
    text: string | null | undefined,
    maxLength = 100,
): string => {
    if (!text) return '-';
    return text.length > maxLength
        ? text.substring(0, maxLength) + '...'
        : text;
};

// Type badge formatter
const getTypeBadge = (type: string) => {
    switch (type) {
        case 'multiple_choice':
            return {
                label: 'Pilihan Ganda',
                class: 'border-blue-500/20 bg-blue-500/10 text-blue-400',
            };
        case 'multiple_select':
            return {
                label: 'Pilihan Jamak',
                class: 'border-purple-500/20 bg-purple-500/10 text-purple-400',
            };
        case 'true_false':
            return {
                label: 'Benar / Salah',
                class: 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400',
            };
        case 'short_answer':
            return {
                label: 'Isian Angka',
                class: 'border-amber-500/20 bg-amber-500/10 text-amber-400',
            };
        case 'matching':
            return {
                label: 'Menjodohkan',
                class: 'border-cyan-500/20 bg-cyan-500/10 text-cyan-400',
            };
        default:
            return {
                label: type,
                class: 'border-slate-500/20 bg-slate-500/10 text-slate-400',
            };
    }
};

// Answer summary preview
const getAnswerSummary = (question: QuestionItem) => {
    if (!question.options || question.options.length === 0) return '-';

    switch (question.question_type) {
        case 'multiple_choice': {
            const correct = question.options.find((opt) => opt.is_correct);
            return correct ? correct.option_text : '-';
        }
        case 'multiple_select': {
            const corrects = question.options.filter((opt) => opt.is_correct);
            return corrects.length > 0
                ? corrects.map((opt) => opt.option_text).join(', ')
                : '-';
        }
        case 'true_false': {
            const correct = question.options.find((opt) => opt.is_correct);
            return correct ? correct.option_text : '-';
        }
        case 'short_answer': {
            return question.options[0]?.option_text || '-';
        }
        case 'matching': {
            return `${question.options.length} Pasang Pernyataan`;
        }
        default:
            return '-';
    }
};
</script>

<template>
    <Head title="Manajemen Bank Soal & Kuis" />

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
                        Manajemen Bank Soal & Kuis
                    </h1>
                    <p class="mt-1 text-sm text-slate-400">
                        Kelola butir soal evaluasi pembelajaran kimia
                        berdasarkan kategori topik dan 5 tipe format kuis.
                    </p>
                </div>

                <div v-if="can('questions.create')" class="flex items-center">
                    <Link
                        :href="route('questions.create')"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-4 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#06101c] focus:outline-none"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Tambah Soal</span>
                    </Link>
                </div>
            </div>

            <!-- Toolbar: Search, Category Filter, Type Filter & Refresh -->
            <div
                class="flex flex-col items-stretch justify-between gap-3 sm:flex-row sm:items-center"
            >
                <div
                    class="flex flex-1 flex-col flex-wrap gap-3 sm:flex-row sm:items-center"
                >
                    <!-- Search Input -->
                    <div class="w-full sm:max-w-xs">
                        <SearchBar
                            v-model="searchQuery"
                            placeholder="Cari pertanyaan soal..."
                            @search="handleSearch"
                        />
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="relative w-full sm:w-56">
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

                    <!-- Question Type Filter Dropdown -->
                    <div class="relative w-full sm:w-52">
                        <select
                            v-model="selectedType"
                            @change="handleTypeChange"
                            class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] px-3.5 py-2.5 text-sm text-white focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                        >
                            <option value="">Semua Format Soal</option>
                            <option value="multiple_choice">
                                Pilihan Ganda
                            </option>
                            <option value="multiple_select">
                                Pilihan Jamak
                            </option>
                            <option value="true_false">Benar / Salah</option>
                            <option value="short_answer">Isian Angka</option>
                            <option value="matching">Menjodohkan</option>
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
                    :empty="questions.data.length === 0"
                    empty-message="Tidak ada butir soal ditemukan."
                >
                    <tr
                        v-for="q in questions.data"
                        :key="q.id_question"
                        class="transition hover:bg-[#0c1c2e]"
                    >
                        <!-- PERTANYAAN SOAL -->
                        <td class="max-w-md px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0">
                                    <div class="mb-1 flex items-center gap-2">
                                        <span
                                            class="rounded bg-[#06121f] px-1.5 py-0.5 font-mono text-[10px] text-slate-500"
                                        >
                                            #{{ q.id_question }}
                                        </span>
                                    </div>
                                    <p
                                        class="line-clamp-2 text-sm font-semibold break-words text-white"
                                        :title="q.name"
                                    >
                                        {{ truncateText(q.name, 120) }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- KATEGORI -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-300">
                                {{ q.category?.name || 'Tanpa Kategori' }}
                            </span>
                        </td>

                        <!-- TIPE SOAL -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-block rounded-md border px-2.5 py-0.5 text-xs font-semibold"
                                :class="getTypeBadge(q.question_type).class"
                            >
                                {{ getTypeBadge(q.question_type).label }}
                            </span>
                        </td>

                        <!-- KUNCI JAWABAN -->
                        <td class="max-w-xs px-6 py-4">
                            <p
                                class="line-clamp-2 text-xs break-words text-slate-300"
                                :title="getAnswerSummary(q)"
                            >
                                {{ truncateText(getAnswerSummary(q), 60) }}
                            </p>
                        </td>

                        <!-- AKSI -->
                        <td
                            v-if="
                                can('questions.edit') || can('questions.delete')
                            "
                            class="px-6 py-4 whitespace-nowrap"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Link
                                    v-if="can('questions.edit')"
                                    :href="
                                        route('questions.edit', q.id_question)
                                    "
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824] focus:outline-none"
                                    title="Edit Soal"
                                    aria-label="Edit Soal"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                                <button
                                    v-if="can('questions.delete')"
                                    @click="openDeleteModal(q)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:border-rose-500/50 hover:bg-rose-500/10 hover:text-rose-400 focus:outline-none"
                                    title="Hapus Soal"
                                    aria-label="Hapus Soal"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </DataTable>

                <!-- Pagination Component -->
                <Pagination
                    :links="questions.links"
                    :from="questions.from"
                    :to="questions.to"
                    :total="questions.total"
                    item-name="soal"
                />
            </div>

            <!-- Delete Confirmation Modal -->
            <DeleteQuestionModal
                :show="deleteModalOpen"
                :question="questionToDelete"
                @close="deleteModalOpen = false"
            />
        </div>
    </AuthenticatedLayout>
</template>
