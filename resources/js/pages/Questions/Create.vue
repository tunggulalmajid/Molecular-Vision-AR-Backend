<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MultipleChoiceBuilder, {
    type OptionItem,
} from './Partials/MultipleChoiceBuilder.vue';
import MultipleSelectBuilder from './Partials/MultipleSelectBuilder.vue';
import TrueFalseBuilder from './Partials/TrueFalseBuilder.vue';
import ShortAnswerBuilder from './Partials/ShortAnswerBuilder.vue';
import MatchingBuilder, {
    type MatchPairItem,
} from './Partials/MatchingBuilder.vue';
import { ArrowLeft, Check, Loader2, HelpCircle } from 'lucide-vue-next';

interface CategoryOption {
    id_category: number;
    name: string;
}

const props = defineProps<{
    categories: CategoryOption[];
}>();

const questionTypeOptions = [
    { value: 'multiple_choice', label: 'Pilihan Ganda (1 Jawaban Benar)' },
    { value: 'multiple_select', label: 'Pilihan Jamak (Centang Lebih dari 1)' },
    { value: 'true_false', label: 'Benar / Salah (Pernyataan)' },
    { value: 'short_answer', label: 'Isian Singkat (Angka Saja)' },
    { value: 'matching', label: 'Menjodohkan / Memasangkan' },
];

const form = useForm({
    name: '',
    id_category: props.categories[0]?.id_category || '',
    question_type: 'multiple_choice' as
        | 'multiple_choice'
        | 'multiple_select'
        | 'true_false'
        | 'short_answer'
        | 'matching',
    options: [
        { option_text: '', is_correct: true },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
    ] as OptionItem[],
    tf_answer: 'Benar' as 'Benar' | 'Salah',
    numeric_answer: '' as string | number,
    pairs: [
        { option_text: '', match_text: '' },
        { option_text: '', match_text: '' },
        { option_text: '', match_text: '' },
    ] as MatchPairItem[],
});

const handleTypeChange = () => {
    // Reset defaults when changing type if empty
    if (form.question_type === 'multiple_choice' && form.options.length === 0) {
        form.options = [
            { option_text: '', is_correct: true },
            { option_text: '', is_correct: false },
            { option_text: '', is_correct: false },
            { option_text: '', is_correct: false },
        ];
    } else if (
        form.question_type === 'multiple_select' &&
        form.options.length === 0
    ) {
        form.options = [
            { option_text: '', is_correct: true },
            { option_text: '', is_correct: true },
            { option_text: '', is_correct: false },
        ];
    } else if (form.question_type === 'matching' && form.pairs.length === 0) {
        form.pairs = [
            { option_text: '', match_text: '' },
            { option_text: '', match_text: '' },
            { option_text: '', match_text: '' },
        ];
    }
};

const submit = () => {
    form.post(route('questions.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Tambah Butir Soal Baru" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Back navigation & Title Header -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('questions.index')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:bg-[#14263b] hover:text-white"
                        title="Kembali ke Bank Soal"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-white"
                        >
                            Tambah Butir Soal Baru
                        </h1>
                        <p class="mt-0.5 text-xs text-slate-400">
                            Buat butir pertanyaan kuis kimia interaktif
                            berdasarkan kategori materi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Section 1: Kategori & Tipe Soal -->
                <div
                    class="space-y-5 rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <!-- Kategori Kimia -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Kategori Kimia
                                <span class="text-rose-400">*</span>
                            </label>
                            <select
                                v-model="form.id_category"
                                class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] px-3.5 py-2.5 text-sm text-white focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                                required
                            >
                                <option value="" disabled>
                                    Pilih Kategori...
                                </option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat.id_category"
                                    :value="cat.id_category"
                                >
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.id_category"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.id_category }}
                            </p>
                        </div>

                        <!-- Tipe Soal -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Tipe Format Soal
                                <span class="text-rose-400">*</span>
                            </label>
                            <select
                                v-model="form.question_type"
                                @change="handleTypeChange"
                                class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] px-3.5 py-2.5 text-sm text-white focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                                required
                            >
                                <option
                                    v-for="typeOpt in questionTypeOptions"
                                    :key="typeOpt.value"
                                    :value="typeOpt.value"
                                >
                                    {{ typeOpt.label }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.question_type"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.question_type }}
                            </p>
                        </div>

                        <!-- Pertanyaan / Teks Soal -->
                        <div class="md:col-span-2">
                            <div
                                class="mb-1.5 flex items-center justify-between"
                            >
                                <label
                                    class="block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                                >
                                    Redaksi / Pertanyaan Soal
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">
                                    {{ form.name.length }} karakter
                                </span>
                            </div>
                            <textarea
                                v-model="form.name"
                                rows="3"
                                placeholder="Tuliskan teks pertanyaan atau pernyataan soal kuis di sini..."
                                class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                                required
                            ></textarea>
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Dynamic Answer Option Builders -->
                <div
                    class="rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <!-- 1. Pilihan Ganda -->
                    <MultipleChoiceBuilder
                        v-if="form.question_type === 'multiple_choice'"
                        v-model="form.options"
                        :error="form.errors.options"
                    />

                    <!-- 2. Pilihan Jamak / Checkbox -->
                    <MultipleSelectBuilder
                        v-else-if="form.question_type === 'multiple_select'"
                        v-model="form.options"
                        :error="form.errors.options"
                    />

                    <!-- 3. Benar / Salah -->
                    <TrueFalseBuilder
                        v-else-if="form.question_type === 'true_false'"
                        v-model="form.tf_answer"
                        :error="form.errors.tf_answer"
                    />

                    <!-- 4. Isian Singkat Numerik -->
                    <ShortAnswerBuilder
                        v-else-if="form.question_type === 'short_answer'"
                        v-model="form.numeric_answer"
                        :error="form.errors.numeric_answer"
                    />

                    <!-- 5. Menjodohkan / Memasangkan -->
                    <MatchingBuilder
                        v-else-if="form.question_type === 'matching'"
                        v-model="form.pairs"
                        :error="form.errors.pairs"
                    />
                </div>

                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link
                        :href="route('questions.index')"
                        class="rounded-xl border border-[#1b344d] bg-[#0d1e30] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-[#122840] hover:text-white"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-6 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:outline-none disabled:opacity-50"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin text-black"
                        />
                        <Check v-else class="h-4 w-4" />
                        <span>Simpan Butir Soal</span>
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
