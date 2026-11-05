<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Atom,
    BookOpen,
    Camera,
    CheckCircle2,
    Copy,
    Database,
    ExternalLink,
    FileText,
    Globe,
    GraduationCap,
    Info,
    Lock,
    Mail,
    Printer,
    RefreshCw,
    Server,
    ShieldCheck,
    Smartphone,
    Sparkles,
    Trash2,
    UserCheck,
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const currentLang = ref<'id' | 'en'>('id');
const activeSection = ref<string>('ringkasan');
const copied = ref<boolean>(false);

const toggleLang = (lang: 'id' | 'en') => {
    currentLang.value = lang;
};

const handleCopyLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch {
        // Fallback jika navigator clipboard tidak diizinkan
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    }
};

const sections = computed(() => {
    if (currentLang.value === 'id') {
        return [
            { id: 'ringkasan', label: '1. Ringkasan Kebijakan' },
            { id: 'data-dikumpulkan', label: '2. Data yang Dikumpulkan' },
            { id: 'kamera-ar', label: '3. Izin Kamera & Teknologi AR' },
            { id: 'tujuan-penggunaan', label: '4. Tujuan Pengolahan Data' },
            { id: 'keamanan-data', label: '5. Keamanan & Penyimpanan' },
            { id: 'privasi-pelajar', label: '6. Perlindungan Privasi Pelajar' },
            { id: 'hak-pengguna', label: '7. Hak Akses & Penghapusan Akun' },
            { id: 'pembaruan-kontak', label: '8. Pembaruan & Kontak Dukungan' },
        ];
    }
    return [
        { id: 'ringkasan', label: '1. Policy Overview' },
        { id: 'data-dikumpulkan', label: '2. Data We Collect' },
        { id: 'kamera-ar', label: '3. Camera & AR Technology' },
        { id: 'tujuan-penggunaan', label: '4. Purposes of Processing' },
        { id: 'keamanan-data', label: '5. Security & Retention' },
        { id: 'privasi-pelajar', label: '6. Student & Children Privacy' },
        { id: 'hak-pengguna', label: '7. User Rights & Account Deletion' },
        { id: 'pembaruan-kontak', label: '8. Policy Updates & Contact' },
    ];
});

const onScroll = () => {
    const scrollPosition = window.scrollY + 140;
    for (const section of sections.value) {
        const element = document.getElementById(section.id);
        if (element) {
            const top = element.offsetTop;
            const height = element.offsetHeight;
            if (scrollPosition >= top && scrollPosition < top + height) {
                activeSection.value = section.id;
                break;
            }
        }
    }
};

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});
</script>

