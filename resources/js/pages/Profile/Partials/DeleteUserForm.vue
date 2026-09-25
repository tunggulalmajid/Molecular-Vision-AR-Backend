<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalDialog from '@/Components/ModalDialog.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import { AlertTriangle, Loader2 } from 'lucide-vue-next';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="rounded-xl border border-rose-500/20 bg-[#091624] p-6 shadow-xl sm:p-8">
        <header class="border-b border-rose-500/20 pb-4">
            <h2 class="text-lg font-bold text-rose-400">
                Zona Bahaya: Hapus Akun
            </h2>
            <p class="mt-1 text-xs text-slate-400">
                Setelah akun Anda dihapus, semua data dan sesi login Anda akan dihentikan secara permanen.
            </p>
        </header>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-400 max-w-xl">
                Tindakan ini tidak dapat dibatalkan. Pastikan Anda telah mempertimbangkan sebelum melanjutkan penghapusan akun.
            </p>
            <button
                type="button"
                @click="confirmUserDeletion"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-600/20 transition-all hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 focus:ring-offset-[#091624]"
            >
                <span>Hapus Akun Saya</span>
            </button>
        </div>

        <!-- Deletion Confirmation Modal -->
        <ModalDialog
            :show="confirmingUserDeletion"
            title="Konfirmasi Penghapusan Akun"
            max-width="md"
            @close="closeModal"
        >
            <div class="space-y-4">
                <div class="flex items-start gap-3 rounded-lg border border-rose-500/20 bg-rose-500/10 p-3.5">
                    <AlertTriangle class="h-5 w-5 shrink-0 text-rose-400" />
                    <p class="text-xs text-slate-300">
                        Apakah Anda yakin ingin menghapus akun Anda? Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
                    </p>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Kata Sandi Anda <span class="text-rose-500">*</span>
                    </label>
                    <DarkTextInput
                        v-model="form.password"
                        type="password"
                        placeholder="Masukkan kata sandi untuk konfirmasi"
                        :show-password-toggle="true"
                        @keyup.enter="deleteUser"
                    />
                    <p v-if="form.errors.password" class="mt-1.5 text-xs text-rose-400">
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button
                        type="button"
                        @click="closeModal"
                        class="rounded-lg border border-[#1b344d] bg-[#0d1e30] px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-[#122840] hover:text-white focus:outline-none"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="deleteUser"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 focus:ring-offset-[#0b1726] disabled:opacity-60"
                    >
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin text-white" />
                        <span>Hapus Permanen</span>
                    </button>
                </div>
            </div>
        </ModalDialog>
    </section>
</template>
