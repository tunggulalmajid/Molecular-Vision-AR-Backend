<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    Users,
    Atom,
    BookOpen,
    ShieldCheck,
    FileQuestion,
    MoreVertical,
    User as UserIcon,
    LogOut,
    Menu,
    X,
} from 'lucide-vue-next';

const page = usePage();
const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);
const userMenuRef = ref<HTMLElement | null>(null);

const user = computed(() => page.props.auth?.user as any);
const userRoles = computed(() => (page.props.auth?.roles as string[]) || []);

const userName = computed(() => user.value?.name || 'Dr. Aris Thorne');
const userRoleName = computed(() => {
    if (userRoles.value && userRoles.value.length > 0) {
        return userRoles.value[0];
    }
    return user.value?.role?.name || 'Super Admin';
});

const userInitials = computed(() => {
    const name = userName.value.trim();
    if (!name) return 'AT';
    const parts = name.split(/\s+/);
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
});

const navigation = [
    {
        name: 'Dashboard',
        href: route('dashboard'),
        active: route().current('dashboard'),
        icon: LayoutDashboard,
    },
    {
        name: 'Modul User',
        href: '#',
        active: false,
        icon: Users,
    },
    {
        name: 'Modul Molekul',
        href: '#',
        active: false,
        icon: Atom,
    },
    {
        name: 'Modul Materi',
        href: '#',
        active: false,
        icon: BookOpen,
    },
    {
        name: 'Modul RBAC & Akses',
        href: '#',
        active: false,
        icon: ShieldCheck,
    },
    {
        name: 'Modul Bank Soal & Kuis',
        href: '#',
        active: false,
        icon: FileQuestion,
    },
];

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
};

const handleClickOutside = (event: MouseEvent) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target as Node)) {
        userMenuOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div
        class="flex min-h-screen bg-[#06101c] font-sans text-slate-100 antialiased selection:bg-[#e5a824] selection:text-black"
    >
        <!-- Mobile Sidebar Overlay Backdrop -->
        <div
            v-if="mobileMenuOpen"
            @click="mobileMenuOpen = false"
            class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm transition-opacity lg:hidden"
        />

        <!-- Sidebar Navigation -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-[#14263b] bg-[#091624] transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
                mobileMenuOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <!-- Top Branding Header -->
            <div
                class="flex h-16 items-center justify-between border-b border-[#14263b] px-6 py-12"
            >
                <Link
                    :href="route('dashboard')"
                    class="flex items-center gap-3"
                >
                    <img
                        src="/MVAR.png"
                        alt="MVAR Logo"
                        class="h-8 w-auto object-contain sm:h-9"
                    />
                </Link>

                <!-- Close button on mobile -->
                <button
                    @click="mobileMenuOpen = false"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-[#13273c] hover:text-white lg:hidden"
                    aria-label="Tutup Menu"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- Main Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6">
                <div class="mb-3 px-3">
                    <span
                        class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                    >
                        MENU UTAMA
                    </span>
                </div>

                <nav class="space-y-1.5">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'group flex items-center gap-3 rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all duration-150',
                            item.active
                                ? 'bg-[#16273b] text-[#e5a824] shadow-sm'
                                : 'text-slate-300 hover:bg-[#112338] hover:text-white',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            class="h-5 w-5 shrink-0"
                            :class="
                                item.active
                                    ? 'text-[#e5a824]'
                                    : 'text-slate-400 group-hover:text-slate-200'
                            "
                        />
                        <span>{{ item.name }}</span>
                    </Link>
                </nav>
            </div>

            <!-- Bottom Profile Section with 3-dots Menu -->
            <div
                class="relative mt-auto border-t border-[#14263b] p-4"
                ref="userMenuRef"
            >
                <div class="flex items-center justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <!-- User Initials Avatar -->
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[#234261] bg-[#1c334b] text-xs font-bold text-slate-200"
                        >
                            {{ userInitials }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-white">
                                {{ userName }}
                            </p>
                            <p
                                class="truncate text-[11px] capitalize text-slate-400"
                            >
                                {{ userRoleName }}
                            </p>
                        </div>
                    </div>

                    <!-- Options Button (Three Vertical Dots) -->
                    <button
                        @click.stop="toggleUserMenu"
                        type="button"
                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-[#14263b] hover:text-white focus:outline-none"
                        title="Opsi Pengguna"
                    >
                        <MoreVertical class="h-5 w-5" />
                    </button>
                </div>

                <!-- Dropdown Popover Menu -->
                <transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="translate-y-2 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="translate-y-2 opacity-0"
                >
                    <div
                        v-if="userMenuOpen"
                        class="absolute bottom-16 right-4 z-50 w-48 rounded-xl border border-[#1b344d] bg-[#0c1c2e] p-1.5 shadow-2xl backdrop-blur-lg"
                    >
                        <Link
                            :href="route('profile.edit')"
                            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-medium text-slate-300 hover:bg-[#152a42] hover:text-white"
                            @click="userMenuOpen = false"
                        >
                            <UserIcon class="h-4 w-4 text-slate-400" />
                            Profil Saya
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300"
                            @click="userMenuOpen = false"
                        >
                            <LogOut class="h-4 w-4 text-rose-400" />
                            Keluar (Logout)
                        </Link>
                    </div>
                </transition>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex min-w-0 flex-1 flex-col bg-[#06101c]">
            <!-- Top Mobile Bar -->
            <header
                class="flex h-16 items-center justify-between border-b border-[#14263b] bg-[#091624] px-4 lg:hidden"
            >
                <div class="flex items-center gap-3">
                    <button
                        @click="mobileMenuOpen = true"
                        class="rounded-lg p-2 text-slate-400 hover:bg-[#13273c] hover:text-white focus:outline-none"
                        aria-label="Buka Menu"
                    >
                        <Menu class="h-6 w-6" />
                    </button>
                    <Link :href="route('dashboard')" class="flex items-center">
                        <img
                            src="/MVAR.png"
                            alt="MVAR Logo"
                            class="h-7 w-auto object-contain"
                        />
                    </Link>
                </div>

                <!-- User Avatar on Mobile -->
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full border border-[#234261] bg-[#1c334b] text-xs font-bold text-slate-200"
                >
                    {{ userInitials }}
                </div>
            </header>

            <!-- Page Main Body -->
            <main class="flex-1 overflow-y-auto p-6 sm:p-8 lg:p-10 xl:p-12">
                <slot />
            </main>
        </div>
    </div>
</template>