<template>
    <Head>
        <title>
            {{ currentLang === 'id' ? 'Kebijakan Privasi' : 'Privacy Policy' }}
            - Molecular Vision AR
        </title>
        <meta
            name="description"
            content="Kebijakan Privasi Molecular Vision AR (MVAR) - Media Pembelajaran Kimia Interaktif & Sistem Manajemen Konten Pendidikan."
        />
    </Head>

    <div
        class="relative min-h-screen overflow-hidden bg-[#07111b] font-sans text-slate-200 selection:bg-[#e8a917] selection:text-[#07111b]"
    >
        <!-- Background Ambient Glow & Circular Shapes -->
        <div
            class="pointer-events-none fixed -top-36 -right-36 h-[500px] w-[500px] rounded-full bg-cyan-500/5 blur-[120px]"
        />
        <div
            class="pointer-events-none fixed top-1/3 -left-48 h-[600px] w-[600px] rounded-full bg-[#0e2338]/80 blur-[140px]"
        />
        <div
            class="pointer-events-none fixed right-1/4 -bottom-36 h-[550px] w-[550px] rounded-full bg-[#e8a917]/5 blur-[130px]"
        />

        <!-- Header Navbar -->
        <header
            class="sticky top-0 z-50 border-b border-[#1b344d]/70 bg-[#07111b]/85 backdrop-blur-md"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8"
            >
                <!-- Brand Logo & App Name -->
                <div class="flex items-center gap-3">
                    <img
                        src="/MVAR.png"
                        alt="MVAR Logo"
                        class="h-9 w-auto object-contain sm:h-10"
                    />
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-base font-bold tracking-tight text-white sm:text-lg"
                            >
                                Molecular Vision AR
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400">
                            {{
                                currentLang === 'id'
                                    ? 'Media Edukasi Kimia Interaktif'
                                    : 'Interactive Chemistry Education'
                            }}
                        </span>
                    </div>
                </div>

                <!-- Right Actions: Language Switcher & Login Link -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Language Switcher Pill -->
                    <div
                        class="flex items-center rounded-lg border border-[#1b344d] bg-[#0c1a28] p-0.5 text-xs"
                    >
                        <button
                            type="button"
                            @click="toggleLang('id')"
                            :class="[
                                'flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-medium transition-all',
                                currentLang === 'id'
                                    ? 'bg-[#e8a917] font-semibold text-[#07111b] shadow-sm'
                                    : 'text-slate-400 hover:text-white',
                            ]"
                            aria-label="Ganti Bahasa Indonesia"
                        >
                            <span>🇮🇩</span>
                            <span>ID</span>
                        </button>
                        <button
                            type="button"
                            @click="toggleLang('en')"
                            :class="[
                                'flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-medium transition-all',
                                currentLang === 'en'
                                    ? 'bg-[#e8a917] font-semibold text-[#07111b] shadow-sm'
                                    : 'text-slate-400 hover:text-white',
                            ]"
                            aria-label="Switch to English"
                        >
                            <span>🇬🇧</span>
                            <span>EN</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section
            class="relative border-b border-[#1b344d]/60 bg-gradient-to-b from-[#0e2338]/90 via-[#0a1b2b]/80 to-[#07111b] py-12 sm:py-16"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <!-- Status Badge -->
                    <div
                        class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-400"
                    >
                        <ShieldCheck class="h-3.5 w-3.5" />
                        <span>
                            {{
                                currentLang === 'id'
                                    ? 'Kebijakan Resmi & Transparansi Data Pengguna'
                                    : 'Official Policy & User Data Transparency'
                            }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1
                        class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl"
                    >
                        {{
                            currentLang === 'id'
                                ? 'Kebijakan Privasi'
                                : 'Privacy Policy'
                        }}
                    </h1>
                    <p class="mt-2 text-lg font-medium text-[#e8a917]">
                        Molecular Vision AR (MVAR)
                    </p>

                    <!-- Subtitle -->
                    <p
                        class="mt-4 text-sm leading-relaxed text-slate-300 sm:text-base"
                    >
                        <template v-if="currentLang === 'id'">
                            Kebijakan Privasi ini menjelaskan bagaimana platform
                            <strong>Molecular Vision AR</strong> (meliputi
                            aplikasi seluler Android/iOS dan Content Management
                            System berbasis web) mengumpulkan, menggunakan,
                            menyimpan, serta melindungi informasi pribadi
                            pengguna, khususnya dalam pemanfaatan teknologi
                            <strong>Augmented Reality (AR)</strong> dan media
                            pembelajaran kimia.
                        </template>
                        <template v-else>
                            This Privacy Policy explains how the
                            <strong>Molecular Vision AR</strong> platform
                            (including the Android/iOS mobile application and
                            the web-based Content Management System) collects,
                            uses, stores, and protects user personal
                            information, specifically regarding the use of
                            <strong>Augmented Reality (AR)</strong> technology
                            and educational chemistry resources.
                        </template>
                    </p>

                    <!-- Meta & Action Badges -->
                    <div
                        class="mt-6 flex flex-wrap items-center gap-3 text-xs text-slate-400"
                    >
                        <div
                            class="flex items-center gap-1.5 rounded-md border border-[#1b344d] bg-[#0c1a28] px-2.5 py-1"
                        >
                            <span class="text-slate-500">{{
                                currentLang === 'id'
                                    ? 'Terakhir Diperbarui:'
                                    : 'Last Updated:'
                            }}</span>
                            <span class="font-semibold text-slate-300"
                                >5 November 2026</span
                            >
                        </div>
                        <div
                            class="flex items-center gap-1.5 rounded-md border border-[#1b344d] bg-[#0c1a28] px-2.5 py-1"
                        >
                            <span class="text-slate-500">{{
                                currentLang === 'id'
                                    ? 'Versi Dokumen:'
                                    : 'Document Version:'
                            }}</span>
                            <span class="font-semibold text-[#e8a917]"
                                >1.1 (Student Safe)</span
                            >
                        </div>

                        <!-- Print & Copy Link Buttons -->
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="handleCopyLink"
                                class="inline-flex items-center gap-1 rounded-md border border-[#1b344d] bg-[#0c1a28] px-2.5 py-1 text-slate-300 transition-colors hover:border-slate-500 hover:text-white"
                                title="Salin Tautan"
                            >
                                <CheckCircle2
                                    v-if="copied"
                                    class="h-3 w-3 text-emerald-400"
                                />
                                <Copy v-else class="h-3 w-3" />
                                <span :class="{ 'text-emerald-400': copied }">
                                    {{
                                        copied
                                            ? currentLang === 'id'
                                                ? 'Tersalin!'
                                                : 'Copied!'
                                            : currentLang === 'id'
                                              ? 'Salin Link'
                                              : 'Copy Link'
                                    }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Area with Sticky Sidebar -->
        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Left Column: Sticky Table of Contents (Desktop) -->
                <aside class="hidden lg:col-span-4 lg:block">
                    <div
                        class="sticky top-24 rounded-xl border border-[#1b344d] bg-[#0e2338]/90 p-5 shadow-lg backdrop-blur"
                    >
                        <div
                            class="mb-4 flex items-center gap-2 text-xs font-bold tracking-wider text-[#e8a917] uppercase"
                        >
                            <BookOpen class="h-4 w-4" />
                            <span>{{
                                currentLang === 'id'
                                    ? 'Daftar Isi Kebijakan'
                                    : 'Table of Contents'
                            }}</span>
                        </div>
                        <nav class="space-y-1 text-xs">
                            <a
                                v-for="sec in sections"
                                :key="sec.id"
                                :href="`#${sec.id}`"
                                :class="[
                                    'block rounded-lg px-3 py-2 font-medium transition-all',
                                    activeSection === sec.id
                                        ? 'border-l-2 border-[#e8a917] bg-[#e8a917]/15 font-semibold text-[#e8a917]'
                                        : 'text-slate-400 hover:bg-[#132c45] hover:text-slate-200',
                                ]"
                            >
                                {{ sec.label }}
                            </a>
                        </nav>

                        <!-- Quick Summary Callout -->
                        <div
                            class="mt-6 border-t border-[#1b344d] pt-4 text-xs"
                        >
                            <div
                                class="flex items-center gap-1.5 font-semibold text-white"
                            >
                                <Sparkles class="h-3.5 w-3.5 text-[#e8a917]" />
                                <span>{{
                                    currentLang === 'id'
                                        ? 'Komitmen Kami'
                                        : 'Our Promise'
                                }}</span>
                            </div>
                            <p
                                class="mt-1 text-[11px] leading-relaxed text-slate-400"
                            >
                                {{
                                    currentLang === 'id'
                                        ? 'MVAR tidak menjual data ke pihak ketiga dan tidak menyisipkan iklan pelacak komersial.'
                                        : 'MVAR does not sell data to third parties and contains zero commercial advertising trackers.'
                                }}
                            </p>
                        </div>
                    </div>
                </aside>

                <!-- Right Column: Policy Content -->
                <article class="space-y-10 lg:col-span-8">
                    <!-- SECTION 1: RINGKASAN & RUANG LINGKUP -->
                    <section
                        id="ringkasan"
                        class="rounded-xl border border-[#1b344d] bg-[#0e2338]/80 p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-500/20 bg-blue-500/10 text-blue-400"
                            >
                                <Info class="h-5 w-5" />
                            </div>
                            <div>
                                <h2
                                    class="text-xl font-bold text-white sm:text-2xl"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? '1. Ringkasan & Ruang Lingkup'
                                            : '1. Overview & Scope'
                                    }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Cakupan platform dan identitas pengelola'
                                            : 'Platform scope and management identity'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 space-y-4 text-sm leading-relaxed text-slate-300"
                        >
                            <p>
                                <template v-if="currentLang === 'id'">
                                    Selamat datang di
                                    <strong>Molecular Vision AR (MVAR)</strong>.
                                    MVAR merupakan ekosistem pendidikan kimia
                                    terintegrasi yang terdiri atas:
                                </template>
                                <template v-else>
                                    Welcome to
                                    <strong>Molecular Vision AR (MVAR)</strong>.
                                    MVAR is an integrated chemistry educational
                                    ecosystem consisting of:
                                </template>
                            </p>
                            <ul class="list-disc space-y-2 pl-5 text-slate-300">
                                <li>
                                    <strong
                                        >{{
                                            currentLang === 'id'
                                                ? 'Aplikasi Seluler MVAR (Mobile App)'
                                                : 'MVAR Mobile App'
                                        }}:</strong
                                    >
                                    {{
                                        currentLang === 'id'
                                            ? 'Aplikasi seluler (Android / iOS) yang digunakan oleh siswa dan pendidik untuk mengeksplorasi materi kimia, visualisasi molekul 3D dengan Augmented Reality (AR), serta mengerjakan kuis dan latihan pemahaman.'
                                            : 'A mobile application (Android / iOS) utilized by students and educators to explore chemistry topics, visualize 3D molecules via Augmented Reality (AR), and complete quizzes/practice exercises.'
                                    }}
                                </li>
                                <li>
                                    <strong
                                        >{{
                                            currentLang === 'id'
                                                ? 'MVAR CMS (Content Management System)'
                                                : 'MVAR Web CMS'
                                        }}:</strong
                                    >
                                    {{
                                        currentLang === 'id'
                                            ? 'Dasbor berbasis web yang digunakan oleh tenaga pendidik dan administrator untuk mengelola konten kurikulum, aset molekul 3D, bank soal, serta memantau analitik akademik.'
                                            : 'A web-based portal utilized by educators and administrators to curate curriculum content, 3D molecular assets, question banks, and monitor academic progress.'
                                    }}
                                </li>
                            </ul>
                            <p>
                                <template v-if="currentLang === 'id'">
                                    Dengan mengunduh, mengakses, atau
                                    menggunakan aplikasi MVAR dan situs web ini,
                                    Anda menyatakan telah membaca, memahami, dan
                                    menyetujui praktik perlindungan data yang
                                    tercantum dalam dokumen ini.
                                </template>
                                <template v-else>
                                    By downloading, accessing, or using the MVAR
                                    mobile application and this website, you
                                    acknowledge that you have read, understood,
                                    and agreed to the data practices described
                                    in this document.
                                </template>
                            </p>
                        </div>
                    </section>

                    <!-- SECTION 2: DATA YANG DIKUMPULKAN -->
                    <section
                        id="data-dikumpulkan"
                        class="rounded-xl border border-[#1b344d] bg-[#0e2338]/80 p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 text-emerald-400"
                            >
                                <Database class="h-5 w-5" />
                            </div>
                            <div>
                                <h2
                                    class="text-xl font-bold text-white sm:text-2xl"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? '2. Data yang Kami Kumpulkan'
                                            : '2. Data We Collect'
                                    }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Jenis data yang diproses demi fungsi pembelajaran'
                                            : 'Data categories processed for educational functionality'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 space-y-4 text-sm leading-relaxed text-slate-300"
                        >
                            <p>
                                <template v-if="currentLang === 'id'">
                                    Kami menerapkan prinsip
                                    <em>data minimization</em> (meminimalkan
                                    data): kami hanya mengumpulkan data yang
                                    benar-benar esensial untuk mendukung proses
                                    belajar mengajar.
                                </template>
                                <template v-else>
                                    We adhere to strict data minimization
                                    principles: we only collect information
                                    strictly essential to supporting your
                                    learning experience.
                                </template>
                            </p>

                            <!-- Data Cards Grid -->
                            <div
                                class="grid grid-cols-1 gap-4 pt-2 sm:grid-cols-2"
                            >
                                <!-- Card A: Akun & Identitas -->
                                <div
                                    class="rounded-lg border border-[#1b344d] bg-[#0a1b2b] p-4"
                                >
                                    <div
                                        class="flex items-center gap-2 text-sm font-semibold text-[#e8a917]"
                                    >
                                        <UserCheck class="h-4 w-4" />
                                        <span>{{
                                            currentLang === 'id'
                                                ? 'Data Akun & Autentikasi'
                                                : 'Account & Auth Data'
                                        }}</span>
                                    </div>
                                    <ul
                                        class="mt-2 list-disc space-y-1.5 pl-4 text-xs text-slate-300"
                                    >
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Nama Pengguna / Lengkap'
                                                    : 'Full Name / Display Name'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Alamat Email (untuk login & verifikasi)'
                                                    : 'Email address (for login & verification)'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Kata Sandi (disimpan dalam bentuk Hash terenkripsi aman)'
                                                    : 'Password (stored securely via one-way hash)'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Jenjang Pendidikan / Tingkat Kelas (Degree)'
                                                    : 'Educational Grade / Degree Level'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Peran Pengguna (Admin, Guru, Siswa)'
                                                    : 'User Role (Admin, Teacher, Student)'
                                            }}
                                        </li>
                                    </ul>
                                </div>

                                <!-- Card B: Data Aktivitas Belajar -->
                                <div
                                    class="rounded-lg border border-[#1b344d] bg-[#0a1b2b] p-4"
                                >
                                    <div
                                        class="flex items-center gap-2 text-sm font-semibold text-emerald-400"
                                    >
                                        <GraduationCap class="h-4 w-4" />
                                        <span>{{
                                            currentLang === 'id'
                                                ? 'Progres Belajar & Evaluasi'
                                                : 'Learning Progress & Scores'
                                        }}</span>
                                    </div>
                                    <ul
                                        class="mt-2 list-disc space-y-1.5 pl-4 text-xs text-slate-300"
                                    >
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Modul materi kimia yang telah dibaca'
                                                    : 'Completed chemistry learning modules'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Riwayat interaksi model molekul 3D'
                                                    : 'Viewed 3D molecular structures'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Skor kuis, jumlah benar/salah, dan riwayat latihan'
                                                    : 'Quiz scores, correct answers & exercise history'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Waktu penyelesaian modul'
                                                    : 'Completion timestamp and achievements'
                                            }}
                                        </li>
                                    </ul>
                                </div>

                                <!-- Card C: Data Teknis & Perangkat -->
                                <div
                                    class="rounded-lg border border-[#1b344d] bg-[#0a1b2b] p-4"
                                >
                                    <div
                                        class="flex items-center gap-2 text-sm font-semibold text-cyan-400"
                                    >
                                        <Smartphone class="h-4 w-4" />
                                        <span>{{
                                            currentLang === 'id'
                                                ? 'Data Perangkat & Diagnostik'
                                                : 'Device & Diagnostics'
                                        }}</span>
                                    </div>
                                    <ul
                                        class="mt-2 list-disc space-y-1.5 pl-4 text-xs text-slate-300"
                                    >
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Model perangkat & versi Sistem Operasi (Android / iOS)'
                                                    : 'Device model & Operating System version'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Versi aplikasi MVAR yang terpasang'
                                                    : 'Installed MVAR app version'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'Log galat (Crash logs) saat terjadi kegagalan sistem'
                                                    : 'Crash logs and technical error reports'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'IP Address sementara untuk komunikasi jaringan API'
                                                    : 'Transient IP address for API communication'
                                            }}
                                        </li>
                                    </ul>
                                </div>

                                <!-- Card D: Data yang TIDAK Dikumpulkan -->
                                <div
                                    class="rounded-lg border border-rose-500/30 bg-[#0a1b2b] p-4"
                                >
                                    <div
                                        class="flex items-center gap-2 text-sm font-semibold text-rose-400"
                                    >
                                        <Lock class="h-4 w-4" />
                                        <span>{{
                                            currentLang === 'id'
                                                ? 'Data yang TIDAK Pernah Kami Kumpulkan'
                                                : 'Data We NEVER Collect'
                                        }}</span>
                                    </div>
                                    <ul
                                        class="mt-2 list-disc space-y-1.5 pl-4 text-xs text-slate-300"
                                    >
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'TIDAK mengumpulkan data lokasi GPS pengguna'
                                                    : 'NO GPS real-time location tracking'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'TIDAK mengakses kontak telepon atau SMS'
                                                    : 'NO contacts or SMS read access'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'TIDAK mengumpulkan data keuangan / kartu kredit'
                                                    : 'NO financial or credit card data'
                                            }}
                                        </li>
                                        <li>
                                            {{
                                                currentLang === 'id'
                                                    ? 'TIDAK mengumpulkan data biometrik wajah'
                                                    : 'NO facial biometric or recognition data'
                                            }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 3: IZIN KAMERA & AR (HIGHLIGHT UTAMA) -->
                    <section
                        id="kamera-ar"
                        class="relative overflow-hidden rounded-xl border-2 border-[#e8a917]/50 bg-gradient-to-b from-[#13283d] to-[#0e2338] p-6 shadow-xl sm:p-8"
                    >
                        <!-- Corner badge -->
                        <div
                            class="absolute top-6 -right-12 rotate-45 bg-[#e8a917] px-12 py-1 text-[10px] font-bold text-[#07111b] uppercase shadow"
                        >
                            AR Safety
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl border border-[#e8a917]/30 bg-[#e8a917]/15 text-[#e8a917]"
                            >
                                <Camera class="h-6 w-6" />
                            </div>
                            <div>
                                <h2
                                    class="flex items-center gap-2 text-xl font-bold text-white sm:text-2xl"
                                >
                                    <span>{{
                                        currentLang === 'id'
                                            ? '3. Izin Kamera & Augmented Reality (AR)'
                                            : '3. Camera Permission & Augmented Reality'
                                    }}</span>
                                </h2>
                                <p class="text-xs text-[#e8a917]">
                                    {{
                                        currentLang === 'id'
                                            ? 'Pemberitahuan Khusus Kepatuhan Google Play & Apple App Store'
                                            : 'Dedicated Notice for Google Play & Apple App Store Guidelines'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-6 space-y-4 text-sm leading-relaxed text-slate-200"
                        >
                            <!-- Important Callout Banner -->
                            <div
                                class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 p-4"
                            >
                                <div class="flex items-start gap-3">
                                    <CheckCircle2
                                        class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400"
                                    />
                                    <div>
                                        <p
                                            class="text-sm font-bold text-emerald-300"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Pemrosesan Kamera Bersifat 100% Real-Time & Lokal di Perangkat (On-Device)'
                                                    : 'Camera Feed is Processed 100% Real-Time & On-Device'
                                            }}
                                        </p>
                                        <p
                                            class="mt-1 text-xs leading-normal text-slate-300"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Aplikasi MVAR TIDAK PERNAH menyimpan, merekam, memotret diam-diam, atau mengirimkan gambar/video dari kamera pengguna ke server backend atau pihak ketiga manapun.'
                                                    : 'The MVAR app NEVER stores, records, captures covertly, or transmits video frames/images from your camera to our servers or any third-party services.'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <p>
                                <template v-if="currentLang === 'id'">
                                    Aplikasi seluler Molecular Vision AR
                                    memerlukan izin kamera
                                    (<code>android.permission.CAMERA</code>)
                                    semata-mata untuk menjalankan fitur
                                    <strong>Augmented Reality (Unity AR)</strong
                                    >:
                                </template>
                                <template v-else>
                                    The Molecular Vision AR mobile app requests
                                    camera access
                                    (<code>android.permission.CAMERA</code>)
                                    strictly to enable its interactive
                                    <strong
                                        >Augmented Reality (Unity AR)</strong
                                    >
                                    functionality:
                                </template>
                            </p>

                            <div class="space-y-3 pt-1">
                                <div
                                    class="flex items-start gap-3 rounded-lg border border-[#1b344d] bg-[#07111b]/60 p-3"
                                >
                                    <Atom
                                        class="mt-0.5 h-5 w-5 shrink-0 text-[#e8a917]"
                                    />
                                    <div>
                                        <strong
                                            class="block text-xs text-white sm:text-sm"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Deteksi Marker & Proyeksi Molekul 3D'
                                                    : 'Marker Detection & 3D Molecule Projection'
                                            }}
                                        </strong>
                                        <p
                                            class="mt-0.5 text-xs text-slate-400"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Kamera digunakan untuk mendeteksi penanda fisik kartu pembelajaran kimia atau bidang datar di ruangan untuk menempatkan model molekul 3D secara visual.'
                                                    : 'The camera feed detects physical chemistry marker cards or flat room surfaces to render 3D chemical molecules in real-world space.'
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex items-start gap-3 rounded-lg border border-[#1b344d] bg-[#07111b]/60 p-3"
                                >
                                    <Lock
                                        class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400"
                                    />
                                    <div>
                                        <strong
                                            class="block text-xs text-white sm:text-sm"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Tidak Ada Pengenalan Wajah / Data Biometrik'
                                                    : 'No Facial Recognition or Biometrics'
                                            }}
                                        </strong>
                                        <p
                                            class="mt-0.5 text-xs text-slate-400"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Sistem AR tidak memindai wajah pengguna dan tidak mengidentifikasi karakteristik biometrik individu.'
                                                    : 'The AR system does not scan faces or identify individual biometric markers.'
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex items-start gap-3 rounded-lg border border-[#1b344d] bg-[#07111b]/60 p-3"
                                >
                                    <Server
                                        class="mt-0.5 h-5 w-5 shrink-0 text-cyan-400"
                                    />
                                    <div>
                                        <strong
                                            class="block text-xs text-white sm:text-sm"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Penyimpanan Lokal & Akses Jaringan'
                                                    : 'Local Storage Cache & Network Access'
                                            }}
                                        </strong>
                                        <p
                                            class="mt-0.5 text-xs text-slate-400"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Izin penyimpanan (Storage) hanya digunakan untuk mengunduh cache model molekul 3D agar siswa dapat belajar tanpa boros kuota. Izin internet digunakan untuk mengambil pembaruan materi dari CMS.'
                                                    : 'Storage access is exclusively used for caching 3D molecule assets locally to conserve bandwidth. Internet access syncs curriculum data from the CMS.'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 4: TUJUAN PENGOLAHAN DATA -->
                    <section
                        id="tujuan-penggunaan"
                        class="rounded-xl border border-[#1b344d] bg-[#0e2338]/80 p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-amber-500/20 bg-amber-500/10 text-amber-400"
                            >
                                <CheckCircle2 class="h-5 w-5" />
                            </div>
                            <div>
                                <h2
                                    class="text-xl font-bold text-white sm:text-2xl"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? '4. Tujuan Pengolahan Data'
                                            : '4. Purposes of Data Processing'
                                    }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Alasan pemrosesan data untuk kepentingan pendidikan'
                                            : 'Why we process data for academic enhancement'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 space-y-3 text-sm leading-relaxed text-slate-300"
                        >
                            <p>
                                {{
                                    currentLang === 'id'
                                        ? 'Data yang kami peroleh digunakan untuk tujuan-tujuan berikut:'
                                        : 'We utilize collected information solely for the following legitimate purposes:'
                                }}
                            </p>
                            <div
                                class="grid grid-cols-1 gap-3 pt-1 sm:grid-cols-2"
                            >
                                <div
                                    class="rounded-lg border border-[#1b344d]/70 bg-[#0a1b2b] p-3.5 text-xs"
                                >
                                    <strong
                                        class="mb-1 block font-semibold text-white"
                                    >
                                        {{
                                            currentLang === 'id'
                                                ? 'Autentikasi Akun Pembelajaran'
                                                : 'User Authentication'
                                        }}
                                    </strong>
                                    <span class="text-slate-400">
                                        {{
                                            currentLang === 'id'
                                                ? 'Memvalidasi kredensial pengguna agar siswa dan guru dapat masuk ke profil pribadinya secara aman.'
                                                : 'Validating credentials so learners and educators can access their customized profiles securely.'
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="rounded-lg border border-[#1b344d]/70 bg-[#0a1b2b] p-3.5 text-xs"
                                >
                                    <strong
                                        class="mb-1 block font-semibold text-white"
                                    >
                                        {{
                                            currentLang === 'id'
                                                ? 'Sinkronisasi Progres Belajar'
                                                : 'Progress Synchronization'
                                        }}
                                    </strong>
                                    <span class="text-slate-400">
                                        {{
                                            currentLang === 'id'
                                                ? 'Menyimpan riwayat capaian materi kimia dan skor kuis agar siswa dapat melanjutkan belajar kapan saja.'
                                                : 'Recording completed modules and quiz outcomes so students can resume learning seamlessly.'
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="rounded-lg border border-[#1b344d]/70 bg-[#0a1b2b] p-3.5 text-xs"
                                >
                                    <strong
                                        class="mb-1 block font-semibold text-white"
                                    >
                                        {{
                                            currentLang === 'id'
                                                ? 'Pengelolaan Konten Kurikulum'
                                                : 'Curriculum Management'
                                        }}
                                    </strong>
                                    <span class="text-slate-400">
                                        {{
                                            currentLang === 'id'
                                                ? 'Memungkinkan guru dan admin mendistribusikan materi, rumus kimia, model 3D, dan soal latihan melalui CMS.'
                                                : 'Empowering instructors to publish modules, formulas, 3D structures, and questions via CMS.'
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="rounded-lg border border-[#1b344d]/70 bg-[#0a1b2b] p-3.5 text-xs"
                                >
                                    <strong
                                        class="mb-1 block font-semibold text-white"
                                    >
                                        {{
                                            currentLang === 'id'
                                                ? 'Keamanan & Pencegahan Bug'
                                                : 'Security & App Stability'
                                        }}
                                    </strong>
                                    <span class="text-slate-400">
                                        {{
                                            currentLang === 'id'
                                                ? 'Mendeteksi kendala teknis dan crash agar aplikasi tetap lancar digunakan di berbagai tipe ponsel.'
                                                : 'Detecting crashes and technical glitches to optimize compatibility across diverse devices.'
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 5: KEAMANAN & PENYIMPANAN DATA -->
                    <section
                        id="keamanan-data"
                        class="rounded-xl border border-[#1b344d] bg-[#0e2338]/80 p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-500/20 bg-indigo-500/10 text-indigo-400"
                            >
                                <Lock class="h-5 w-5" />
                            </div>
                            <div>
                                <h2
                                    class="text-xl font-bold text-white sm:text-2xl"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? '5. Keamanan & Penyimpanan Data'
                                            : '5. Data Security & Retention'
                                    }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Langkah perlindungan teknis dan retensi data'
                                            : 'Technical protections and storage practices'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 space-y-4 text-sm leading-relaxed text-slate-300"
                        >
                            <p>
                                <template v-if="currentLang === 'id'">
                                    Kami menerapkan langkah-langkah keamanan
                                    berstandar industri untuk melindungi data
                                    pengguna dari akses, perubahan, atau
                                    pengungkapan yang tidak sah:
                                </template>
                                <template v-else>
                                    We employ robust, industry-standard
                                    technical measures to prevent unauthorized
                                    data access, modification, or exposure:
                                </template>
                            </p>

                            <ul class="list-disc space-y-2 pl-5 text-slate-300">
                                <li>
                                    <strong>{{
                                        currentLang === 'id'
                                            ? 'Enkripsi Data Bergerak (In-Transit):'
                                            : 'In-Transit Encryption:'
                                    }}</strong>
                                    {{
                                        currentLang === 'id'
                                            ? 'Semua komunikasi antara aplikasi seluler dan server CMS dienkripsi menggunakan protokol aman HTTPS/TLS.'
                                            : 'All network traffic between mobile devices and the CMS API is secured via industry-grade HTTPS/TLS.'
                                    }}
                                </li>
                                <li>
                                    <strong>{{
                                        currentLang === 'id'
                                            ? 'Enkripsi Kata Sandi (At-Rest):'
                                            : 'Password Hashing (At-Rest):'
                                    }}</strong>
                                    {{
                                        currentLang === 'id'
                                            ? 'Kata sandi pengguna tidak pernah disimpan dalam format teks polos (plain text), melainkan di-hash menggunakan algoritma Bcrypt/Argon2 yang kuat.'
                                            : 'Passwords are never stored in plain text and are cryptographically hashed using standard algorithms (Bcrypt/Argon2).'
                                    }}
                                </li>
                                <li>
                                    <strong>{{
                                        currentLang === 'id'
                                            ? 'Kontrol Akses Berbasis Peran (RBAC):'
                                            : 'Role-Based Access Control (RBAC):'
                                    }}</strong>
                                    {{
                                        currentLang === 'id'
                                            ? 'Akses ke data pengguna dan kurikulum di CMS dibatasi secara ketat berdasarkan wewenang peran (Role & Permission) yang terverifikasi.'
                                            : 'Administrative and educator capabilities within the CMS are strictly segmented via verified roles and permissions.'
                                    }}
                                </li>
                                <li>
                                    <strong>{{
                                        currentLang === 'id'
                                            ? 'Retensi Data:'
                                            : 'Data Retention:'
                                    }}</strong>
                                    {{
                                        currentLang === 'id'
                                            ? 'Data akun dan progres disimpan selama akun aktif digunakan. Pengguna dapat mengajukan permohonan penghapusan data kapan saja.'
                                            : 'User progress and profile data are retained while the account is active. Users can request immediate deletion at any time.'
                                    }}
                                </li>
                            </ul>
                        </div>
                    </section>

                    <!-- SECTION 6: PRIVASI PELAJAR & ANAK -->
                    <section
                        id="privasi-pelajar"
                        class="rounded-xl border border-[#1b344d] bg-[#0e2338]/80 p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-pink-500/20 bg-pink-500/10 text-pink-400"
                            >
                                <GraduationCap class="h-5 w-5" />
                            </div>
                            <div>
                                <h2
                                    class="text-xl font-bold text-white sm:text-2xl"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? '6. Perlindungan Privasi Pelajar & Anak'
                                            : '6. Student & Children Privacy'
                                    }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Komitmen khusus lingkungan pendidikan'
                                            : 'Dedicated commitments for learning environments'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 space-y-4 text-sm leading-relaxed text-slate-300"
                        >
                            <p>
                                <template v-if="currentLang === 'id'">
                                    Sebagai aplikasi yang ditujukan untuk
                                    mendukung pembelajaran sains di sekolah dan
                                    perguruan tinggi:
                                </template>
                                <template v-else>
                                    As an educational solution designed to
                                    assist chemistry education in schools and
                                    academic institutions:
                                </template>
                            </p>
                            <ul class="list-disc space-y-2 pl-5 text-slate-300">
                                <li>
                                    <strong>{{
                                        currentLang === 'id'
                                            ? 'Bebas Iklan Komersial:'
                                            : 'Zero Third-Party Advertising:'
                                    }}</strong>
                                    {{
                                        currentLang === 'id'
                                            ? 'MVAR tidak menayangkan iklan pihak ketiga, tidak menggunakan SDK jaringan periklanan perilaku (behavioral ad networks), dan tidak melacak profil komersial siswa.'
                                            : 'MVAR contains no behavioral advertisements, no commercial ad SDKs, and does not build commercial student profiles.'
                                    }}
                                </li>
                                <li>
                                    <strong>{{
                                        currentLang === 'id'
                                            ? 'Kepatuhan Regulasi:'
                                            : 'Regulatory Compliance:'
                                    }}</strong>
                                    {{
                                        currentLang === 'id'
                                            ? 'Kami tunduk pada prinsip perlindungan data pribadi sesuai UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP) di Indonesia serta ketentuan Google Play Families Policy.'
                                            : 'We align our practices with Indonesia PDP Law No. 27/2022, GDPR child data provisions, and Google Play Families Policy.'
                                    }}
                                </li>
                                <li>
                                    <strong>{{
                                        currentLang === 'id'
                                            ? 'Pengawasan Guru & Orang Tua:'
                                            : 'Parent & Educator Supervision:'
                                    }}</strong>
                                    {{
                                        currentLang === 'id'
                                            ? 'Guru atau orang tua dapat menghubungi tim kami apabila menghendaki peninjauan atau penghapusan data siswa yang terdaftar.'
                                            : 'Educators or guardians may contact our administrative team at any time to inspect or remove student account data.'
                                    }}
                                </li>
                            </ul>
                        </div>
                    </section>

                    <!-- SECTION 7: HAK PENGGUNA & PENGHAPUSAN AKUN -->
                    <section
                        id="hak-pengguna"
                        class="rounded-xl border border-[#1b344d] bg-[#0e2338]/80 p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-500/10 text-purple-400"
                            >
                                <Trash2 class="h-5 w-5" />
                            </div>
                            <div>
                                <h2
                                    class="text-xl font-bold text-white sm:text-2xl"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? '7. Hak Pengguna & Penghapusan Akun'
                                            : '7. User Rights & Account Deletion'
                                    }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Kontrol penuh atas informasi pribadi Anda'
                                            : 'Full control over your personal information'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 space-y-4 text-sm leading-relaxed text-slate-300"
                        >
                            <p>
                                <template v-if="currentLang === 'id'">
                                    Pengguna MVAR memiliki hak penuh atas data
                                    pribadi mereka, meliputi:
                                </template>
                                <template v-else>
                                    All MVAR users retain explicit rights
                                    concerning their personal data:
                                </template>
                            </p>

                            <div
                                class="grid grid-cols-1 gap-3 pt-1 sm:grid-cols-3"
                            >
                                <div
                                    class="rounded-lg border border-[#1b344d]/70 bg-[#0a1b2b] p-3.5 text-xs"
                                >
                                    <strong
                                        class="mb-1 block font-semibold text-white"
                                    >
                                        {{
                                            currentLang === 'id'
                                                ? '1. Hak Mengakses Data'
                                                : '1. Right to Access'
                                        }}
                                    </strong>
                                    <span class="text-slate-400">
                                        {{
                                            currentLang === 'id'
                                                ? 'Melihat informasi profil dan rekam jejak progres kuis Anda di dalam aplikasi.'
                                                : 'View your stored credentials, degree information, and quiz progress.'
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="rounded-lg border border-[#1b344d]/70 bg-[#0a1b2b] p-3.5 text-xs"
                                >
                                    <strong
                                        class="mb-1 block font-semibold text-white"
                                    >
                                        {{
                                            currentLang === 'id'
                                                ? '2. Hak Memperbarui Data'
                                                : '2. Right to Rectify'
                                        }}
                                    </strong>
                                    <span class="text-slate-400">
                                        {{
                                            currentLang === 'id'
                                                ? 'Memperbarui nama, kata sandi, dan jenjang pendidikan melalui halaman Pengaturan Profil.'
                                                : 'Modify your name, password, or educational level via profile settings.'
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="rounded-lg border border-[#1b344d]/70 bg-[#0a1b2b] p-3.5 text-xs"
                                >
                                    <strong
                                        class="mb-1 block font-semibold text-white"
                                    >
                                        {{
                                            currentLang === 'id'
                                                ? '3. Hak Penghapusan Akun'
                                                : '3. Right to Erasure'
                                        }}
                                    </strong>
                                    <span class="text-slate-400">
                                        {{
                                            currentLang === 'id'
                                                ? 'Meminta penghapusan permanen akun dan seluruh riwayat progres belajar.'
                                                : 'Request permanent deletion of your account and associated records.'
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- How to Request Deletion Box -->
                            <div
                                class="rounded-lg border border-[#1b344d] bg-[#0c1a28] p-4 text-xs"
                            >
                                <strong
                                    class="mb-1 block text-sm font-semibold text-[#e8a917]"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? 'Panduan Pengajuan Penghapusan Akun & Data (Account Deletion):'
                                            : 'How to Request Account & Data Deletion:'
                                    }}
                                </strong>
                                <p class="leading-relaxed text-slate-300">
                                    <template v-if="currentLang === 'id'">
                                        Sesuai regulasi Google Play Store dan
                                        Apple App Store mengenai
                                        <em>Account Deletion Requirement</em>,
                                        Anda dapat meminta penghapusan akun
                                        kapan saja dengan cara:
                                    </template>
                                    <template v-else>
                                        In compliance with Google Play Store and
                                        Apple App Store Account Deletion
                                        Requirements, you can request account
                                        erasure at any time via:
                                    </template>
                                </p>
                                <ol
                                    class="mt-2 list-decimal space-y-1 pl-4 text-slate-300"
                                >
                                    <li>
                                        {{
                                            currentLang === 'id'
                                                ? 'Melalui menu profil pengguna di dalam aplikasi seluler MVAR atau CMS Web.'
                                                : 'Directly inside the MVAR Mobile App or CMS under Profile Settings.'
                                        }}
                                    </li>
                                    <li>
                                        {{
                                            currentLang === 'id'
                                                ? 'Mengirimkan email permohonan ke tim teknis kami di '
                                                : 'Sending a direct deletion request to our support email at '
                                        }}
                                        <a
                                            href="mailto:support@mvar.id"
                                            class="font-medium text-[#e8a917] underline"
                                            >support@mvar.id</a
                                        >
                                        {{
                                            currentLang === 'id'
                                                ? ' dengan subjek "Permohonan Penghapusan Akun MVAR" disertai alamat email akun Anda.'
                                                : ' with the subject "MVAR Account Deletion Request" and your registered email address.'
                                        }}
                                    </li>
                                </ol>
                                <p class="mt-2 text-[11px] text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Setelah diverifikasi, akun beserta seluruh data progres belajar akan dihapus permanen dalam jangka waktu maksimal 7 (tujuh) hari kerja.'
                                            : 'Upon verification, your profile and learning records will be permanently expunged within 7 business days.'
                                    }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 8: PEMBARUAN & KONTAK DUKUNGAN -->
                    <section
                        id="pembaruan-kontak"
                        class="rounded-xl border border-[#1b344d] bg-[#0e2338]/80 p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-[#e8a917]/20 bg-[#e8a917]/10 text-[#e8a917]"
                            >
                                <Mail class="h-5 w-5" />
                            </div>
                            <div>
                                <h2
                                    class="text-xl font-bold text-white sm:text-2xl"
                                >
                                    {{
                                        currentLang === 'id'
                                            ? '8. Pembaruan Kebijakan & Kontak'
                                            : '8. Updates & Contact Info'
                                    }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{
                                        currentLang === 'id'
                                            ? 'Hubungi tim pengembang untuk pertanyaan privasi'
                                            : 'Reach out to the development team for inquiries'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 space-y-4 text-sm leading-relaxed text-slate-300"
                        >
                            <p>
                                <template v-if="currentLang === 'id'">
                                    Kami dapat memperbarui Kebijakan Privasi ini
                                    secara berkala untuk mencerminkan
                                    peningkatan fitur, modul pembelajaran baru,
                                    atau penyesuaian regulasi hukum yang
                                    berlaku. Tanggal revisi terbaru akan selalu
                                    diperbarui di bagian atas dokumen ini.
                                </template>
                                <template v-else>
                                    We may revise this Privacy Policy
                                    periodically to reflect new educational
                                    capabilities, software updates, or
                                    regulatory obligations. The latest revision
                                    date is prominently displayed at the top of
                                    this document.
                                </template>
                            </p>

                            <!-- Contact Card -->
                            <div
                                class="mt-4 rounded-xl border border-[#1b344d] bg-gradient-to-r from-[#0a1b2b] to-[#0e2338] p-5"
                            >
                                <div
                                    class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                >
                                    <div>
                                        <h3
                                            class="text-sm font-bold text-white"
                                        >
                                            {{
                                                currentLang === 'id'
                                                    ? 'Tim Pengembang Molecular Vision AR'
                                                    : 'Molecular Vision AR Development Team'
                                            }}
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{
                                                currentLang === 'id'
                                                    ? 'Inisiatif Media Pembelajaran Digital & CMS Edukasi Sains'
                                                    : 'Interactive Digital Science Learning & CMS Initiative'
                                            }}
                                        </p>
                                        <div
                                            class="mt-3 space-y-1.5 text-xs text-slate-300"
                                        >
                                            <p class="flex items-center gap-2">
                                                <Mail
                                                    class="h-3.5 w-3.5 text-[#e8a917]"
                                                />
                                                <span
                                                    >Email:
                                                    <a
                                                        href="mailto:support@mvar.id"
                                                        class="text-white underline hover:text-[#e8a917]"
                                                        >support@mvar.id</a
                                                    ></span
                                                >
                                            </p>
                                            <p class="flex items-center gap-2">
                                                <Globe
                                                    class="h-3.5 w-3.5 text-[#e8a917]"
                                                />
                                                <span
                                                    >Platform:
                                                    <span class="text-white"
                                                        >Molecular Vision AR
                                                        (MVAR)</span
                                                    ></span
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="mt-16 border-t border-[#1b344d] bg-[#050c13] py-8 text-center text-xs text-slate-500"
        >
            <div class="mx-auto max-w-7xl space-y-3 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center gap-4">
                    <img
                        src="/MVAR.png"
                        alt="MVAR Logo"
                        class="h-6 w-auto object-contain opacity-75"
                    />
                    <span class="font-medium text-slate-400"
                        >Molecular Vision AR</span
                    >
                </div>
                <div
                    class="flex flex-wrap items-center justify-center gap-3 text-slate-400"
                >
                    <Link
                        :href="route('privacy-policy')"
                        class="transition-colors hover:text-[#e8a917]"
                    >
                        {{
                            currentLang === 'id'
                                ? 'Kebijakan Privasi'
                                : 'Privacy Policy'
                        }}
                    </Link>
                </div>
                <p class="text-[11px] text-slate-600">
                    © 2026 Molecular Vision AR — Sistem Manajemen Konten
                    Akademik & Media Edukasi Kimia Interaktif Berbasis Augmented
                    Reality.
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@media print {
    header,
    aside,
    footer,
    button {
        display: none !important;
    }
    body,
    div,
    section {
        background: white !important;
        color: black !important;
    }
}
</style>
