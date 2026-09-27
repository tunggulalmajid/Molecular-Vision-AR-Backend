<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePermission } from '@/composables/usePermission';
import { Plus, ArrowRight, Sparkles, ChevronRight } from 'lucide-vue-next';

interface StatsProps {
    total_user?: number;
    total_molekul?: number;
    total_materi?: number;
    total_soal?: number;
    total_kategori?: number;
    total_role?: number;
}

interface RecentMolecule {
    id_molecule: number;
    name: string;
    formula: string;
    shape: string;
}

interface RecentMaterial {
    id_material: number;
    name: string;
    category?: {
        id_category: number;
        name: string;
    };
}

interface RecentQuestion {
    id_question: number;
    name: string;
    question_type: string;
    category?: {
        id_category: number;
        name: string;
    };
}

interface RecentCategory {
    id_category: number;
    name: string;
    materials_count?: number;
    questions_count?: number;
}

const props = withDefaults(
    defineProps<{
        stats?: StatsProps;
        recentMolecules?: RecentMolecule[];
        recentMaterials?: RecentMaterial[];
        recentQuestions?: RecentQuestion[];
        recentCategories?: RecentCategory[];
    }>(),
    {
        stats: () => ({
            total_user: 0,
            total_molekul: 0,
            total_materi: 0,
            total_soal: 0,
            total_kategori: 0,
            total_role: 0,
        }),
        recentMolecules: () => [],
        recentMaterials: () => [],
        recentQuestions: () => [],
        recentCategories: () => [],
    },
);

const page = usePage();
const { can } = usePermission();

const currentUser = computed(() => page.props.auth?.user);

const formattedStats = computed(() => ({
    user: (props.stats?.total_user ?? 0).toLocaleString('id-ID'),
    molekul: (props.stats?.total_molekul ?? 0).toLocaleString('id-ID'),
    materi: (props.stats?.total_materi ?? 0).toLocaleString('id-ID'),
    soal: (props.stats?.total_soal ?? 0).toLocaleString('id-ID'),
    kategori: (props.stats?.total_kategori ?? 0).toLocaleString('id-ID'),
    role: (props.stats?.total_role ?? 0).toLocaleString('id-ID'),
}));

const getQuestionTypeLabel = (type: string) => {
    switch (type) {
        case 'multiple_choice':
            return 'PILIHAN GANDA';
        case 'multiple_select':
            return 'PILIHAN JAMAK';
        case 'true_false':
            return 'BENAR / SALAH';
        case 'short_answer':
            return 'ISIAN ANGKA';
        case 'matching':
            return 'MENJODOHKAN';
        default:
            return type.toUpperCase();
    }
};

const hasAnyShortcut = computed(() => {
    return (
        can('molecules.view') ||
        can('materials.view') ||
        can('questions.view') ||
        can('categories.view')
    );
});

