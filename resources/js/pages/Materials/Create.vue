<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { ArrowLeft, BookOpen, Check, Loader2 } from 'lucide-vue-next';

interface CategoryOption {
    id_category: number;
    name: string;
}

const props = defineProps<{
    categories: CategoryOption[];
}>();

const form = useForm({
    name: '',
    id_category: props.categories[0]?.id_category || '',
    description: '',
    content: '',
});

const submit = () => {
    form.post(route('materials.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Tambah Materi Pembelajaran" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Back navigation & Title Header -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('materials.index')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:bg-[#14263b] hover:text-white"
                        title="Kembali ke Daftar Materi"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-white"
                        >
                            Tambah Materi Pembelajaran
                        </h1>
                        <p class="mt-0.5 text-xs text-slate-400">
                            Susun materi baru lengkap dengan modul teks
                            interaktif, media 3D, gambar, dan video pendukung.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Metadata Section (Title, Category, Short Description) -->
                <div
                    class="space-y-5 rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <!-- Judul Materi -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Judul Materi
                                <span class="text-rose-400">*</span>
                            </label>
                            <DarkTextInput
                                v-model="form.name"
                                placeholder="Contoh: Pengenalan Bentuk Geometri Molekul..."
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Kategori Kimia -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Kategori Kimia Induk
                                <span class="text-rose-400">*</span>
                            </label>
                            <select
                                v-model="form.id_category"
                                class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] px-3.5 py-2.5 text-sm text-white focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                                required
                            >
                                <option value="" disabled>
                                    Pilih Kategori...
                                </option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat.id_category"
                                    :value="cat.id_category"
                                >
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.id_category"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.id_category }}
                            </p>
                        </div>
                    </div>

                    <!-- Ringkasan / Deskripsi Singkat -->
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label
                                class="block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Ringkasan Singkat Materi
                                <span class="text-rose-400">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">
                                {{ form.description.length }}/500 karakter
                            </span>
                        </div>
                        <textarea
                            v-model="form.description"
                            rows="2"
                            maxlength="500"
                            placeholder="Tuliskan ringkasan 1-2 kalimat pengantar mengenai apa yang akan dipelajari siswa di materi ini..."
                            class="w-full rounded-xl border border-[#1b344d] bg-[#0c1a28] px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:border-[#e5a824] focus:ring-1 focus:ring-[#e5a824] focus:outline-none"
                            required
                        ></textarea>
                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-xs text-rose-400"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>

                <!-- Content Section: Rich Text Editor -->
                <div
                    class="space-y-3 rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <h2
                                class="text-sm font-semibold tracking-wider text-white uppercase"
                            >
                                Konten & Modul Pembelajaran
                                <span class="text-rose-400">*</span>
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-400">
                                Gunakan toolbar untuk memformat teks,
                                menyisipkan gambar, video YouTube, dan mengatur
                                perataan tulisan (Align Justify).
                            </p>
                        </div>
                    </div>

                    <RichTextEditor
                        v-model="form.content"
                        placeholder="Mulai menulis konten materi pembelajaran kimia..."
                        min-height="420px"
                    />

                    <p
                        v-if="form.errors.content"
                        class="mt-1 text-xs text-rose-400"
                    >
                        {{ form.errors.content }}
                    </p>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link
                        :href="route('materials.index')"
                        class="rounded-xl border border-[#1b344d] bg-[#0d1e30] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-[#122840] hover:text-white"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#e5a824] px-6 py-2.5 text-sm font-semibold text-black shadow-lg shadow-[#e5a824]/20 transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:outline-none disabled:opacity-50"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin text-black"
                        />
                        <Check v-else class="h-4 w-4" />
                        <span>Simpan Materi Pembelajaran</span>
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
