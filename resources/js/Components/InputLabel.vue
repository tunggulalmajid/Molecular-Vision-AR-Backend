<script setup lang="ts">
defineProps<{
    value?: string;
}>();
</script>

<template>
    <label class="text-sm font-medium text-gray-700 block">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
