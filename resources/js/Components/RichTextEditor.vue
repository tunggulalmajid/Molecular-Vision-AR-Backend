<script setup lang="ts">
import { ref, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Image from '@tiptap/extension-image';
import Youtube from '@tiptap/extension-youtube';
import Link from '@tiptap/extension-link';
import ModalDialog from '@/Components/ModalDialog.vue';
import DarkTextInput from '@/Components/DarkTextInput.vue';
import {
    Bold,
    Italic,
    Underline as UnderlineIcon,
    Strikethrough,
    Code,
    Heading1,
    Heading2,
    Heading3,
    Pilcrow,
    AlignLeft,
    AlignCenter,
    AlignRight,
    AlignJustify,
    List,
    ListOrdered,
    Quote,
    Minus,
    Link2,
    Unlink,
    ImagePlus,
    Video,
    Undo2,
    Redo2,
    RemoveFormatting,
    UploadCloud,
    Link as LinkIcon,
    Loader2,
} from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
        editable?: boolean;
        minHeight?: string;
    }>(),
    {
        modelValue: '',
        placeholder: 'Tuliskan isi materi pembelajaran di sini...',
        editable: true,
        minHeight: '360px',
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

// Modals state
const isImageModalOpen = ref(false);
const imageTab = ref<'upload' | 'url'>('upload');
const imageUrlInput = ref('');
const imageFileInput = ref<HTMLInputElement | null>(null);
const isUploadingImage = ref(false);
const imageUploadError = ref('');

const isYoutubeModalOpen = ref(false);
const youtubeUrlInput = ref('');
const youtubeError = ref('');

const isLinkModalOpen = ref(false);
const linkUrlInput = ref('');

// Initialize TipTap Editor
const editor = useEditor({
    content: props.modelValue,
    editable: props.editable,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3],
            },
        }),
        Underline,
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        Image.configure({
            inline: false,
            allowBase64: true,
            HTMLAttributes: {
                class: 'max-w-full rounded-xl border border-[#1b344d] my-4 shadow-lg mx-auto',
            },
        }),
        Youtube.configure({
            inline: false,
            width: 640,
            height: 360,
            HTMLAttributes: {
                class: 'w-full aspect-video rounded-xl border border-[#1b344d] my-4 shadow-lg overflow-hidden',
            },
        }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-[#e5a824] underline hover:text-[#f3be4d] cursor-pointer',
            },
        }),
    ],
    onUpdate: () => {
        if (!editor.value) return;
        emit('update:modelValue', editor.value.getHTML());
    },
});

// Sync modelValue changes from outside
watch(
    () => props.modelValue,
    (newValue) => {
        if (!editor.value) return;
        const isSame = editor.value.getHTML() === newValue;
        if (!isSame) {
            editor.value.commands.setContent(newValue, { emitUpdate: false });
        }
    },
);

// Watch editable prop
watch(
    () => props.editable,
    (val) => {
        editor.value?.setEditable(val);
    },
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});

// Image handlers
const openImageModal = () => {
    imageUrlInput.value = '';
    imageUploadError.value = '';
    imageTab.value = 'upload';
    isImageModalOpen.value = true;
};

const handleInsertImageUrl = () => {
    if (!imageUrlInput.value.trim() || !editor.value) return;
    editor.value
        .chain()
        .focus()
        .setImage({ src: imageUrlInput.value.trim() })
        .run();
    isImageModalOpen.value = false;
    imageUrlInput.value = '';
};

const handleFileUpload = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        imageUploadError.value =
            'Hanya file gambar (JPEG, PNG, WEBP, GIF) yang diperbolehkan.';
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        imageUploadError.value = 'Ukuran gambar maksimal adalah 5MB.';
        return;
    }

    isUploadingImage.value = true;
    imageUploadError.value = '';

    const formData = new FormData();
    formData.append('image', file);

    try {
        const csrfToken =
            (
                document.querySelector(
                    'meta[name="csrf-token"]',
                ) as HTMLMetaElement
            )?.content || '';
        const response = await fetch(route('materials.upload-image'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            const errData = await response.json().catch(() => null);
            throw new Error(
                errData?.message ||
                    'Gagal mengunggah gambar. Silakan coba lagi.',
            );
        }

        const data = await response.json();
        if (data.url && editor.value) {
            editor.value.chain().focus().setImage({ src: data.url }).run();
            isImageModalOpen.value = false;
        }
    } catch (err: unknown) {
        imageUploadError.value =
            err instanceof Error
                ? err.message
                : 'Terjadi kesalahan saat mengunggah gambar.';
    } finally {
        isUploadingImage.value = false;
        if (imageFileInput.value) {
            imageFileInput.value.value = '';
        }
    }
};

