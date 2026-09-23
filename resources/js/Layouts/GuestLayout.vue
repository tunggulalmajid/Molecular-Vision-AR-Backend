<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="bg-gray-100 pt-6 sm:justify-center sm:pt-0 flex min-h-screen flex-col items-center"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 text-gray-500 fill-current" />
            </Link>
        </div>

        <div
            class="mt-6 bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg w-full overflow-hidden"
        >
            <slot />
        </div>
    </div>
</template>
