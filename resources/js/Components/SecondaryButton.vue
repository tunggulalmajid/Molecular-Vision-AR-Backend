<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <button
        :type="type"
        class="rounded-md border-gray-300 bg-white px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 shadow-sm ease-in-out hover:bg-gray-50 focus:ring-indigo-500 inline-flex items-center border uppercase transition duration-150 focus:ring-2 focus:ring-offset-2 focus:outline-none disabled:opacity-25"
    >
        <slot />
    </button>
</template>
