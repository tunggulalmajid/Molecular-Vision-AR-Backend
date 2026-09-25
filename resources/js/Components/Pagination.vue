<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

withDefaults(
    defineProps<{
        links: PaginationLink[];
        from?: number | null;
        to?: number | null;
        total?: number;
        itemName?: string;
    }>(),
    {
        from: 0,
        to: 0,
        total: 0,
        itemName: 'data',
    },
);

const cleanLabel = (label: string) => {
    return label
        .replace('&laquo; Previous', 'Previous')
        .replace('Next &raquo;', 'Next')
        .replace('&laquo;', '')
        .replace('&raquo;', '');
};
</script>

<template>
    <div
        v-if="(total !== undefined && total > 0) || (links && links.length > 0)"
        class="flex flex-col items-center justify-between gap-4 border-t border-[#14263b] px-6 py-4 sm:flex-row"
    >
        <!-- Summary Info -->
        <p class="text-xs text-slate-400">
            Menampilkan
            <span class="font-semibold text-slate-200"
                >{{ from ?? 0 }}-{{ to ?? 0 }}</span
            >
            dari
            <span class="font-semibold text-slate-200">{{ total ?? 0 }}</span>
            {{ itemName }}
        </p>

        <!-- Page Link Buttons (only shown when multiple pages exist) -->
        <div v-if="links && links.length > 3" class="flex items-center gap-1.5">
            <template v-for="(link, idx) in links" :key="idx">
                <span
                    v-if="!link.url"
                    class="inline-flex h-8 items-center justify-center rounded-lg border border-[#16293f] bg-[#091522] px-3 text-xs text-slate-500 opacity-60"
                    v-html="cleanLabel(link.label)"
                />
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    preserve-state
                    :class="[
                        'inline-flex h-8 items-center justify-center rounded-lg px-3 text-xs font-medium transition-all duration-150',
                        link.active
                            ? 'bg-[#e8a917] font-bold text-slate-950 shadow-sm'
                            : 'border border-[#172c44] bg-[#0c1a29] text-slate-300 hover:border-[#2a4d74] hover:bg-[#122437] hover:text-white',
                    ]"
                    v-html="cleanLabel(link.label)"
                />
            </template>
        </div>
    </div>
</template>
