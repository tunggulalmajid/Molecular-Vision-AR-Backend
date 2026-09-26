<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    UploadCloud,
    Box,
    FileCheck,
    X,
    ExternalLink,
    AlertCircle,
} from 'lucide-vue-next';

interface Props {
    modelValue: File | null;
    accept?: string;
    maxSizeMb?: number;
    existingUrl?: string | null;
    label?: string;
    description?: string;
    required?: boolean;
    error?: string;
}

const props = withDefaults(defineProps<Props>(), {
    accept: '.glb,.gltf,.obj',
    maxSizeMb: 50,
    existingUrl: null,
    label: 'Unggah Berkas Model 3D',
    description: 'Format yang didukung: .GLB, .GLTF, .OBJ (Maks. 50 MB)',
    required: false,
    error: '',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | null): void;
    (e: 'change', value: File | null): void;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const localError = ref('');

const displayError = computed(() => props.error || localError.value);

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getExtension = (filename: string): string => {
    return filename.split('.').pop()?.toUpperCase() || 'FILE';
};

const validateAndSetFile = (file: File) => {
    localError.value = '';

    // Check size
    if (file.size > props.maxSizeMb * 1024 * 1024) {
        localError.value = `Ukuran berkas (${formatFileSize(file.size)}) melebihi batas maksimal ${props.maxSizeMb} MB.`;
        return;
    }

    // Check extension
    if (props.accept) {
        const allowed = props.accept
            .split(',')
            .map((ext) => ext.trim().toLowerCase().replace('.', ''));
        const fileExt = file.name.split('.').pop()?.toLowerCase();
        if (!fileExt || !allowed.includes(fileExt)) {
            localError.value = `Format berkas .${fileExt} tidak didukung. Harap pilih: ${props.accept}.`;
            return;
        }
    }

    emit('update:modelValue', file);
    emit('change', file);
};

const handleFileInputChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        validateAndSetFile(file);
    }
};

const handleDrop = (event: DragEvent) => {
    isDragging.value = false;
    const file = event.dataTransfer?.files?.[0];
    if (file) {
        validateAndSetFile(file);
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const openFileDialog = () => {
    fileInput.value?.click();
};

const removeSelectedFile = () => {
    localError.value = '';
    emit('update:modelValue', null);
    emit('change', null);
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};
</script>

<template>
    <div class="space-y-2">
        <!-- Label & Status Header -->
        <div class="flex items-center justify-between">
            <label
                class="block text-xs font-semibold tracking-wider text-slate-300 uppercase"
            >
                {{ label }}
                <span v-if="required" class="text-rose-400">*</span>
            </label>
            <span class="text-[11px] text-slate-400">
                Maks. {{ maxSizeMb }} MB
            </span>
        </div>

        <!-- Hidden input file -->
        <input
            ref="fileInput"
            type="file"
            :accept="accept"
            class="hidden"
            @change="handleFileInputChange"
        />

        <!-- Case 1: New file is selected by user -->
        <div
            v-if="modelValue"
            class="flex items-center justify-between rounded-xl border border-emerald-500/40 bg-[#0c1e2e] p-4 shadow-lg transition-all"
        >
            <div class="flex min-w-0 items-center gap-3.5">
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400"
                >
                    <Box class="h-6 w-6" />
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="truncate text-sm font-semibold text-white">
                            {{ modelValue.name }}
                        </p>
                        <span
                            class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-400"
                        >
                            {{ getExtension(modelValue.name) }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-xs text-slate-400">
                        {{ formatFileSize(modelValue.size) }} &bull; Siap
                        diunggah ke S3
                    </p>
                </div>
            </div>

            <div class="ml-3 flex flex-shrink-0 items-center gap-2">
                <button
                    type="button"
                    @click="openFileDialog"
                    class="rounded-lg border border-[#1b344d] bg-[#091624] px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-[#14263b] hover:text-white"
                >
                    Ganti
                </button>
                <button
                    type="button"
                    @click="removeSelectedFile"
                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-500/10 hover:text-rose-400"
                    title="Hapus pilihan berkas"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </div>

        <!-- Case 2: Existing file on cloud (Edit mode, no new file selected) -->
        <div
            v-else-if="existingUrl"
            class="flex flex-col gap-3 rounded-xl border border-[#1b344d] bg-[#0c1a28] p-4 shadow-lg sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex min-w-0 items-center gap-3.5">
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl border border-[#e5a824]/20 bg-[#e5a824]/10 text-[#e5a824]"
                >
                    <FileCheck class="h-6 w-6" />
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">
                        Berkas Model 3D Tersimpan di Cloud S3
                    </p>
                    <a
                        :href="existingUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-0.5 inline-flex items-center gap-1 text-xs text-[#e5a824] hover:underline"
                    >
                        <span>Unduh / Tinjau Berkas Sumber</span>
                        <ExternalLink class="h-3 w-3" />
                    </a>
                </div>
            </div>

            <div class="flex flex-shrink-0 items-center gap-2">
                <button
                    type="button"
                    @click="openFileDialog"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-[#e5a824]/30 bg-[#e5a824]/10 px-3.5 py-1.5 text-xs font-semibold text-[#e5a824] transition hover:bg-[#e5a824]/20"
                >
                    <UploadCloud class="h-3.5 w-3.5" />
                    <span>Ganti Berkas 3D</span>
                </button>
            </div>
        </div>

        <!-- Case 3: Empty State (Drag and Drop Zone) -->
        <div
            v-else
            @click="openFileDialog"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop.prevent="handleDrop"
            class="group relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed p-6 text-center transition-all duration-200"
            :class="[
                isDragging
                    ? 'scale-[0.99] border-[#e5a824] bg-[#e5a824]/5'
                    : 'border-[#1b344d] bg-[#0c1a28]/60 hover:border-[#e5a824]/50 hover:bg-[#0c1a28]',
            ]"
        >
            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl border border-[#1b344d] bg-[#091624] text-slate-400 transition-transform group-hover:scale-110 group-hover:border-[#e5a824]/40 group-hover:text-[#e5a824]"
            >
                <UploadCloud class="h-6 w-6" />
            </div>

            <p class="mt-3 text-sm font-semibold text-white">
                <span class="text-[#e5a824] underline underline-offset-4"
                    >Klik untuk memilih</span
                >
                atau seret berkas 3D ke sini
            </p>
            <p class="mt-1 text-xs text-slate-400">
                {{ description }}
            </p>
        </div>

        <!-- Error Message -->
        <div
            v-if="displayError"
            class="flex items-center gap-1.5 pt-0.5 text-xs text-rose-400"
        >
            <AlertCircle class="h-3.5 w-3.5 flex-shrink-0" />
            <span>{{ displayError }}</span>
        </div>
    </div>
</template>
