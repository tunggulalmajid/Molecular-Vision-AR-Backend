<script setup lang="ts">
import { ref } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        modelValue: string;
        type?: string;
        placeholder?: string;
        required?: boolean;
        autofocus?: boolean;
        autocomplete?: string;
        showPasswordToggle?: boolean;
    }>(),
    {
        type: 'text',
        placeholder: '',
        required: false,
        autofocus: false,
        autocomplete: 'off',
        showPasswordToggle: false,
    },
);

defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const isPasswordVisible = ref(false);

const togglePasswordVisibility = () => {
    isPasswordVisible.value = !isPasswordVisible.value;
};
</script>

<template>
    <div class="relative w-full">
        <input
            ref="inputRef"
            :type="
                showPasswordToggle
                    ? isPasswordVisible
                        ? 'text'
                        : 'password'
                    : type
            "
            :value="modelValue"
            @input="
                $emit(
                    'update:modelValue',
                    ($event.target as HTMLInputElement).value,
                )
            "
            :placeholder="placeholder"
            :required="required"
            :autofocus="autofocus"
            :autocomplete="autocomplete"
            :class="[
                'w-full rounded-lg border border-[#1b344d] bg-[#0c1a28] py-3 text-sm text-white placeholder-slate-500 transition-all duration-150',
                'focus:border-[#2e5984] focus:ring-1 focus:ring-[#2e5984] focus:outline-none',
                showPasswordToggle ? 'pr-11 pl-4' : 'px-4',
            ]"
        />

        <button
            v-if="showPasswordToggle"
            type="button"
            @click="togglePasswordVisibility"
            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-slate-200 focus:outline-none"
            tabindex="-1"
            aria-label="Toggle password visibility"
        >
            <Eye v-if="!isPasswordVisible" class="h-4.5 w-4.5" :size="18" />
            <EyeOff v-else class="h-4.5 w-4.5" :size="18" />
        </button>
    </div>
</template>
