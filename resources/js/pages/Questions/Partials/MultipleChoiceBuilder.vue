<script setup lang="ts">
import { Plus, Trash2, CheckCircle2 } from 'lucide-vue-next';

export interface OptionItem {
    id_option?: number;
    option_text: string;
    is_correct: boolean;
}

const props = defineProps<{
    modelValue: OptionItem[];
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: OptionItem[]): void;
}>();

const optionLetters = ['A', 'B', 'C', 'D', 'E', 'F'];

const setCorrect = (selectedIndex: number) => {
    const updated = props.modelValue.map((opt, idx) => ({
        ...opt,
        is_correct: idx === selectedIndex,
    }));
    emit('update:modelValue', updated);
};

const updateText = (index: number, text: string) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], option_text: text };
    emit('update:modelValue', updated);
};

const addOption = () => {
    if (props.modelValue.length >= 6) return;
    const updated = [
        ...props.modelValue,
        { option_text: '', is_correct: false },
    ];
    emit('update:modelValue', updated);
};

const removeOption = (index: number) => {
    if (props.modelValue.length <= 2) return;
    const wasCorrect = props.modelValue[index].is_correct;
    const updated = props.modelValue.filter((_, idx) => idx !== index);
    // If the removed option was the correct one, default the first one to correct
    if (wasCorrect && updated.length > 0) {
        updated[0].is_correct = true;
    }
    emit('update:modelValue', updated);
};
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-white">
                    Pilihan Jawaban (Pilihan Ganda)
                </h3>
                <p class="text-xs text-slate-400">
                    Pilih salah satu lingkaran radio di bawah ini sebagai kunci
                    jawaban yang benar.
                </p>
            </div>
            <button
                v-if="modelValue.length < 6"
                type="button"
                @click="addOption"
                class="inline-flex items-center gap-1.5 rounded-lg border border-[#1b344d] bg-[#0c1a28] px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824]"
            >
                <Plus class="h-3.5 w-3.5" />
                <span>Tambah Opsi</span>
            </button>
        </div>

        <div class="space-y-3">
            <div
                v-for="(opt, idx) in modelValue"
                :key="idx"
                class="flex items-center gap-3 rounded-xl border p-3 transition-all"
                :class="[
                    opt.is_correct
                        ? 'border-emerald-500/50 bg-emerald-500/5 shadow-md shadow-emerald-950/20'
                        : 'border-[#1b344d] bg-[#0c1a28]',
                ]"
            >
                <!-- Radio Button for selecting correct answer -->
                <button
                    type="button"
                    @click="setCorrect(idx)"
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg border text-xs font-bold transition"
                    :class="[
                        opt.is_correct
                            ? 'border-emerald-500 bg-emerald-500 text-black shadow-sm shadow-emerald-500/50'
                            : 'border-[#1b344d] bg-[#091624] text-slate-400 hover:border-[#e5a824] hover:text-white',
                    ]"
                    :title="
                        opt.is_correct
                            ? 'Kunci Jawaban Benar'
                            : 'Jadikan Kunci Jawaban'
                    "
                >
                    <CheckCircle2 v-if="opt.is_correct" class="h-4 w-4" />
                    <span v-else>{{ optionLetters[idx] || idx + 1 }}</span>
                </button>

                <!-- Text Input for option -->
                <input
                    type="text"
                    :value="opt.option_text"
                    @input="
                        updateText(
                            idx,
                            ($event.target as HTMLInputElement).value,
                        )
                    "
                    :placeholder="`Tuliskan teks pilihan ${optionLetters[idx] || idx + 1}...`"
                    class="w-full rounded-lg border border-[#1b344d] bg-[#091624] px-3.5 py-2 text-sm text-white placeholder-slate-500 focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                    required
                />

                <!-- Indicator Pill -->
                <span
                    v-if="opt.is_correct"
                    class="hidden flex-shrink-0 rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-400 sm:inline-block"
                >
                    Kunci Benar
                </span>

                <!-- Delete button (if > 2 options) -->
                <button
                    v-if="modelValue.length > 2"
                    type="button"
                    @click="removeOption(idx)"
                    class="flex-shrink-0 p-1.5 text-slate-500 transition hover:text-rose-400"
                    title="Hapus Opsi"
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
