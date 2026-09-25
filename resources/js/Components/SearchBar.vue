<script setup lang="ts">
import { ref, watch } from 'vue';
import { Search, X } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
        debounceMs?: number;
    }>(),
    {
        placeholder: 'Cari data...',
        debounceMs: 350,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'search', value: string): void;
}>();

const localValue = ref(props.modelValue);
let timeout: ReturnType<typeof setTimeout> | null = null;

watch(
    () => props.modelValue,
    (newVal) => {
        localValue.value = newVal;
    },
);

const handleInput = (event: Event) => {
    const val = (event.target as HTMLInputElement).value;
    localValue.value = val;
    emit('update:modelValue', val);

    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        emit('search', val);
    }, props.debounceMs);
};

const clearSearch = () => {
    localValue.value = '';
    emit('update:modelValue', '');
    emit('search', '');
};
</script>

<template>
    <div class="relative w-full">
        <div
            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"
        >
            <Search class="h-4.5 w-4.5" :size="18" />
        </div>

        <input
            type="text"
            :value="localValue"
            @input="handleInput"
            :placeholder="placeholder"
            class="w-full rounded-lg border border-[#1b344d] bg-[#0c1a28] py-2.5 pr-9 pl-10 text-xs text-white placeholder-slate-500 transition duration-150 focus:border-[#2e5984] focus:ring-1 focus:ring-[#2e5984] focus:outline-none sm:text-sm"
        />

        <button
            v-if="localValue"
            type="button"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition hover:text-white"
            title="Hapus pencarian"
        >
            <X class="h-4 w-4" :size="16" />
        </button>
    </div>
</template>
