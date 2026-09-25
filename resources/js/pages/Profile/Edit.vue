<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import {
    User as UserIcon,
    Shield,
    School,
    GraduationCap,
    Calendar,
    CheckCircle2,
    AlertCircle,
    BadgeCheck,
} from 'lucide-vue-next';

interface Degree {
    id_degree: number;
    name: string;
}

const props = defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    degrees?: Degree[];
}>();

const page = usePage();
const user = computed(() => page.props.auth?.user as any);
const userRoles = computed(() => (page.props.auth?.roles as string[]) || []);
const roleName = computed(() => userRoles.value[0] || 'User');

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const userInitials = computed(() => {
    const name = user.value?.name?.trim() || '';
    if (!name) return 'U';
    const parts = name.split(/\s+/);
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
});

const formatDate = (dateString?: string): string => {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }).format(date);
    } catch {
        return dateString;
    }
};
</script>

<template>
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <div class="max-w-5xl space-y-6">
            <!-- Flash Message Banner -->
            <div
                v-if="flashSuccess"
                class="flex items-center gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
            >
                <CheckCircle2 class="h-5 w-5 flex-shrink-0 text-emerald-400" />
                <p>{{ flashSuccess }}</p>
            </div>
            <div
                v-if="flashError"
                class="flex items-center gap-3 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-400"
            >
                <AlertCircle class="h-5 w-5 flex-shrink-0 text-rose-400" />
                <p>{{ flashError }}</p>
            </div>

            <!-- Page Header Card -->
            <div class="rounded-xl border border-[#14263b] bg-[#091624] p-6 shadow-xl sm:p-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-5">
                        <!-- Big Avatar with Gold Ring -->
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border-2 border-[#e5a824] bg-[#14283f] text-xl font-bold text-[#e5a824] shadow-lg shadow-[#e5a824]/10"
                        >
                            {{ userInitials }}
                        </div>

                        <div>
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-xl font-bold text-white sm:text-2xl">
                                    {{ user?.name }}
                                </h1>
                                <span
                                    v-if="user?.email_verified_at"
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-bold text-emerald-400"
                                    title="Email Terverifikasi"
                                >
                                    <BadgeCheck class="h-3 w-3" />
                                    <span>Terverifikasi</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 sm:text-sm">
                                {{ user?.email }}
                            </p>
                        </div>
                    </div>

                    <!-- Meta Badges (Role & School & Degree) -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1.5 rounded-lg border border-[#1b344d] bg-[#0c1a28] px-3 py-1.5 text-xs text-slate-300">
                            <Shield class="h-3.5 w-3.5 text-[#e5a824]" />
                            <span class="font-semibold text-white capitalize">{{ roleName }}</span>
                        </div>

                        <div v-if="user?.school" class="flex items-center gap-1.5 rounded-lg border border-[#1b344d] bg-[#0c1a28] px-3 py-1.5 text-xs text-slate-300">
                            <School class="h-3.5 w-3.5 text-sky-400" />
                            <span>{{ user.school }}</span>
                        </div>

                        <div v-if="user?.degree?.name" class="flex items-center gap-1.5 rounded-lg border border-[#1b344d] bg-[#0c1a28] px-3 py-1.5 text-xs text-slate-300">
                            <GraduationCap class="h-3.5 w-3.5 text-amber-400" />
                            <span>{{ user.degree.name }}</span>
                        </div>

                        <div class="flex items-center gap-1.5 rounded-lg border border-[#1b344d] bg-[#0c1a28] px-3 py-1.5 text-xs text-slate-400">
                            <Calendar class="h-3.5 w-3.5 text-slate-500" />
                            <span>Bergabung {{ formatDate(user?.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form 1: Profile Information -->
            <UpdateProfileInformationForm
                :must-verify-email="mustVerifyEmail"
                :status="status"
                :degrees="degrees"
            />

            <!-- Form 2: Password Update -->
            <UpdatePasswordForm />

            <!-- Form 3: Danger Zone -->
            <DeleteUserForm />
        </div>
    </AuthenticatedLayout>
</template>
