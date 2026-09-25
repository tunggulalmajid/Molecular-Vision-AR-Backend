<script setup lang="ts">
import { RefreshCw } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        loading?: boolean;
        title?: string;
    }>(),
    {
        loading: false,
        title: 'Segarkan data',
    },
);

defineEmits<{
    (e: 'click'): void;
}>();
</script>

<template>
    <button
        type="button"
        @click="$emit('click')"
        :disabled="loading"
        :title="title"
        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition-all duration-150 hover:border-[#2b5278] hover:bg-[#122538] hover:text-white focus:ring-1 focus:ring-[#2e5984] focus:outline-none disabled:opacity-50"
    >
        <RefreshCw
            class="h-4 w-4"
            :class="{ 'animate-spin text-[#e8a917]': loading }"
            :size="16"
        />
    </button>
</template>
