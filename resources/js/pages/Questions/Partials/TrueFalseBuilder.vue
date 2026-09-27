<script setup lang="ts">
import { CheckCircle2, XCircle } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string; // 'Benar' | 'Salah'
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const selectAnswer = (val: 'Benar' | 'Salah') => {
    emit('update:modelValue', val);
};
</script>

<template>
    <div class="space-y-4">
        <div>
            <h3 class="text-sm font-semibold text-white">
                Kunci Jawaban Pernyataan
            </h3>
            <p class="text-xs text-slate-400">
                Tentukan apakah pernyataan yang ditulis di atas bernilai Benar
                atau Salah.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Pilihan BENAR -->
            <button
                type="button"
                @click="selectAnswer('Benar')"
                class="flex items-center gap-4 rounded-xl border p-4 text-left transition-all duration-150"
                :class="[
                    modelValue === 'Benar'
                        ? 'border-emerald-500 bg-emerald-500/10 shadow-lg shadow-emerald-950/30'
                        : 'border-[#1b344d] bg-[#0c1a28] hover:border-emerald-500/40 hover:bg-[#0f2336]',
                ]"
            >
                <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border transition"
                    :class="[
                        modelValue === 'Benar'
                            ? 'border-emerald-500 bg-emerald-500 text-black'
                            : 'border-[#1b344d] bg-[#091624] text-slate-400',
                    ]"
                >
                    <CheckCircle2 class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm font-bold text-white">BENAR (True)</p>
                    <p class="mt-0.5 text-xs text-slate-400">
                        Pernyataan pada soal ini valid dan tepat.
                    </p>
                </div>
            </button>

            <!-- Pilihan SALAH -->
            <button
                type="button"
                @click="selectAnswer('Salah')"
                class="flex items-center gap-4 rounded-xl border p-4 text-left transition-all duration-150"
                :class="[
                    modelValue === 'Salah'
                        ? 'border-rose-500 bg-rose-500/10 shadow-lg shadow-rose-950/30'
                        : 'border-[#1b344d] bg-[#0c1a28] hover:border-rose-500/40 hover:bg-[#0f2336]',
                ]"
            >
                <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border transition"
                    :class="[
                        modelValue === 'Salah'
                            ? 'border-rose-500 bg-rose-500 text-white'
                            : 'border-[#1b344d] bg-[#091624] text-slate-400',
                    ]"
                >
                    <XCircle class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm font-bold text-white">SALAH (False)</p>
                    <p class="mt-0.5 text-xs text-slate-400">
                        Pernyataan pada soal ini keliru atau salah.
                    </p>
                </div>
            </button>
        </div>

        <p v-if="error" class="text-xs text-rose-400">
            {{ error }}
        </p>
    </div>
</template>
