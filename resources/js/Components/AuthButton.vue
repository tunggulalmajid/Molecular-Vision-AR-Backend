<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
        loading?: boolean;
        disabled?: boolean;
    }>(),
    {
        type: 'submit',
        loading: false,
        disabled: false,
    },
);
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        class="inline-flex w-full items-center justify-center rounded-lg bg-[#e8a917] px-4 py-3 text-sm font-semibold text-slate-950 shadow-md transition-all duration-200 hover:bg-[#d6980b] focus:ring-2 focus:ring-[#e8a917]/50 focus:ring-offset-2 focus:ring-offset-[#07111b] focus:outline-none active:scale-[0.99] active:bg-[#c28704] disabled:cursor-not-allowed disabled:opacity-50"
    >
        <svg
            v-if="loading"
            class="mr-2.5 -ml-1 h-4 w-4 animate-spin text-slate-950"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v8H4z"
            />
        </svg>
        <slot />
    </button>
</template>
