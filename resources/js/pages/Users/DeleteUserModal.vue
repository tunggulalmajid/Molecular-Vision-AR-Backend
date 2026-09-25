<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import ModalDialog from '@/Components/ModalDialog.vue';
import { AlertTriangle, Loader2 } from 'lucide-vue-next';

export interface UserItem {
    id_user: number;
    name: string;
    email: string;
}

const props = defineProps<{
    show: boolean;
    user: UserItem | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({});

const deleteUser = () => {
    if (!props.user) return;

    form.delete(route('users.destroy', props.user.id_user), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>

<template>
    <ModalDialog
        :show="show"
        title="Konfirmasi Hapus Pengguna"
        max-width="md"
        @close="$emit('close')"
    >
        <div class="space-y-4">
            <div
                class="flex items-start gap-3.5 rounded-lg border border-rose-500/20 bg-rose-500/10 p-3.5"
            >
                <div class="flex-shrink-0 text-rose-400">
                    <AlertTriangle class="h-5 w-5" />
                </div>
                <div class="text-sm text-slate-300">
                    <p class="font-medium text-white">Perhatian!</p>
                    <p class="mt-0.5 text-xs text-slate-300">
                        Apakah Anda yakin ingin menghapus data pengguna
                        <span class="font-bold text-rose-300">{{
                            user?.name
                        }}</span>
                        ({{ user?.email }})?
                    </p>
                </div>
            </div>

            <p class="text-xs text-slate-400">
                Data pengguna akan dihapus dari sistem. Tindakan ini tidak dapat
                dibatalkan.
            </p>

            <!-- Action buttons -->
            <div class="flex items-center justify-end gap-3 pt-3">
                <button
                    type="button"
                    @click="$emit('close')"
                    class="rounded-lg border border-[#1b344d] bg-[#0d1e30] px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-[#122840] hover:text-white focus:outline-none"
                >
                    Batal
                </button>
                <button
                    type="button"
                    @click="deleteUser"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 focus:ring-offset-[#0b1726] focus:outline-none disabled:opacity-60"
                >
                    <Loader2
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin text-white"
                    />
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </ModalDialog>
</template>
