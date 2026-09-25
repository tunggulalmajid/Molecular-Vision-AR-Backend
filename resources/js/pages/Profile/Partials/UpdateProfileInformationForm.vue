<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import {
    Loader2,
    CheckCircle2,
    Shield,
    GraduationCap,
    School,
} from 'lucide-vue-next';

interface Degree {
    id_degree: number;
    name: string;
}

const props = defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    degrees?: Degree[];
}>();

const user = usePage().props.auth.user as any;
const userRole = (usePage().props.auth.roles as string[])?.[0] || 'User';

const form = useForm({
    name: user.name || '',
    email: user.email || '',
    school: user.school || '',
    id_degree: user.id_degree || '',
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section
        class="rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl sm:p-8"
    >
        <header class="border-b border-[#14263b] pb-4">
            <h2 class="text-lg font-bold text-white">Informasi Profil Akun</h2>
            <p class="mt-1 text-xs text-slate-400">
                Perbarui data profil akun Anda, instansi sekolah, serta jenjang
                tingkatan kelas.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-5">
            <!-- Nama Lengkap -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.name"
                    type="text"
                    placeholder="Masukkan nama lengkap"
                    required
                    autofocus
                    autocomplete="name"
                />
                <p v-if="form.errors.name" class="mt-1.5 text-xs text-rose-400">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email Address -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.email"
                    type="email"
                    placeholder="nama@email.com"
                    required
                    autocomplete="username"
                />
                <p
                    v-if="form.errors.email"
                    class="mt-1.5 text-xs text-rose-400"
                >
                    {{ form.errors.email }}
                </p>

                <!-- <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mt-2 text-xs text-amber-400">
                    Email Anda belum terverifikasi.
                </div> -->
            </div>

            <!-- Asal Sekolah / Institusi -->
            <div>
                <label
                    class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    <School class="h-3.5 w-3.5 text-slate-400" />
                    <span>Sekolah / Institusi</span>
                </label>
                <DarkTextInput
                    v-model="form.school"
                    type="text"
                    placeholder="Contoh: SMAN 1 Jakarta / Universitas / Kemendikbud"
                />
                <p
                    v-if="form.errors.school"
                    class="mt-1.5 text-xs text-rose-400"
                >
                    {{ form.errors.school }}
                </p>
            </div>

            <!-- Jenjang Tingkat Kelas (degrees) -->
            <div>
                <label
                    class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    <GraduationCap class="h-3.5 w-3.5 text-slate-400" />
                    <span>Jenjang / Tingkat Kelas</span>
                </label>
                <div class="relative">
                    <select
                        v-model="form.id_degree"
                        class="w-full appearance-none rounded-lg border border-[#1b344d] bg-[#0c1a28] px-4 py-3 text-sm text-white transition-all duration-150 focus:border-[#2e5984] focus:ring-1 focus:ring-[#2e5984] focus:outline-none"
                    >
                        <option value="" class="bg-[#0c1a28] text-slate-500">
                            Pilih Jenjang Kelas (Opsional)
                        </option>
                        <option
                            v-for="deg in degrees"
                            :key="deg.id_degree"
                            :value="deg.id_degree"
                            class="bg-[#0c1a28] text-white"
                        >
                            {{ deg.name }}
                        </option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </div>
                </div>
                <p
                    v-if="form.errors.id_degree"
                    class="mt-1 text-xs text-rose-400"
                >
                    {{ form.errors.id_degree }}
                </p>
            </div>

            <!-- Peran Hak Akses (Read-only badge info) -->
            <div>
                <label
                    class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    <Shield class="h-3.5 w-3.5 text-[#e5a824]" />
                    <span>Peran Akun Sistem</span>
                </label>
                <div
                    class="flex items-center gap-3 rounded-lg border border-[#14263b] bg-[#0c1c2e] px-4 py-3 text-sm"
                >
                    <span class="font-semibold text-white capitalize">{{
                        userRole
                    }}</span>
                    <span
                        class="rounded bg-[#122840] px-2 py-0.5 text-[10px] font-medium text-slate-400"
                    >
                        Dikelola Administrator
                    </span>
                </div>
            </div>

            <!-- Action Save Button & Status -->
            <div class="flex items-center gap-4 pt-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-5 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#091624] focus:outline-none disabled:opacity-60"
                >
                    <Loader2
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin text-black"
                    />
                    <span>Simpan Perubahan</span>
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
                        <span>Tersimpan dengan sukses.</span>
                    </p>
                </transition>
            </div>
        </form>
    </section>
</template>
