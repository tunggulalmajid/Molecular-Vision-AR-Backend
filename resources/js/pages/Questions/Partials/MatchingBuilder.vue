<script setup lang="ts">
import { Plus, Trash2, ArrowRightLeft } from 'lucide-vue-next';

export interface MatchPairItem {
    id_option?: number;
    option_text: string;
    match_text: string;
}

const props = defineProps<{
    modelValue: MatchPairItem[];
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: MatchPairItem[]): void;
}>();

const updateLeft = (index: number, text: string) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], option_text: text };
    emit('update:modelValue', updated);
};

const updateRight = (index: number, text: string) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], match_text: text };
    emit('update:modelValue', updated);
};

const addPair = () => {
    if (props.modelValue.length >= 8) return;
    const updated = [...props.modelValue, { option_text: '', match_text: '' }];
    emit('update:modelValue', updated);
};

const removePair = (index: number) => {
    if (props.modelValue.length <= 2) return;
    const updated = props.modelValue.filter((_, idx) => idx !== index);
    emit('update:modelValue', updated);
};
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-white">
                    Pasangan Pernyataan & Jawaban (Menjodohkan)
                </h3>
                <p class="text-xs text-slate-400">
                    Tuliskan pasangan yang benar antara pernyataan di kolom kiri
                    dan jawaban cocok di kolom kanan. Sistem akan mengacak
                    urutannya untuk siswa.
                </p>
            </div>
            <button
                v-if="modelValue.length < 8"
                type="button"
                @click="addPair"
                class="inline-flex items-center gap-1.5 rounded-lg border border-[#1b344d] bg-[#0c1a28] px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824]"
            >
                <Plus class="h-3.5 w-3.5" />
                <span>Tambah Pasangan</span>
            </button>
        </div>

        <div class="space-y-3">
            <div
                v-for="(pair, idx) in modelValue"
                :key="idx"
                class="flex flex-col items-stretch gap-2.5 rounded-xl border border-[#1b344d] bg-[#0c1a28] p-3 shadow-md sm:flex-row sm:items-center"
            >
                <!-- Number badge -->
                <div
                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg border border-[#14263b] bg-[#091624] text-xs font-bold text-[#e5a824]"
                >
                    {{ idx + 1 }}
                </div>

                <!-- Left text (Premise) -->
                <div class="flex-1">
                    <input
                        type="text"
                        :value="pair.option_text"
                        @input="
                            updateLeft(
                                idx,
                                ($event.target as HTMLInputElement).value,
                            )
                        "
                        placeholder="Pernyataan premis kiri (contoh: H2O)..."
                        class="w-full rounded-lg border border-[#1b344d] bg-[#091624] px-3.5 py-2 text-sm text-white placeholder-slate-500 focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                        required
                    />
                </div>

                <!-- Connect icon -->
                <div
                    class="flex flex-shrink-0 items-center justify-center text-slate-500"
                >
                    <ArrowRightLeft class="h-4 w-4" />
                </div>

                <!-- Right text (Match) -->
                <div class="flex-1">
                    <input
                        type="text"
                        :value="pair.match_text"
                        @input="
                            updateRight(
                                idx,
                                ($event.target as HTMLInputElement).value,
                            )
                        "
                        placeholder="Pasangan cocok kanan (contoh: Air / Polar)..."
                        class="w-full rounded-lg border border-[#1b344d] bg-[#091624] px-3.5 py-2 text-sm text-white placeholder-slate-500 focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                        required
                    />
                </div>

                <!-- Delete button -->
                <button
                    v-if="modelValue.length > 2"
                    type="button"
                    @click="removePair(idx)"
                    class="self-end p-2 text-slate-500 transition hover:text-rose-400 sm:self-center"
                    title="Hapus Pasangan"
                >
                    <Trash2 class="h-4 w-4" />
                </button>
            </div>
        </div>

        <p v-if="error" class="text-xs text-rose-400">
            {{ error }}
        </p>
    </div>
</template>
