<script setup lang="ts">
import AuthButton from '@/Components/AuthButton.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import FeaturePill from '@/Components/FeaturePill.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LineChart } from 'lucide-vue-next';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="flex min-h-screen w-full flex-col font-sans lg:flex-row">
        <!-- Left Side: Branding, Hero & Feature Highlights -->
        <div
            class="relative flex w-full flex-col justify-between overflow-hidden bg-[#0e2338] p-8 sm:p-12 lg:min-h-screen lg:w-[50%] lg:p-14 xl:p-16"
        >
            <!-- Decorative Background Circular Elements -->
            <div
                class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-[#091827]/60"
            />
            <div
                class="pointer-events-none absolute -bottom-36 -left-36 h-[460px] w-[460px] rounded-full bg-[#081522]/80"
            />
            <div
                class="pointer-events-none absolute top-1/2 -right-24 h-80 w-80 -translate-y-1/2 rounded-full bg-[#0a1b2b]/50"
            />

            <!-- Top Logo -->
            <div class="relative z-10 mb-8 lg:mb-0">
                <img
                    src="/MVAR.png"
                    alt="MVAR Logo"
                    class="h-9 w-auto object-contain sm:h-10"
                />
            </div>

            <!-- Middle Text Header -->
            <div class="relative z-10 my-auto py-8 lg:py-0">
                <h1
                    class="mb-3.5 text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-[38px] lg:leading-[1.25] xl:text-[42px]"
                >
                    Layanan Manajemen<br class="hidden sm:inline" />
                    Konten Molecular Vision AR
                </h1>
                <p
                    class="max-w-lg text-xs leading-relaxed text-slate-300/85 sm:text-sm lg:text-base"
                >
                    Kelola Data Materi, Molekul dengan cepat dan mudah
                </p>
            </div>

            <!-- Bottom Feature Badges -->
            <div class="relative z-10 space-y-3 pt-6 lg:pt-0">
                <!-- Feature 2: Analytics & Progress -->
                <FeaturePill
                    text="KELOLA Materi, Molekul dan Soal untuk MVAR Mobile"
                >
                    <template #icon>
                        <LineChart
                            class="h-4.5 w-4.5 text-slate-300"
                            :size="18"
                        />
                    </template>
                </FeaturePill>
            </div>
        </div>

        <!-- Right Side: Clean Dark Login Form -->
        <div
            class="flex w-full flex-1 flex-col justify-between bg-[#07111b] p-6 sm:p-12 lg:min-h-screen lg:w-[50%] lg:p-14 xl:p-16"
        >
            <!-- Top empty space for balance on desktop -->
            <div class="hidden lg:block" />

            <!-- Main Form Card -->
            <div class="mx-auto my-auto w-full max-w-sm sm:max-w-md">
                <div class="mb-6">
                    <h2
                        class="text-2xl font-bold tracking-tight text-white sm:text-3xl"
                    >
                        Selamat Datang
                    </h2>
                    <p class="mt-1 text-xs text-slate-400 sm:text-sm">
                        Masuk ke akun CMS Admin Anda
                    </p>
                </div>

                <div
                    v-if="status"
                    class="mb-5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 p-3 text-xs font-medium text-emerald-400"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4 sm:space-y-5">
                    <!-- Username / Email Field -->
                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-xs font-medium text-slate-300"
                        >
                            Email
                        </label>
                        <DarkTextInput
                            id="email"
                            v-model="form.email"
                            type="text"
                            placeholder="Masukkan Email "
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError
                            class="mt-1.5 text-xs text-rose-400"
                            :message="form.errors.email"
                        />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-xs font-medium text-slate-300"
                        >
                            Password
                        </label>
                        <DarkTextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Masukkan password"
                            :show-password-toggle="true"
                            required
                            autocomplete="current-password"
                        />
                        <InputError
                            class="mt-1.5 text-xs text-rose-400"
                            :message="form.errors.password"
                        />
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between pt-1">
                        <label
                            class="flex cursor-pointer items-center gap-2 select-none"
                        >
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="h-4 w-4 rounded border-[#1b344d] bg-[#0c1a28] text-[#e8a917] focus:ring-0 focus:ring-offset-0"
                            />
                            <span class="text-xs text-slate-400"
                                >Ingat saya</span
                            >
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <AuthButton :loading="form.processing">
                            Masuk ke CMS Admin
                        </AuthButton>
                    </div>
                </form>
            </div>

            <!-- Bottom Copyright Footer -->
            <footer class="mt-8 text-center">
                <p class="text-[11px] text-slate-500 sm:text-xs">
                    © 2026 Molecular Vision AR — Sistem Manajemen Konten
                    Akademik
                </p>
            </footer>
        </div>
    </div>
</template>
