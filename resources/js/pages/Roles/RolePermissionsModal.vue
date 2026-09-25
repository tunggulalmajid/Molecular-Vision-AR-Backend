<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalDialog from '@/Components/ModalDialog.vue';
import PermissionGroupCard, {
    type PermissionDefinition,
} from '@/Components/PermissionGroupCard.vue';
import type { RoleItem } from './RoleFormModal.vue';
import { ShieldCheck, AlertCircle, Loader2 } from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    role: RoleItem | null;
    groupedPermissions: Record<string, PermissionDefinition[]>;
    totalPermissionsCount: number;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const selectedPermissions = ref<string[]>([]);
const isSuperAdmin = computed(
    () => props.role?.name?.toLowerCase() === 'super admin',
);

const allAvailablePermissions = computed(() => {
    const list: string[] = [];
    for (const group of Object.values(props.groupedPermissions)) {
        for (const p of group) {
            list.push(p.name);
        }
    }
    return list;
});

watch(
    () => props.show,
    (isOpen) => {
        if (!isOpen) return;

        if (props.role) {
            if (isSuperAdmin.value) {
                selectedPermissions.value = [...allAvailablePermissions.value];
            } else {
                selectedPermissions.value = (props.role.permissions || []).map(
                    (p) => p.name,
                );
            }
        } else {
            selectedPermissions.value = [];
        }
    },
);

const selectAllGlobal = () => {
    if (isSuperAdmin.value) return;
    selectedPermissions.value = [...allAvailablePermissions.value];
};

const deselectAllGlobal = () => {
    if (isSuperAdmin.value) return;
    selectedPermissions.value = [];
};

const form = useForm({
    permissions: [] as string[],
});

const submit = () => {
    if (!props.role || isSuperAdmin.value) return;

    form.permissions = selectedPermissions.value;
    form.put(route('roles.permissions.update', props.role.id), {
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
        :title="`Konfigurasi Hak Akses: ${role?.name || ''}`"
        max-width="2xl"
        @close="$emit('close')"
    >
        <div class="space-y-5">
            <!-- Super Admin Notice Banner -->
            <div
                v-if="isSuperAdmin"
                class="flex items-start gap-3 rounded-xl border border-[#e5a824]/30 bg-[#e5a824]/10 p-4 text-xs text-[#f4ca6c]"
            >
                <ShieldCheck class="h-5 w-5 flex-shrink-0 text-[#e5a824]" />
                <div>
                    <p class="font-bold text-[#e5a824]">
                        Peran Super Administrator
                    </p>
                    <p class="mt-0.5 leading-relaxed text-slate-300">
                        Peran ini secara otomatis memiliki hak akses penuh
                        (bypass) terhadap seluruh modul dan fungsionalitas di
                        sistem MVAR.
                    </p>
                </div>
            </div>

            <!-- Toolbar Header: Stats & Global Quick Actions -->
            <div
                v-if="!isSuperAdmin"
                class="flex flex-col gap-3 border-b border-[#14263b] pb-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400"
                        >Total Izin Terpilih:</span
                    >
                    <span
                        class="rounded-md bg-[#13273c] px-2 py-0.5 text-xs font-bold text-[#e5a824]"
                    >
                        {{ selectedPermissions.length }} /
                        {{ allAvailablePermissions.length }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="selectAllGlobal"
                        class="rounded-lg border border-[#1b344d] bg-[#091726] px-2.5 py-1 text-xs font-semibold text-slate-300 transition hover:bg-[#132840] hover:text-white"
                    >
                        Pilih Semua Izin
                    </button>
                    <button
                        type="button"
                        @click="deselectAllGlobal"
                        class="rounded-lg border border-[#1b344d] bg-[#091726] px-2.5 py-1 text-xs font-semibold text-slate-400 transition hover:bg-[#132840] hover:text-rose-400"
                    >
                        Hapus Semua
                    </button>
                </div>
            </div>

            <!-- Permissions Grouped by Module -->
            <div class="max-h-[60vh] space-y-4 overflow-y-auto pr-1">
                <PermissionGroupCard
                    v-for="(perms, moduleName) in groupedPermissions"
                    :key="moduleName"
                    :module-name="moduleName as string"
                    :permissions="perms"
                    v-model="selectedPermissions"
                    :disabled="isSuperAdmin"
                />
            </div>

            <!-- Modal Action Buttons -->
            <div
                class="flex items-center justify-end gap-3 border-t border-[#14263b] pt-4"
            >
                <button
                    type="button"
                    @click="$emit('close')"
                    class="rounded-lg border border-[#1b344d] bg-[#0d1e30] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-[#122840] hover:text-white focus:outline-none"
                >
                    {{ isSuperAdmin ? 'Tutup' : 'Batal' }}
                </button>
                <button
                    v-if="!isSuperAdmin"
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#e5a824] px-5 py-2.5 text-sm font-semibold text-black transition-all hover:bg-[#d49718] focus:ring-2 focus:ring-[#e5a824] focus:ring-offset-2 focus:ring-offset-[#0b1726] focus:outline-none disabled:opacity-60"
                >
                    <Loader2
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin text-black"
                    />
                    <span>Simpan Hak Akses</span>
                </button>
            </div>
        </div>
    </ModalDialog>
</template>
