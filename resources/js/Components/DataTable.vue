<script setup lang="ts">
export interface TableHeader {
    label: string;
    align?: 'left' | 'center' | 'right';
    width?: string;
}

withDefaults(
    defineProps<{
        headers: TableHeader[];
        empty?: boolean;
        emptyMessage?: string;
    }>(),
    {
        empty: false,
        emptyMessage: 'Tidak ada data ditemukan.',
    },
);
</script>

<template>
    <div
        class="w-full overflow-hidden rounded-xl border border-[#152a42] bg-[#0c1c2e] shadow-xl"
    >
        <div class="overflow-x-auto">
            <table
                class="w-full min-w-[680px] text-left text-sm text-slate-300"
            >
                <!-- Table Header -->
                <thead
                    class="border-b border-[#152a42] bg-[#0a1827]/80 text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                >
                    <tr>
                        <th
                            v-for="(header, idx) in headers"
                            :key="idx"
                            scope="col"
                            :style="header.width ? { width: header.width } : {}"
                            :class="[
                                'px-6 py-4 whitespace-nowrap',
                                header.align === 'center'
                                    ? 'text-center'
                                    : header.align === 'right'
                                      ? 'text-right'
                                      : 'text-left',
                            ]"
                        >
                            {{ header.label }}
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="divide-y divide-[#13253b]">
                    <tr v-if="empty">
                        <td
                            :colspan="headers.length"
                            class="px-6 py-12 text-center text-sm text-slate-400"
                        >
                            <slot name="empty">
                                <div
                                    class="flex flex-col items-center justify-center gap-2"
                                >
                                    <p class="font-medium text-slate-400">
                                        {{ emptyMessage }}
                                    </p>
                                </div>
                            </slot>
                        </td>
                    </tr>
                    <slot v-else />
                </tbody>
            </table>
        </div>
    </div>
</template>
