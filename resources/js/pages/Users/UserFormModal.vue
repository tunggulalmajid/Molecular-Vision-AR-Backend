<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalDialog from '@/Components/ModalDialog.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import { Loader2 } from 'lucide-vue-next';

export interface UserItem {
    id_user: number;
    name: string;
    email: string;
    school: string | null;
    roles?: { id: number; name: string }[];
}

const props = defineProps<{
    show: boolean;
    user: UserItem | null;
    roles: string[];
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    name: '',
    email: '',
    school: '',
    password: '',
    role: '',
});

watch(
    () => props.show,
    (isOpen) => {
        if (!isOpen) {
            form.reset();
            form.clearErrors();
            return;
        }

        if (props.user) {
            // Edit Mode
            form.name = props.user.name || '';
            form.email = props.user.email || '';
            form.school = props.user.school || '';
            form.password = '';
            form.role =
                props.user.roles?.[0]?.name ||
                (props.roles.length > 0 ? props.roles[0] : '');
        } else {
            // Create Mode
            form.reset();
            form.role = props.roles.length > 0 ? props.roles[0] : '';
        }
        form.clearErrors();
    },
);

const submit = () => {
    if (props.user) {
        form.put(route('users.update', props.user.id_user), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
            },
        });
    } else {
        form.post(route('users.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
            },
        });
    }
};
</script>

<template>
    <ModalDialog
        :show="show"
        :title="user ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"
        max-width="lg"
        @close="$emit('close')"
    >
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Nama Lengkap -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.name"
                    placeholder="Masukkan nama lengkap"
                    required
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email Address -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Email <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.email"
                    type="email"
                    placeholder="nama@email.com"
                    required
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Asal Sekolah / Institusi -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Sekolah / Institusi
                </label>
                <DarkTextInput
                    v-model="form.school"
                    placeholder="Contoh: SMAN 1 Jakarta / Kemendikbud"
                />
                <p v-if="form.errors.school" class="mt-1 text-xs text-rose-400">
                    {{ form.errors.school }}
                </p>
            </div>

            <!-- Role Selection -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Role Hak Akses <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select
                        v-model="form.role"
                        required
                        class="w-full appearance-none rounded-lg border border-[#1b344d] bg-[#0c1a28] px-4 py-3 text-sm text-white transition-all duration-150 focus:border-[#2e5984] focus:ring-1 focus:ring-[#2e5984] focus:outline-none"
                    >
                        <option
                            value=""
                            disabled
                            class="bg-[#0c1a28] text-slate-500"
                        >
                            Pilih Role
                        </option>
                        <option
                            v-for="roleName in roles"
                            :key="roleName"
                            :value="roleName"
                            class="bg-[#0c1a28] text-white capitalize"
                        >
                            {{ roleName }}
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
                <p v-if="form.errors.role" class="mt-1 text-xs text-rose-400">
                    {{ form.errors.role }}
                </p>
            </div>

            <!-- Password -->
            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label
                        class="text-xs font-semibold tracking-wider text-slate-300 uppercase"
                    >
                        Password
                        <span v-if="!user" class="text-rose-500">*</span>
                    </label>
                    <span v-if="user" class="text-[11px] text-slate-400">
                        (Kosongkan jika tidak ingin mengubah)
                    </span>
                </div>
                <DarkTextInput
                    v-model="form.password"
                    type="password"
                    :placeholder="
                        user
                            ? 'Biarkan kosong untuk mempertahankan password lama'
                            : 'Minimal 8 karakter'
                    "
                    :required="!user"
                    :show-password-toggle="true"
                />
                <p
                    v-if="form.errors.password"
                    class="mt-1 text-xs text-rose-400"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Modal Action Buttons -->
            <div class="mt-6 flex items-center justify-end gap-3 pt-3">
                <button
                    type="button"
                    @click="$emit('close')"
                    class="rounded-lg border border-[#1b344d] bg-[#0d1e30] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-[#122840] hover:text-white focus:outline-none"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#e5a824] px-5 py-2.5 text-sm font-semibold text-black transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#0b1726] focus:outline-none disabled:opacity-60"
                >
                    <Loader2
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin text-black"
                    />
                    <span>{{
                        user ? 'Simpan Perubahan' : 'Tambah Pengguna'
                    }}</span>
                </button>
            </div>
        </form>
    </ModalDialog>
</template>
