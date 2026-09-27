<script setup lang="ts">
import { Binary, HelpCircle } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string | number;
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
};
</script>

<template>
    <div class="space-y-4">
        <div>
            <h3 class="text-sm font-semibold text-white">
                Kunci Jawaban Isian Singkat (Numerik)
            </h3>
            <p class="text-xs text-slate-400">
                Masukkan nilai angka yang menjadi jawaban benar. Input ini hanya
                menerima angka bulat atau desimal (contoh: 14, 2.5, -40).
            </p>
        </div>

        <div
            class="max-w-md space-y-3 rounded-xl border border-[#1b344d] bg-[#0c1a28] p-4"
        >
            <label
                class="block text-xs font-semibold tracking-wider text-slate-300 uppercase"
            >
                Nilai Angka Benar
                <span class="text-rose-400">*</span>
            </label>

            <div class="relative">
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#e5a824]"
                >
                    <Binary class="h-4 w-4" />
                </div>
                <input
                    type="number"
                    step="any"
                    :value="modelValue"
                    @input="handleInput"
                    placeholder="Contoh: 14 atau 2.5"
                    class="w-full rounded-lg border border-[#1b344d] bg-[#091624] py-2.5 pr-4 pl-10 text-sm text-white placeholder-slate-500 focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                    required
                />
            </div>

            <div
                class="flex items-start gap-2 rounded-lg border border-[#14263b] bg-[#091624]/80 p-2.5 text-xs text-slate-400"
            >
                <HelpCircle
                    class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#e5a824]"
                />
                <p>
                    Pada aplikasi mobile, keyboard siswa akan otomatis terbuka
                    dalam mode numerik (angka).
                </p>
            </div>
        </div>

        <p v-if="error" class="text-xs text-rose-400">
            {{ error }}
        </p>
    </div>
</template>
