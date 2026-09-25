<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalDialog from '@/Components/ModalDialog.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import { Loader2 } from 'lucide-vue-next';

export interface RoleItem {
    id: number;
    name: string;
    guard_name: string;
    users_count?: number;
    permissions?: { id: number; name: string }[];
    created_at?: string;
}

const props = defineProps<{
    show: boolean;
    role: RoleItem | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    name: '',
});

watch(
    () => props.show,
    (isOpen) => {
        if (!isOpen) {
            form.reset();
            form.clearErrors();
            return;
        }

        if (props.role) {
            form.name = props.role.name;
        } else {
            form.reset();
        }
        form.clearErrors();
    }
);

const submit = () => {
    if (props.role) {
        form.put(route('roles.update', props.role.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
            },
        });
    } else {
        form.post(route('roles.store'), {
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
        :title="role ? 'Edit Nama Peran' : 'Tambah Peran Baru'"
        max-width="md"
        @close="$emit('close')"
    >
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Nama Peran (Role) <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.name"
                    placeholder="Contoh: guru, koordinator materi, kurator"
                    required
                    autofocus
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">
                    {{ form.errors.name }}
                </p>
                <p class="mt-1.5 text-xs text-slate-400">
                    Nama peran akan otomatis dikonversi ke huruf kecil (lowercase) untuk konsistensi sistem.
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
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#e5a824] px-5 py-2.5 text-sm font-semibold text-black transition-all hover:bg-[#d49718] focus:outline-none focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#0b1726] disabled:opacity-60"
                >
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin text-black" />
                    <span>{{ role ? 'Simpan Perubahan' : 'Tambah Peran' }}</span>
                </button>
            </div>
        </form>
    </ModalDialog>
</template>
