<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Users, Atom, BookOpen, FileQuestion } from 'lucide-vue-next';

interface StatsProps {
    total_user?: number;
    total_molekul?: number;
    total_materi?: number;
    total_soal?: number;
}

const props = withDefaults(
    defineProps<{
        stats?: StatsProps;
    }>(),
    {
        stats: () => ({
            total_user: 1386,
            total_molekul: 348,
            total_materi: 86,
            total_soal: 14290,
        }),
    }
);

const formattedStats = computed(() => ({
    user: (props.stats?.total_user ?? 1386).toLocaleString(),
    molekul: (props.stats?.total_molekul ?? 348).toLocaleString(),
    materi: (props.stats?.total_materi ?? 86).toLocaleString(),
    soal: (props.stats?.total_soal ?? 14290).toLocaleString(),
}));
</script>

<template>
    <Head title="Dashboard Analitik" />

    <AuthenticatedLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-[34px] font-bold tracking-tight text-white">
                Dashboard
            </h1>
            <p class="mt-2 text-xs sm:text-sm text-slate-400 leading-relaxed max-w-2xl">
                Pantau Ringkasan Data didalam MVAR
            </p>
        </div>

        <!-- Metric Stat Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- Card 1: TOTAL USER -->
            <div
                class="group rounded-xl border border-[#152a42] bg-[#0c1c2e] p-5 sm:p-6 transition-all duration-200 hover:border-[#224467] hover:shadow-lg hover:shadow-cyan-950/20"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                        TOTAL USER
                    </span>
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-[#1b3856] bg-[#14283e] text-[#6b92be] transition group-hover:text-cyan-400"
                    >
                        <Users class="h-4.5 w-4.5" :size="18" />
                    </div>
                </div>
                <div class="mt-4 mb-2 text-3xl sm:text-[34px] font-bold tracking-tight text-white">
                    {{ formattedStats.user }}
                </div>
                <p class="text-xs text-slate-400 leading-relaxed font-normal">
                    Pengguna Terdaftar dalam sistem
                </p>
            </div>

            <!-- Card 2: TOTAL MOLEKUL -->
            <div
                class="group rounded-xl border border-[#152a42] bg-[#0c1c2e] p-5 sm:p-6 transition-all duration-200 hover:border-[#224467] hover:shadow-lg hover:shadow-cyan-950/20"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                        TOTAL MOLEKUL
                    </span>
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-[#1b3856] bg-[#14283e] text-[#6b92be] transition group-hover:text-cyan-400"
                    >
                        <Atom class="h-4.5 w-4.5" :size="18" />
                    </div>
                </div>
                <div class="mt-4 mb-2 text-3xl sm:text-[34px] font-bold tracking-tight text-white">
                    {{ formattedStats.molekul }}
                </div>
                <p class="text-xs text-slate-400 leading-relaxed font-normal">
                    Materi Molekul Tersimpan dalam database
                </p>
            </div>

            <!-- Card 3: TOTAL MATERI -->
            <div
                class="group rounded-xl border border-[#152a42] bg-[#0c1c2e] p-5 sm:p-6 transition-all duration-200 hover:border-[#224467] hover:shadow-lg hover:shadow-cyan-950/20"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                        TOTAL MATERI
                    </span>
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-[#1b3856] bg-[#14283e] text-[#6b92be] transition group-hover:text-cyan-400"
                    >
                        <BookOpen class="h-4.5 w-4.5" :size="18" />
                    </div>
                </div>
                <div class="mt-4 mb-2 text-3xl sm:text-[34px] font-bold tracking-tight text-white">
                    {{ formattedStats.materi }}
                </div>
                <p class="text-xs text-slate-400 leading-relaxed font-normal">
                    Materi Tersedia dalam database
                </p>
            </div>

            <!-- Card 4: TOTAL SOAL -->
            <div
                class="group rounded-xl border border-[#152a42] bg-[#0c1c2e] p-5 sm:p-6 transition-all duration-200 hover:border-[#224467] hover:shadow-lg hover:shadow-cyan-950/20"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                        TOTAL SOAL
                    </span>
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-[#1b3856] bg-[#14283e] text-[#6b92be] transition group-hover:text-cyan-400"
                    >
                        <FileQuestion class="h-4.5 w-4.5" :size="18" />
                    </div>
                </div>
                <div class="mt-4 mb-2 text-3xl sm:text-[34px] font-bold tracking-tight text-white">
                    {{ formattedStats.soal }}
                </div>
                <p class="text-xs text-slate-400 leading-relaxed font-normal">
                    Tersedia untuk dikerjakan didalam databse
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
