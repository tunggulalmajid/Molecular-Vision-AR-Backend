<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import { Loader2, CheckCircle2 } from 'lucide-vue-next';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section class="rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl sm:p-8">
        <header class="border-b border-[#14263b] pb-4">
            <h2 class="text-lg font-bold text-white">
                Perbarui Kata Sandi
            </h2>
            <p class="mt-1 text-xs text-slate-400">
                Pastikan akun Anda menggunakan kata sandi yang panjang dan aman dengan kombinasi karakter acak.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-5">
            <!-- Current Password -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.current_password"
                    type="password"
                    placeholder="Masukkan kata sandi lama Anda"
                    autocomplete="current-password"
                    :show-password-toggle="true"
                    required
                />
                <p v-if="form.errors.current_password" class="mt-1.5 text-xs text-rose-400">
                    {{ form.errors.current_password }}
                </p>
            </div>

            <!-- New Password -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.password"
                    type="password"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    :show-password-toggle="true"
                    required
                />
                <p v-if="form.errors.password" class="mt-1.5 text-xs text-rose-400">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Password Confirmation -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.password_confirmation"
                    type="password"
                    placeholder="Ulangi kata sandi baru"
                    autocomplete="new-password"
                    :show-password-toggle="true"
                    required
                />
                <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-rose-400">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <!-- Submit Button & Feedback -->
            <div class="flex items-center gap-4 pt-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-5 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:outline-none focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#091624] disabled:opacity-60"
                >
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin text-black" />
                    <span>Ubah Kata Sandi</span>
                </button>

                <transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="flex items-center gap-1.5 text-xs font-medium text-emerald-400"
                    >
                        <CheckCircle2 class="h-4 w-4" />
                        <span>Kata sandi berhasil diperbarui.</span>
                    </p>
                </transition>
            </div>
        </form>
    </section>
</template>
