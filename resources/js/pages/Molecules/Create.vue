<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import FileUploadZone from '@/Components/FileUploadZone.vue';
import { ArrowLeft, Check, Loader2, Atom, Sparkles } from 'lucide-vue-next';

const form = useForm({
    name: '',
    formula: '',
    shape: '',
    bent: '',
    bond_type: '',
    description: '',
    model_3d_file: null as File | null,
});

const submit = () => {
    form.post(route('molecules.store'), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Tambah Model Molekul Kimia" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Back navigation & Title Header -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('molecules.index')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#1b344d] bg-[#0c1a28] text-slate-300 transition hover:bg-[#14263b] hover:text-white"
                        title="Kembali ke Katalog Molekul"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-white"
                        >
                            Tambah Model Molekul Kimia
                        </h1>
                        <p class="mt-0.5 text-xs text-slate-400">
                            Unggah berkas model 3D (.glb) dan konfigurasikan
                            parameter geometri molekul untuk visualisasi AR.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Section 1: Chemical & Geometric Parameters -->
                <div
                    class="space-y-5 rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <div
                        class="flex items-center gap-2 border-b border-[#14263b] pb-2"
                    >
                        <Atom class="h-4 w-4 text-[#e5a824]" />
                        <h2
                            class="text-sm font-semibold tracking-wider text-white uppercase"
                        >
                            Informasi & Parameter Kimiawi
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <!-- Nama Molekul -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Nama Molekul
                                <span class="text-rose-400">*</span>
                            </label>
                            <DarkTextInput
                                v-model="form.name"
                                placeholder="Contoh: Air, Metana, Karbon Dioksida..."
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Rumus Kimia -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Rumus Kimia
                                <span class="text-rose-400">*</span>
                            </label>
                            <DarkTextInput
                                v-model="form.formula"
                                placeholder="Contoh: H2O, CH4, CO2, NH3..."
                                required
                            />
                            <p
                                v-if="form.errors.formula"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.formula }}
                            </p>
                        </div>

                        <!-- Bentuk Geometri Molekul -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Bentuk Geometri Molekul
                                <span class="text-rose-400">*</span>
                            </label>
                            <DarkTextInput
                                v-model="form.shape"
                                placeholder="Contoh: Tetrahedral, Linier, Trigonal Planar, Bengkok..."
                                required
                            />
                            <p
                                v-if="form.errors.shape"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.shape }}
                            </p>
                        </div>

                        <!-- Sudut Ikatan (Bent) -->
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Sudut Ikatan / Geometri Tekukan
                                <span class="text-rose-400">*</span>
                            </label>
                            <DarkTextInput
                                v-model="form.bent"
                                placeholder="Contoh: 104.5°, 109.5°, 120°, 180°..."
                                required
                            />
                            <p
                                v-if="form.errors.bent"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.bent }}
                            </p>
                        </div>

                        <!-- Tipe Ikatan (Bond Type) -->
                        <div class="md:col-span-2">
                            <label
                                class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >
                                Jenis Ikatan Kimia
                                <span class="text-rose-400">*</span>
                            </label>
                            <DarkTextInput
                                v-model="form.bond_type"
                                placeholder="Contoh: Kovalen Tunggal Polar, Kovalen Rangkap Dua Nonpolar..."
                                required
                            />
                            <p
                                v-if="form.errors.bond_type"
                                class="mt-1 text-xs text-rose-400"
                            >
                                {{ form.errors.bond_type }}
                            </p>
                        </div>

                        <!-- Deskripsi / Catatan Molekul -->
                        <div class="md:col-span-2">
                            <div
                                class="mb-1.5 flex items-center justify-between"
                            >
                                <label
                                    class="block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                                >
                                    Deskripsi & Pembahasan Molekul
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">
                                    {{ form.description.length }} karakter
                                </span>
                            </div>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Tuliskan penjelasan detail mengenai karakteristik, sifat polaritas, peran elektron bebas, dan informasi penting lainnya tentang molekul ini..."
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
                </div>

                <!-- Section 2: 3D Model File Upload (S3) -->
                <div
                    class="space-y-4 rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl"
                >
                    <div
                        class="flex items-center gap-2 border-b border-[#14263b] pb-2"
                    >
                        <Sparkles class="h-4 w-4 text-[#e5a824]" />
                        <h2
                            class="text-sm font-semibold tracking-wider text-white uppercase"
                        >
                            Berkas Model 3D Augmented Reality
                        </h2>
                    </div>

                    <FileUploadZone
                        v-model="form.model_3d_file"
                        accept=".glb,.gltf,.obj"
                        :max-size-mb="50"
                        label="Berkas Model 3D (.GLB, .GLTF, .OBJ)"
                        description="Format standar AR: .GLB atau .GLTF (Maksimal 50 MB). Berkas akan disimpan secara aman di S3 DomaiNesia."
                        :required="true"
                        :error="form.errors.model_3d_file"
                    />
                </div>

                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link
                        :href="route('molecules.index')"
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
                        <span>Simpan Data Molekul</span>
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
