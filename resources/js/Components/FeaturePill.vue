<script setup lang="ts">
defineProps<{
    text: string;
}>();
</script>

<template>
    <div
        class="flex items-center gap-3.5 rounded-full border border-[#1e3c5e]/70 bg-[#122840]/70 px-4 py-2.5 text-xs text-slate-300 shadow-sm backdrop-blur-md transition-all duration-200 hover:border-[#28507d] hover:bg-[#15304e]/90 sm:text-sm"
    >
        <div class="flex shrink-0 items-center justify-center text-slate-300">
            <slot name="icon" />
        </div>
        <span class="leading-snug tracking-wide text-slate-300">{{ text }}</span>
    </div>
</template>