// YouTube handlers
const openYoutubeModal = () => {
    youtubeUrlInput.value = '';
    youtubeError.value = '';
    isYoutubeModalOpen.value = true;
};

const handleInsertYoutube = () => {
    if (!youtubeUrlInput.value.trim() || !editor.value) return;

    const url = youtubeUrlInput.value.trim();
    // Validate youtube url regex roughly
    if (!url.includes('youtube.com') && !url.includes('youtu.be')) {
        youtubeError.value = 'Mohon masukkan tautan video YouTube yang valid.';
        return;
    }

    editor.value.chain().focus().setYoutubeVideo({ src: url }).run();
    isYoutubeModalOpen.value = false;
    youtubeUrlInput.value = '';
};

// Link handlers
const openLinkModal = () => {
    if (!editor.value) return;
    const previousUrl = editor.value.getAttributes('link').href || '';
    linkUrlInput.value = previousUrl;
    isLinkModalOpen.value = true;
};

const handleSetLink = () => {
    if (!editor.value) return;

    if (!linkUrlInput.value.trim()) {
        editor.value.chain().focus().unsetLink().run();
        isLinkModalOpen.value = false;
        return;
    }

    let url = linkUrlInput.value.trim();
    if (!/^https?:\/\//i.test(url)) {
        url = 'https://' + url;
    }

    editor.value
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: url })
        .run();
    isLinkModalOpen.value = false;
};

const handleUnlink = () => {
    editor.value?.chain().focus().unsetLink().run();
};
</script>

