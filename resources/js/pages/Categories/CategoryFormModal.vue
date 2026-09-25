<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalDialog from '@/Components/ModalDialog.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import { Loader2 } from 'lucide-vue-next';

export interface CategoryItem {
    id_category: number;
    name: string;
    description: string;
    materials_count?: number;
    questions_count?: number;
}

const props = defineProps<{
    show: boolean;
    category: CategoryItem | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    name: '',
    description: '',
});

watch(
    () => props.show,
    (isOpen) => {
        if (!isOpen) {
            form.reset();
            form.clearErrors();
            return;
        }

        if (props.category) {
            form.name = props.category.name;
            form.description = props.category.description;
        } else {
            form.reset();
        }
        form.clearErrors();
    },
);

const submit = () => {
    if (props.category) {
        form.put(route('categories.update', props.category.id_category), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
            },
        });
    } else {
        form.post(route('categories.store'), {
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
        :title="category ? 'Edit Kategori' : 'Tambah Kategori Baru'"
        max-width="md"
        @close="$emit('close')"
    >
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Nama Kategori -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <DarkTextInput
                    v-model="form.name"
                    placeholder="Contoh: Geometri Molekul, Ikatan Kimia"
                    required
                    autofocus
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Deskripsi Kategori -->
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                >
                    Deskripsi Kategori <span class="text-rose-500">*</span>
                </label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Tuliskan penjelasan cakupan topik materi kimia ini..."
                    required
                    class="w-full rounded-lg border border-[#1b344d] bg-[#0c1a28] px-4 py-3 text-sm text-white placeholder-slate-500 transition-all duration-150 focus:border-[#2e5984] focus:ring-1 focus:ring-[#2e5984] focus:outline-none"
                ></textarea>
                <p
                    v-if="form.errors.description"
                    class="mt-1 text-xs text-rose-400"
                >
                    {{ form.errors.description }}
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
                        category ? 'Simpan Perubahan' : 'Tambah Kategori'
                    }}</span>
                </button>
            </div>
        </form>
    </ModalDialog>
</template>
