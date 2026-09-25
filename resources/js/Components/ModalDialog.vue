<script setup lang="ts">
import { watch } from 'vue';
import { X } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        show: boolean;
        title?: string;
        maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
    }>(),
    {
        title: '',
        maxWidth: 'md',
    }
);

const emit = defineEmits<{
    (e: 'close'): void;
}>();

watch(
    () => props.show,
    (isOpen) => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
);

const maxWidthClasses = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
};
</script>

<template>
    <teleport to="body">
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
            >
                <!-- Backdrop -->
                <div
                    @click="$emit('close')"
                    class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity"
                />

                <!-- Dialog Panel -->
                <transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="scale-95 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-95 opacity-0"
                >
                    <div
                        v-if="show"
                        :class="[
                            'relative w-full rounded-2xl border border-[#1a334d] bg-[#0c1a29] p-6 shadow-2xl transition-all',
                            maxWidthClasses[maxWidth],
                        ]"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-[#14263b] pb-4">
                            <h3 class="text-base sm:text-lg font-bold tracking-tight text-white">
                                {{ title }}
                            </h3>
                            <button
                                @click="$emit('close')"
                                type="button"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-[#14263b] hover:text-white focus:outline-none"
                                aria-label="Tutup Dialog"
                            >
                                <X class="h-4.5 w-4.5" :size="18" />
                            </button>
                        </div>

                        <!-- Content Slot -->
                        <div class="mt-5">
                            <slot />
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