<template>
    <div
        class="w-full overflow-hidden rounded-xl border border-[#1a344f] bg-[#071322] shadow-xl"
    >
        <!-- Toolbar -->
        <div
            v-if="editor && editable"
            class="flex flex-wrap items-center gap-1 border-b border-[#14263b] bg-[#0a1827] p-2 text-slate-300"
        >
            <!-- History -->
            <button
                type="button"
                @click="editor.chain().focus().undo().run()"
                :disabled="!editor.can().undo()"
                class="rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white disabled:opacity-30 disabled:hover:bg-transparent"
                title="Undo (Ctrl+Z)"
            >
                <Undo2 class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().redo().run()"
                :disabled="!editor.can().redo()"
                class="rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white disabled:opacity-30 disabled:hover:bg-transparent"
                title="Redo (Ctrl+Y)"
            >
                <Redo2 class="h-4 w-4" />
            </button>

            <div class="mx-1 h-5 w-[1px] bg-[#1a344f]" />

            <!-- Headings & Paragraph -->
            <button
                type="button"
                @click="editor.chain().focus().setParagraph().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('paragraph')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Paragraf Normal"
            >
                <Pilcrow class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="
                    editor.chain().focus().toggleHeading({ level: 1 }).run()
                "
                :class="[
                    'rounded-lg p-1.5 font-bold transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('heading', { level: 1 })
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Heading 1"
            >
                <Heading1 class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="
                    editor.chain().focus().toggleHeading({ level: 2 }).run()
                "
                :class="[
                    'rounded-lg p-1.5 font-bold transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('heading', { level: 2 })
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Heading 2"
            >
                <Heading2 class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="
                    editor.chain().focus().toggleHeading({ level: 3 }).run()
                "
                :class="[
                    'rounded-lg p-1.5 font-bold transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('heading', { level: 3 })
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Heading 3"
            >
                <Heading3 class="h-4 w-4" />
            </button>

            <div class="mx-1 h-5 w-[1px] bg-[#1a344f]" />

            <!-- Basic Text Formatting -->
            <button
                type="button"
                @click="editor.chain().focus().toggleBold().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('bold')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Tebal (Ctrl+B)"
            >
                <Bold class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleItalic().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('italic')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Miring (Ctrl+I)"
            >
                <Italic class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleUnderline().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('underline')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Garis Bawah (Ctrl+U)"
            >
                <UnderlineIcon class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleStrike().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('strike')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Coret"
            >
                <Strikethrough class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleCode().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('code')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Kode Inline"
            >
                <Code class="h-4 w-4" />
            </button>

            <div class="mx-1 h-5 w-[1px] bg-[#1a344f]" />

            <!-- Alignment (Including Justify) -->
            <button
                type="button"
                @click="editor.chain().focus().setTextAlign('left').run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive({ textAlign: 'left' })
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Rata Kiri"
            >
                <AlignLeft class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().setTextAlign('center').run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive({ textAlign: 'center' })
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Rata Tengah"
            >
                <AlignCenter class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().setTextAlign('right').run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive({ textAlign: 'right' })
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Rata Kanan"
            >
                <AlignRight class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().setTextAlign('justify').run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive({ textAlign: 'justify' })
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Rata Kanan-Kiri (Justify)"
            >
                <AlignJustify class="h-4 w-4" />
            </button>

            <div class="mx-1 h-5 w-[1px] bg-[#1a344f]" />

            <!-- Lists & Blocks -->
            <button
                type="button"
                @click="editor.chain().focus().toggleBulletList().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('bulletList')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Daftar Titik (Bullet List)"
            >
                <List class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleOrderedList().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('orderedList')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Daftar Nomor (Numbered List)"
            >
                <ListOrdered class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleBlockquote().run()"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('blockquote')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Kutipan (Blockquote)"
            >
                <Quote class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="editor.chain().focus().setHorizontalRule().run()"
                class="rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white"
                title="Garis Pembatas"
            >
                <Minus class="h-4 w-4" />
            </button>

            <div class="mx-1 h-5 w-[1px] bg-[#1a344f]" />

            <!-- Media: Image, YouTube, Link -->
            <button
                type="button"
                @click="openImageModal"
                class="rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white"
                title="Sisipkan Gambar"
            >
                <ImagePlus class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="openYoutubeModal"
                class="rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white"
                title="Sisipkan Video YouTube"
            >
                <Video class="h-4 w-4" />
            </button>
            <button
                type="button"
                @click="openLinkModal"
                :class="[
                    'rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white',
                    editor.isActive('link')
                        ? 'bg-[#18324e] text-[#e5a824]'
                        : '',
                ]"
                title="Tautan / Link"
            >
                <Link2 class="h-4 w-4" />
            </button>
            <button
                v-if="editor.isActive('link')"
                type="button"
                @click="handleUnlink"
                class="rounded-lg p-1.5 text-rose-400 transition hover:bg-rose-500/20"
                title="Hapus Link"
            >
                <Unlink class="h-4 w-4" />
            </button>

            <div class="mx-1 h-5 w-[1px] bg-[#1a344f]" />

            <!-- Clear formatting -->
            <button
                type="button"
                @click="
                    editor.chain().focus().unsetAllMarks().clearNodes().run()
                "
                class="rounded-lg p-1.5 transition hover:bg-[#14263b] hover:text-white"
                title="Hapus Semua Format"
            >
                <RemoveFormatting class="h-4 w-4" />
            </button>
        </div>

        <!-- Editor Content Area -->
        <div
            class="editor-wrapper px-5 py-4 focus:outline-none"
            :style="{ minHeight }"
            @click="editor?.chain().focus().run()"
        >
            <EditorContent
                :editor="editor"
                class="tiptap-content focus:outline-none"
            />
        </div>

        <!-- IMAGE UPLOAD / URL MODAL -->
        <ModalDialog
            :show="isImageModalOpen"
            title="Sisipkan Gambar ke Materi"
            max-width="md"
            @close="isImageModalOpen = false"
        >
            <div class="space-y-4">
                <!-- Tab Selector -->
                <div
                    class="flex rounded-xl border border-[#14263b] bg-[#091624] p-1"
                >
                    <button
                        type="button"
                        @click="imageTab = 'upload'"
                        :class="[
                            'flex flex-1 items-center justify-center gap-2 rounded-lg py-1.5 text-xs font-semibold transition',
                            imageTab === 'upload'
                                ? 'bg-[#e5a824] text-black shadow-md'
                                : 'text-slate-400 hover:text-white',
                        ]"
                    >
                        <UploadCloud class="h-3.5 w-3.5" />
                        <span>Upload dari Perangkat</span>
                    </button>
                    <button
                        type="button"
                        @click="imageTab = 'url'"
                        :class="[
                            'flex flex-1 items-center justify-center gap-2 rounded-lg py-1.5 text-xs font-semibold transition',
                            imageTab === 'url'
                                ? 'bg-[#e5a824] text-black shadow-md'
                                : 'text-slate-400 hover:text-white',
                        ]"
                    >
                        <LinkIcon class="h-3.5 w-3.5" />
                        <span>Tautan URL Gambar</span>
                    </button>
                </div>

                <!-- Tab 1: Upload from device -->
                <div v-if="imageTab === 'upload'" class="space-y-3">
                    <div
                        class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#1f3a58] bg-[#0a1827]/60 p-6 text-center transition hover:border-[#e5a824]/50"
                        @click="imageFileInput?.click()"
                    >
                        <input
                            ref="imageFileInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFileUpload"
                        />
                        <div
                            class="mb-2 rounded-full bg-[#112438] p-3 text-[#e5a824]"
                        >
                            <UploadCloud class="h-6 w-6" />
                        </div>
                        <p class="text-sm font-medium text-white">
                            Klik untuk memilih gambar dari komputer
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Mendukung format PNG, JPG, WEBP, GIF (Maks. 5MB)
                        </p>
                    </div>

                    <div
                        v-if="isUploadingImage"
                        class="flex items-center justify-center gap-2 py-2 text-xs text-[#e5a824]"
                    >
                        <Loader2 class="h-4 w-4 animate-spin" />
                        <span>Sedang mengunggah dan memproses gambar...</span>
                    </div>

                    <div
                        v-if="imageUploadError"
                        class="rounded-lg border border-rose-500/20 bg-rose-500/10 p-2.5 text-xs text-rose-300"
                    >
                        {{ imageUploadError }}
                    </div>
                </div>

                <!-- Tab 2: Image URL -->
                <div v-else class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-slate-300"
                        >
                            URL Gambar (Contoh: https://example.com/image.png)
                        </label>
                        <DarkTextInput
                            v-model="imageUrlInput"
                            placeholder="https://..."
                            @keydown.enter.prevent="handleInsertImageUrl"
                        />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isImageModalOpen = false"
                            class="rounded-lg border border-[#1b344d] bg-[#0d1e30] px-3.5 py-1.5 text-xs font-medium text-slate-300 hover:bg-[#122840]"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="handleInsertImageUrl"
                            :disabled="!imageUrlInput.trim()"
                            class="rounded-lg bg-[#e5a824] px-4 py-1.5 text-xs font-semibold text-black hover:bg-[#d49718] disabled:opacity-50"
                        >
                            Sisipkan Gambar
                        </button>
                    </div>
                </div>
            </div>
        </ModalDialog>

        <!-- YOUTUBE EMBED MODAL -->
        <ModalDialog
            :show="isYoutubeModalOpen"
            title="Sisipkan Video YouTube"
            max-width="md"
            @close="isYoutubeModalOpen = false"
        >
            <div class="space-y-4">
                <p class="text-xs text-slate-400">
                    Masukkan URL video YouTube materi (misal:
                    <code>https://www.youtube.com/watch?v=...</code> atau
                    <code>https://youtu.be/...</code>).
                </p>
                <div>
                    <label
                        class="mb-1 block text-xs font-medium text-slate-300"
                    >
                        URL Video YouTube
                    </label>
                    <DarkTextInput
                        v-model="youtubeUrlInput"
                        placeholder="https://www.youtube.com/watch?v=..."
                        @keydown.enter.prevent="handleInsertYoutube"
                    />
                </div>

                <div
                    v-if="youtubeError"
                    class="rounded-lg border border-rose-500/20 bg-rose-500/10 p-2 text-xs text-rose-300"
                >
                    {{ youtubeError }}
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button
                        type="button"
                        @click="isYoutubeModalOpen = false"
                        class="rounded-lg border border-[#1b344d] bg-[#0d1e30] px-3.5 py-1.5 text-xs font-medium text-slate-300 hover:bg-[#122840]"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="handleInsertYoutube"
                        :disabled="!youtubeUrlInput.trim()"
                        class="rounded-lg bg-[#e5a824] px-4 py-1.5 text-xs font-semibold text-black hover:bg-[#d49718] disabled:opacity-50"
                    >
                        Sematkan Video
                    </button>
                </div>
            </div>
        </ModalDialog>

        <!-- HYPERLINK MODAL -->
        <ModalDialog
            :show="isLinkModalOpen"
            title="Kelola Tautan (Link)"
            max-width="sm"
            @close="isLinkModalOpen = false"
        >
            <div class="space-y-4">
                <div>
                    <label
                        class="mb-1 block text-xs font-medium text-slate-300"
                    >
                        URL Tautan
                    </label>
                    <DarkTextInput
                        v-model="linkUrlInput"
                        placeholder="https://..."
                        @keydown.enter.prevent="handleSetLink"
                    />
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button
                        type="button"
                        @click="isLinkModalOpen = false"
                        class="rounded-lg border border-[#1b344d] bg-[#0d1e30] px-3.5 py-1.5 text-xs font-medium text-slate-300 hover:bg-[#122840]"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="handleSetLink"
                        class="rounded-lg bg-[#e5a824] px-4 py-1.5 text-xs font-semibold text-black hover:bg-[#d49718]"
                    >
                        Terapkan
                    </button>
                </div>
            </div>
        </ModalDialog>
    </div>
</template>

<style>
/* TipTap Prose Styling for MVAR Dark Theme */
.tiptap-content .tiptap {
    outline: none !important;
    color: #cbd5e1;
    font-size: 0.95rem;
    line-height: 1.7;
}

.tiptap-content .tiptap p {
    margin-bottom: 0.75rem;
}

.tiptap-content .tiptap p:last-child {
    margin-bottom: 0;
}

.tiptap-content .tiptap h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.tiptap-content .tiptap h2 {
    font-size: 1.35rem;
    font-weight: 600;
    color: #ffffff;
    margin-top: 1rem;
    margin-bottom: 0.4rem;
    line-height: 1.35;
}

.tiptap-content .tiptap h3 {
    font-size: 1.15rem;
    font-weight: 600;
    color: #e2e8f0;
    margin-top: 0.85rem;
    margin-bottom: 0.35rem;
    line-height: 1.4;
}

.tiptap-content .tiptap ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 0.75rem;
}

.tiptap-content .tiptap ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 0.75rem;
}