const hasAnyStatCard = computed(() => {
    return (
        can('molecules.view') ||
        can('materials.view') ||
        can('questions.view') ||
        can('categories.view') ||
        can('users.view') ||
        can('roles.view')
    );
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="space-y-8">
            <!-- Header (Mirip dengan Desain Referensi) -->
            <div>
                <h1
                    class="font-serif text-2xl font-normal tracking-tight text-white sm:text-3xl lg:text-[34px]"
                >
                    Selamat datang, {{ currentUser?.name || 'Administrator' }}
                </h1>
                <p class="mt-1 text-xs text-slate-400 sm:text-sm">
                    Kelola konten dan materi pembelajaran Molecular Vision AR
                    (MVAR) dari sini.
                </p>
            </div>

            <!-- Metric Cards Grid (Hanya muncul jika memiliki permission view) -->
            <div
                v-if="hasAnyStatCard"
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <!-- Stat 1: MOLEKUL -->
                <Link
                    v-if="can('molecules.view')"
                    :href="route('molecules.index')"
                    class="group relative flex flex-col justify-between rounded-xl border border-[#14263b] bg-[#091624] p-5 shadow-lg transition-all duration-200 hover:border-[#1b344d] hover:bg-[#0c1a28]"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            MOLEKUL 3D
                        </span>
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-[#e5a824]/60"
                        ></span>
                    </div>
                    <div
                        class="mt-4 text-3xl font-normal tracking-tight text-white sm:text-4xl"
                    >
                        {{ formattedStats.molekul }}
                    </div>
                </Link>

                <!-- Stat 2: MATERI -->
                <Link
                    v-if="can('materials.view')"
                    :href="route('materials.index')"
                    class="group relative flex flex-col justify-between rounded-xl border border-[#14263b] bg-[#091624] p-5 shadow-lg transition-all duration-200 hover:border-[#1b344d] hover:bg-[#0c1a28]"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            MATERI PEMBELAJARAN
                        </span>
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-emerald-400/60"
                        ></span>
                    </div>
                    <div
                        class="mt-4 text-3xl font-normal tracking-tight text-white sm:text-4xl"
                    >
                        {{ formattedStats.materi }}
                    </div>
                </Link>

                <!-- Stat 3: BANK SOAL (Card Highlight Aksen) -->
                <Link
                    v-if="can('questions.view')"
                    :href="route('questions.index')"
                    class="group relative flex flex-col justify-between rounded-xl border border-[#e5a824]/30 bg-gradient-to-br from-[#1a2838] to-[#0c1a28] p-5 shadow-lg transition-all duration-200 hover:border-[#e5a824]/60"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold tracking-widest text-[#e5a824] uppercase"
                        >
                            BANK SOAL & KUIS
                        </span>
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-[#e5a824]"
                        ></span>
                    </div>
                    <div
                        class="mt-4 text-3xl font-normal tracking-tight text-[#e5a824] sm:text-4xl"
                    >
                        {{ formattedStats.soal }}
                    </div>
                </Link>

                <!-- Stat 4: KATEGORI KIMIA -->
                <Link
                    v-if="can('categories.view')"
                    :href="route('categories.index')"
                    class="group relative flex flex-col justify-between rounded-xl border border-[#14263b] bg-[#091624] p-5 shadow-lg transition-all duration-200 hover:border-[#1b344d] hover:bg-[#0c1a28]"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            KATEGORI KIMIA
                        </span>
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-cyan-400/60"
                        ></span>
                    </div>
                    <div
                        class="mt-4 text-3xl font-normal tracking-tight text-white sm:text-4xl"
                    >
                        {{ formattedStats.kategori }}
                    </div>
                </Link>

                <!-- Stat 5: TOTAL USERS -->
                <Link
                    v-if="can('users.view')"
                    :href="route('users.index')"
                    class="group relative flex flex-col justify-between rounded-xl border border-[#14263b] bg-[#091624] p-5 shadow-lg transition-all duration-200 hover:border-[#1b344d] hover:bg-[#0c1a28]"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            TOTAL PENGGUNA
                        </span>
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-blue-400/60"
                        ></span>
                    </div>
                    <div
                        class="mt-4 text-3xl font-normal tracking-tight text-white sm:text-4xl"
                    >
                        {{ formattedStats.user }}
                    </div>
                </Link>

                <!-- Stat 6: TOTAL ROLES -->
                <Link
                    v-if="can('roles.view')"
                    :href="route('roles.index')"
                    class="group relative flex flex-col justify-between rounded-xl border border-[#14263b] bg-[#091624] p-5 shadow-lg transition-all duration-200 hover:border-[#1b344d] hover:bg-[#0c1a28]"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            HAK AKSES / ROLE
                        </span>
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-purple-400/60"
                        ></span>
                    </div>
                    <div
                        class="mt-4 text-3xl font-normal tracking-tight text-white sm:text-4xl"
                    >
                        {{ formattedStats.role }}
                    </div>
                </Link>
            </div>

            <!-- Shortcut Panels Section (Persis Layout Gambar: 2 Kolom berdampingan) -->
            <div
                v-if="hasAnyShortcut"
                class="grid grid-cols-1 gap-6 lg:grid-cols-2"
            >
                <!-- Panel 1: MOLEKUL TERBARU (Shortcut + Recent Items) -->
                <div
                    v-if="can('molecules.view')"
                    class="rounded-2xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <!-- Header Panel: Title + Shortcut Tambah -->
                    <div
                        class="flex items-center justify-between border-b border-[#14263b] pb-4"
                    >
                        <Link
                            :href="route('molecules.index')"
                            class="group inline-flex items-center gap-2 font-serif text-lg text-white transition hover:text-[#e5a824]"
                        >
                            <span>Molekul Terbaru</span>
                            <ChevronRight
                                class="h-4 w-4 opacity-50 transition group-hover:translate-x-0.5 group-hover:opacity-100"
                            />
                        </Link>

                        <!-- Shortcut Tambah Baru -->
                        <Link
                            v-if="can('molecules.create')"
                            :href="route('molecules.create')"
                            class="inline-flex items-center gap-1 rounded-full border border-[#1b344d] bg-[#0c1a28] px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-[#e5a824]/50 hover:bg-[#14263b] hover:text-[#e5a824]"
                        >
                            <Plus class="h-3 w-3" />
                            <span>Tambah</span>
                        </Link>
                    </div>

                    <!-- List of Recent Molecules -->
                    <div class="divide-y divide-[#14263b]/70">
                        <div
                            v-for="mol in recentMolecules"
                            :key="mol.id_molecule"
                            class="flex items-center justify-between rounded-lg px-2 py-3.5 transition hover:bg-[#0c1a28]/40"
                        >
                            <Link
                                :href="
                                    can('molecules.edit')
                                        ? route(
                                              'molecules.edit',
                                              mol.id_molecule,
                                          )
                                        : route('molecules.index')
                                "
                                class="truncate pr-3 text-xs font-medium text-slate-200 hover:text-white sm:text-sm"
                            >
                                {{ mol.name }}
                                <span
                                    v-if="mol.formula"
                                    class="font-mono text-xs text-slate-400"
                                >
                                    ({{ mol.formula }})
                                </span>
                            </Link>

                            <!-- Badge Capsule Status (seperti COMPLETED di gambar) -->
                            <span
                                class="flex-shrink-0 rounded-full border border-[#1b344d] bg-[#06121f] px-2.5 py-0.5 font-mono text-[10px] font-bold tracking-wider text-slate-300 uppercase"
                            >
                                {{ mol.shape || '3D AR' }}
                            </span>
                        </div>

                        <!-- Empty state if 0 records -->
                        <div
                            v-if="recentMolecules.length === 0"
                            class="py-6 text-center text-xs text-slate-500"
                        >
                            Belum ada molekul yang tersimpan.
                        </div>
                    </div>
                </div>

                <!-- Panel 2: MATERI PEMBELAJARAN TERBARU -->
                <div
                    v-if="can('materials.view')"
                    class="rounded-2xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <!-- Header Panel: Title + Shortcut Tambah -->
                    <div
                        class="flex items-center justify-between border-b border-[#14263b] pb-4"
                    >
                        <Link
                            :href="route('materials.index')"
                            class="group inline-flex items-center gap-2 font-serif text-lg text-white transition hover:text-[#e5a824]"
                        >
                            <span>Materi Pembelajaran Terbaru</span>
                            <ChevronRight
                                class="h-4 w-4 opacity-50 transition group-hover:translate-x-0.5 group-hover:opacity-100"
                            />
                        </Link>

                        <!-- Shortcut Tambah Baru -->
                        <Link
                            v-if="can('materials.create')"
                            :href="route('materials.create')"
                            class="inline-flex items-center gap-1 rounded-full border border-[#1b344d] bg-[#0c1a28] px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-emerald-500/50 hover:bg-[#14263b] hover:text-emerald-400"
                        >
                            <Plus class="h-3 w-3" />
                            <span>Tambah</span>
                        </Link>
                    </div>

                    <!-- List of Recent Materials -->
                    <div class="divide-y divide-[#14263b]/70">
                        <div
                            v-for="mat in recentMaterials"
                            :key="mat.id_material"
                            class="flex items-center justify-between rounded-lg px-2 py-3.5 transition hover:bg-[#0c1a28]/40"
                        >
                            <Link
                                :href="
                                    can('materials.edit')
                                        ? route(
                                              'materials.edit',
                                              mat.id_material,
                                          )
                                        : route('materials.index')
                                "
                                class="truncate pr-3 text-xs font-medium text-slate-200 hover:text-white sm:text-sm"
                            >
                                {{ mat.name }}
                            </Link>

                            <!-- Badge Capsule Category -->
                            <span
                                class="flex-shrink-0 rounded-full border border-[#1b344d] bg-[#06121f] px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-slate-300 uppercase"
                            >
                                {{ mat.category?.name || 'UMUM' }}
                            </span>
                        </div>

                        <!-- Empty state if 0 records -->
                        <div
                            v-if="recentMaterials.length === 0"
                            class="py-6 text-center text-xs text-slate-500"
                        >
                            Belum ada materi pembelajaran yang dibuat.
                        </div>
                    </div>
                </div>

                <!-- Panel 3: BANK SOAL & KUIS TERBARU -->
                <div
                    v-if="can('questions.view')"
                    class="rounded-2xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <!-- Header Panel: Title + Shortcut Tambah -->
                    <div
                        class="flex items-center justify-between border-b border-[#14263b] pb-4"
                    >
                        <Link
                            :href="route('questions.index')"
                            class="group inline-flex items-center gap-2 font-serif text-lg text-white transition hover:text-[#e5a824]"
                        >
                            <span>Bank Soal & Kuis Terbaru</span>
                            <ChevronRight
                                class="h-4 w-4 opacity-50 transition group-hover:translate-x-0.5 group-hover:opacity-100"
                            />
                        </Link>

                        <!-- Shortcut Tambah Baru -->
                        <Link
                            v-if="can('questions.create')"
                            :href="route('questions.create')"
                            class="inline-flex items-center gap-1 rounded-full border border-[#1b344d] bg-[#0c1a28] px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-purple-500/50 hover:bg-[#14263b] hover:text-purple-400"
                        >
                            <Plus class="h-3 w-3" />
                            <span>Tambah</span>
                        </Link>
                    </div>

                    <!-- List of Recent Questions -->
                    <div class="divide-y divide-[#14263b]/70">
                        <div
                            v-for="q in recentQuestions"
                            :key="q.id_question"
                            class="flex items-center justify-between rounded-lg px-2 py-3.5 transition hover:bg-[#0c1a28]/40"
                        >
                            <Link
                                :href="
                                    can('questions.edit')
                                        ? route('questions.edit', q.id_question)
                                        : route('questions.index')
                                "
                                class="truncate pr-3 text-xs font-medium text-slate-200 hover:text-white sm:text-sm"
                                :title="q.name"
                            >
                                {{ q.name }}
                            </Link>

                            <!-- Badge Capsule Format -->
                            <span
                                class="flex-shrink-0 rounded-full border border-[#1b344d] bg-[#06121f] px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-slate-300 uppercase"
                            >
                                {{ getQuestionTypeLabel(q.question_type) }}
                            </span>
                        </div>

                        <!-- Empty state if 0 records -->
                        <div
                            v-if="recentQuestions.length === 0"
                            class="py-6 text-center text-xs text-slate-500"
                        >
                            Belum ada butir soal kuis yang tersimpan.
                        </div>
                    </div>
                </div>

                <!-- Panel 4: KATEGORI KIMIA TERBARU -->
                <div
                    v-if="can('categories.view')"
                    class="rounded-2xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <!-- Header Panel: Title + Shortcut Buka -->
                    <div
                        class="flex items-center justify-between border-b border-[#14263b] pb-4"
                    >
                        <Link
                            :href="route('categories.index')"
                            class="group inline-flex items-center gap-2 font-serif text-lg text-white transition hover:text-[#e5a824]"
                        >
                            <span>Kategori Pembelajaran</span>
                            <ChevronRight
                                class="h-4 w-4 opacity-50 transition group-hover:translate-x-0.5 group-hover:opacity-100"
                            />
                        </Link>

                        <Link
                            :href="route('categories.index')"
                            class="inline-flex items-center gap-1 rounded-full border border-[#1b344d] bg-[#0c1a28] px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-cyan-500/50 hover:bg-[#14263b] hover:text-cyan-400"
                        >
                            <span>Kelola</span>
                            <ArrowRight class="h-3 w-3" />
                        </Link>
                    </div>

                    <!-- List of Categories -->
                    <div class="divide-y divide-[#14263b]/70">
                        <div
                            v-for="cat in recentCategories"
                            :key="cat.id_category"
                            class="flex items-center justify-between rounded-lg px-2 py-3.5 transition hover:bg-[#0c1a28]/40"
                        >
                            <span
                                class="truncate pr-3 text-xs font-medium text-slate-200 sm:text-sm"
                            >
                                {{ cat.name }}
                            </span>

                            <!-- Badge Capsule Counts -->
                            <span
                                class="flex-shrink-0 rounded-full border border-[#1b344d] bg-[#06121f] px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-slate-300 uppercase"
                            >
                                {{ cat.materials_count || 0 }} MATERI
                            </span>
                        </div>

                        <!-- Empty state if 0 records -->
                        <div
                            v-if="recentCategories.length === 0"
                            class="py-6 text-center text-xs text-slate-500"
                        >
                            Belum ada kategori yang dibuat.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fallback jika user tidak punya permission apapun -->
            <div
                v-else
                class="rounded-2xl border border-[#14263b] bg-[#091624] p-8 text-center"
            >
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-[#1b344d] bg-[#0c1a28] text-slate-400"
                >
                    <Sparkles class="h-6 w-6 text-[#e5a824]" />
                </div>
                <h3 class="mt-3 text-sm font-semibold text-white">
                    Selamat Datang di Portal Pembelajaran MVAR
                </h3>
                <p class="mx-auto mt-1 max-w-sm text-xs text-slate-400">
                    Gunakan menu navigasi untuk melihat progres pembelajaran
                    atau materi kimia yang tersedia.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
