<script setup lang="ts">
import { computed } from 'vue';
import { Check } from 'lucide-vue-next';

export interface PermissionDefinition {
    name: string;
    label: string;
    description?: string;
}

const props = withDefaults(
    defineProps<{
        moduleName: string;
        permissions: PermissionDefinition[];
        modelValue: string[];
        disabled?: boolean;
    }>(),
    {
        disabled: false,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string[]): void;
}>();

const allSelected = computed(() => {
    if (props.permissions.length === 0) return false;
    return props.permissions.every((p) => props.modelValue.includes(p.name));
});

const someSelected = computed(() => {
    return (
        !allSelected.value &&
        props.permissions.some((p) => props.modelValue.includes(p.name))
    );
});

const selectedCount = computed(() => {
    return props.permissions.filter((p) => props.modelValue.includes(p.name))
        .length;
});

const toggleSelectAll = () => {
    if (props.disabled) return;

    const modulePermNames = props.permissions.map((p) => p.name);
    let updated: string[];

    if (allSelected.value) {
        // Deselect all in this module
        updated = props.modelValue.filter(
            (name) => !modulePermNames.includes(name),
        );
    } else {
        // Select all in this module
        const combined = new Set([...props.modelValue, ...modulePermNames]);
        updated = Array.from(combined);
    }

    emit('update:modelValue', updated);
};

const togglePermission = (name: string) => {
    if (props.disabled) return;

    let updated: string[];
    if (props.modelValue.includes(name)) {
        updated = props.modelValue.filter((item) => item !== name);
    } else {
        updated = [...props.modelValue, name];
    }

    emit('update:modelValue', updated);
};
</script>

<template>
    <div
        class="overflow-hidden rounded-xl border border-[#14263b] bg-[#0c1c2e] transition hover:border-[#1a3552]"
    >
        <!-- Module Group Header -->
        <div
            class="flex items-center justify-between border-b border-[#14263b] bg-[#091624]/90 px-4 py-3 sm:px-5"
        >
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold tracking-wide text-white">
                    {{ moduleName }}
                </span>
                <span
                    class="rounded-full bg-[#12273e] px-2.5 py-0.5 text-[11px] font-medium text-slate-300"
                >
                    {{ selectedCount }} / {{ permissions.length }} Izin Aktif
                </span>
            </div>

            <!-- Select All Button -->
            <button
                type="button"
                v-if="!disabled"
                @click="toggleSelectAll"
                class="text-xs font-semibold text-[#e5a824] transition hover:text-[#f3bc47] focus:outline-none"
            >
                {{ allSelected ? 'Hapus Semua' : 'Pilih Semua' }}
            </button>
        </div>

        <!-- Permissions List Grid -->
        <div class="grid grid-cols-1 gap-2.5 p-3 sm:grid-cols-2 sm:p-4">
            <div
                v-for="perm in permissions"
                :key="perm.name"
                @click="togglePermission(perm.name)"
                :class="[
                    'group flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-all duration-150',
                    modelValue.includes(perm.name)
                        ? 'border-[#e5a824]/40 bg-[#14283f]/60'
                        : 'border-[#13263b] bg-[#0a1827]/60 hover:border-[#1d3855] hover:bg-[#0e2136]',
                    disabled ? 'cursor-not-allowed opacity-75' : '',
                ]"
            >
                <!-- Custom Checkbox -->
                <div
                    :class="[
                        'mt-0.5 flex h-4.5 w-4.5 flex-shrink-0 items-center justify-center rounded border transition-all duration-150',
                        modelValue.includes(perm.name)
                            ? 'border-[#e5a824] bg-[#e5a824] text-black shadow-sm'
                            : 'border-[#1f3c5c] bg-[#0c1a28] group-hover:border-slate-400',
                    ]"
                >
                    <Check
                        v-if="modelValue.includes(perm.name)"
                        class="h-3.5 w-3.5 stroke-[3]"
                    />
                </div>

                <!-- Label & Key -->
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold text-white">
                            {{ perm.label }}
                        </span>
                        <code
                            class="rounded bg-[#071320] px-1.5 py-0.5 font-mono text-[10px] text-slate-400"
                        >
                            {{ perm.name }}
                        </code>
                    </div>
                    <p
                        v-if="perm.description"
                        class="mt-1 text-[11px] leading-tight text-slate-400"
                    >
                        {{ perm.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