.tiptap-content .tiptap li {
    margin-bottom: 0.25rem;
}

.tiptap-content .tiptap blockquote {
    border-left: 3px solid #e5a824;
    background-color: rgba(14, 34, 56, 0.4);
    padding: 0.5rem 1rem;
    margin: 0.75rem 0;
    font-style: italic;
    color: #94a3b8;
    border-radius: 0 0.5rem 0.5rem 0;
}

.tiptap-content .tiptap hr {
    border: none;
    border-top: 1px solid #1a344f;
    margin: 1.5rem 0;
}

.tiptap-content .tiptap code {
    background-color: #0c1a28;
    border: 1px solid #162c44;
    color: #e5a824;
    padding: 0.15rem 0.35rem;
    border-radius: 0.375rem;
    font-family: monospace;
    font-size: 0.85em;
}

.tiptap-content .tiptap img {
    max-width: 100%;
    height: auto;
    border-radius: 0.75rem;
    border: 1px solid #1a344f;
    margin: 1rem auto;
    display: block;
}

.tiptap-content .tiptap iframe {
    width: 100%;
    aspect-ratio: 16 / 9;
    border-radius: 0.75rem;
    border: 1px solid #1a344f;
    margin: 1rem auto;
    display: block;
}

/* Text alignment support */
.tiptap-content .tiptap [style*='text-align: left'] {
    text-align: left;
}

.tiptap-content .tiptap [style*='text-align: center'] {
    text-align: center;
}

.tiptap-content .tiptap [style*='text-align: right'] {
    text-align: right;
}

.tiptap-content .tiptap [style*='text-align: justify'] {
    text-align: justify;
}
</style>
