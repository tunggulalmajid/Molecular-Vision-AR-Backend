<script setup lang="ts">
import type { Component } from 'vue';

export interface TabItem {
    id: string;
    label: string;
    icon?: Component;
    count?: number;
}

defineProps<{
    tabs: TabItem[];
    modelValue: string;
}>();

defineEmits<{
    (e: 'update:modelValue', id: string): void;
}>();
</script>

<template>
    <div
        class="inline-flex flex-wrap items-center gap-1.5 rounded-xl border border-[#14263b] bg-[#091624] p-1.5 shadow-sm"
    >
        <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            @click="$emit('update:modelValue', tab.id)"
            :class="[
                'inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-xs font-semibold transition-all duration-150 focus:outline-none',
                modelValue === tab.id
                    ? 'bg-[#14293f] text-[#e5a824] shadow-sm ring-1 ring-[#e5a824]/20'
                    : 'text-slate-400 hover:bg-[#0d1e30] hover:text-slate-200',
            ]"
        >
            <component v-if="tab.icon" :is="tab.icon" class="h-4 w-4" />
            <span>{{ tab.label }}</span>
            <span
                v-if="tab.count !== undefined"
                :class="[
                    'ml-1 rounded-full px-2 py-0.5 text-[10px] font-bold',
                    modelValue === tab.id
                        ? 'bg-[#e5a824]/20 text-[#e5a824]'
                        : 'bg-[#06101c] text-slate-400',
                ]"
            >
                {{ tab.count }}
            </span>
        </button>
    </div>
</template>
