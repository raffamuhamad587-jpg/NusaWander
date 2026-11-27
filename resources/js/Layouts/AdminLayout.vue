<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import {
    LayoutDashboard,
    Compass,
    FolderKanban,
    Camera,
    BadgeDollarSign,
    Settings,
    LogOut,
    Search,
    Menu,
    X,
    ChevronDown,
    UserRound,
} from 'lucide-vue-next'

defineProps({
    title: {
        type: String,
        default: '',
    },
})

const page = usePage()

const sidebarOpen = ref(false)
const userMenuOpen = ref(false)
const searchTerm = ref('')
const toasts = ref([])

const navItems = [
    { label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
    { label: 'Kelola Kategori', href: '/admin/categories', icon: FolderKanban },
    { label: 'Kelola Destinasi', href: '/admin/destinations', icon: Compass },
    { label: 'Kelola Foto', href: '/admin/destinations?photo_status=missing', icon: Camera },
    { label: 'Kelola Estimasi Harga', href: '/admin/destinations?price_status=missing', icon: BadgeDollarSign },
]

const currentUrl = computed(() => page.url || '/')

const currentLabel = computed(() => {
    const exactMatch = navItems.find((item) => currentUrl.value === item.href)
    if (exactMatch) return exactMatch.label

    const basePath = currentUrl.value.split('?')[0]
    const pathMatch = navItems
        .filter((item) => !item.href.includes('?'))
        .sort((a, b) => b.href.length - a.href.length)
        .find((item) => basePath === item.href || basePath.startsWith(item.href + '/'))

    return pathMatch?.label ?? 'Dashboard'
})

function isActive(href) {
    if (href.includes('?')) {
        return currentUrl.value === href
    }
    const basePath = currentUrl.value.split('?')[0]
    if (currentUrl.value.includes('photo_status=missing') || currentUrl.value.includes('price_status=missing')) {
        return false
    }
    return basePath === href || basePath.startsWith(href + '/')
}

function showToast(message, type = 'success') {
    const id = Date.now() + Math.random()
    toasts.value.push({ id, message, type })
    setTimeout(() => {
        toasts.value = toasts.value.filter((t) => t.id !== id)
    }, 5000)
}

function syncFlash(flash) {
    if (!flash) return
    if (flash.success) showToast(flash.success, 'success')
    if (flash.error) showToast(flash.error, 'error')
    if (flash.info) showToast(flash.info, 'info')
}

function onKeydown(event) {
    if (event.key === 'Escape') {
        sidebarOpen.value = false
        userMenuOpen.value = false
    }
}

function handleClickOutside(event) {
    if (!event.target.closest('.user-menu')) userMenuOpen.value = false
}

onMounted(() => {
    syncFlash(page.props.flash)
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', onKeydown)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', onKeydown)
})

watch(() => page.props.flash, (flash) => syncFlash(flash), { deep: true })

watch(() => page.url, () => {
    sidebarOpen.value = false
    userMenuOpen.value = false
})

function submitSearch() {
    const term = searchTerm.value.trim()
    router.get('/admin/destinations', term ? { search: term } : {}, { preserveState: false })
}

function logout() {
    router.post('/logout')
}
</script>

<template>
    <Head :title="title || currentLabel + ' | Admin NUSA WANDER'" />

    <div class="min-h-screen bg-brand-bg font-body text-brand-text">
        <!-- Sidebar -->
        <aside
            aria-label="Navigasi admin"
            :class="[
                'fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white border-r border-brand-dark/10 transition-transform duration-200 lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <!-- Brand -->
            <div class="flex items-center justify-between px-5 py-5 border-b border-brand-dark/10">
                <Link href="/admin/dashboard" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-dark text-white" aria-hidden="true">
                        <Compass class="h-5 w-5" />
                    </span>
                    <span class="leading-tight">
                        <span class="block font-heading text-sm font-bold text-brand-dark">NUSA WANDER</span>
                        <span class="block text-[11px] font-medium text-brand-muted">Portal Admin</span>
                    </span>
                </Link>
                <button
                    type="button"
                    class="rounded-lg p-2 text-brand-muted hover:bg-brand-bg lg:hidden min-h-[40px] min-w-[40px] flex items-center justify-center"
                    aria-label="Tutup menu admin"
                    @click="sidebarOpen = false"
                >
                    <X class="h-4 w-4" aria-hidden="true" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-5">
                <div>
                    <p class="px-2.5 pb-2 text-[11px] font-semibold text-brand-muted">Manajemen Katalog</p>
                    <div class="space-y-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            :aria-current="isActive(item.href) ? 'page' : undefined"
                            :class="[
                                'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors min-h-[44px]',
                                isActive(item.href)
                                    ? 'bg-brand-primary text-white font-semibold'
                                    : 'text-brand-text hover:bg-brand-bg',
                            ]"
                        >
                            <component :is="item.icon" class="h-4 w-4 shrink-0" aria-hidden="true" />
                            <span>{{ item.label }}</span>
                        </Link>
                    </div>
                </div>

                <div>
                    <p class="px-2.5 pb-2 text-[11px] font-semibold text-brand-muted">Akun &amp; Situs</p>
                    <div class="space-y-1">
                        <Link
                            href="/"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-brand-text hover:bg-brand-bg min-h-[44px]"
                        >
                            <Compass class="h-4 w-4 shrink-0" aria-hidden="true" />
                            <span>Lihat Situs Publik</span>
                        </Link>
                        <Link
                            href="/profile"
                            :class="[
                                'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors min-h-[44px]',
                                isActive('/profile') ? 'bg-brand-primary text-white font-semibold' : 'text-brand-text hover:bg-brand-bg',
                            ]"
                        >
                            <Settings class="h-4 w-4 shrink-0" aria-hidden="true" />
                            <span>Profil Admin</span>
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Logout -->
            <div class="border-t border-brand-dark/10 p-3.5">
                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-coral-text hover:bg-coral-light transition-colors min-h-[44px]"
                    @click="logout"
                >
                    <LogOut class="h-4 w-4 shrink-0" aria-hidden="true" />
                    <span>Keluar</span>
                </button>
            </div>
        </aside>

        <!-- Mobile overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-brand-dark/45 lg:hidden"
            aria-hidden="true"
            @click="sidebarOpen = false"
        />

        <!-- Main -->
        <div class="flex min-h-screen flex-col lg:ml-64">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 border-b border-brand-dark/10 bg-white">
                <div class="flex h-16 items-center gap-4 px-4 sm:px-6">
                    <button
                        type="button"
                        :aria-expanded="sidebarOpen"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-brand-dark/15 px-3 py-2 text-xs font-semibold text-brand-dark hover:bg-brand-bg lg:hidden min-h-[44px]"
                        @click="sidebarOpen = true"
                    >
                        <Menu class="h-4 w-4" aria-hidden="true" />
                        <span>Menu</span>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="hidden items-center gap-2 text-sm sm:flex">
                        <span class="text-brand-muted">Admin</span>
                        <span class="text-brand-muted/50">/</span>
                        <span class="font-semibold text-brand-dark">{{ currentLabel }}</span>
                    </div>

                    <!-- Search -->
                    <form class="mx-auto hidden w-full max-w-md md:block" @submit.prevent="submitSearch">
                        <label for="admin-topbar-search" class="sr-only">Cari destinasi</label>
                        <div class="relative">
                            <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-muted" aria-hidden="true" />
                            <input
                                id="admin-topbar-search"
                                v-model="searchTerm"
                                type="search"
                                placeholder="Cari nama destinasi atau kota..."
                                class="w-full rounded-lg border border-brand-dark/15 bg-brand-bg py-2 pl-9 pr-4 text-sm text-brand-dark placeholder:text-brand-muted focus:border-brand-primary focus:bg-white min-h-[40px]"
                            >
                        </div>
                    </form>

                    <!-- User dropdown -->
                    <div class="ml-auto user-menu relative">
                        <button
                            type="button"
                            :aria-expanded="userMenuOpen"
                            class="flex items-center gap-2 rounded-lg border border-brand-dark/10 px-2.5 py-1.5 hover:bg-brand-bg transition-colors min-h-[44px]"
                            @click="userMenuOpen = !userMenuOpen"
                        >
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-brand-dark text-white" aria-hidden="true">
                                <UserRound class="h-4 w-4" />
                            </span>
                            <span class="hidden text-left leading-tight sm:block">
                                <span class="block text-xs font-semibold text-brand-dark">{{ page.props.auth?.user?.name || 'Admin' }}</span>
                                <span class="block text-[11px] text-brand-muted">Administrator</span>
                            </span>
                            <ChevronDown class="hidden h-3.5 w-3.5 text-brand-muted sm:block" aria-hidden="true" />
                        </button>

                        <div
                            v-if="userMenuOpen"
                            class="absolute right-0 top-full z-50 mt-2 w-56 rounded-xl border border-brand-dark/10 bg-white p-1.5 shadow-lg"
                        >
                            <div class="border-b border-brand-dark/10 px-3 py-2">
                                <p class="truncate text-sm font-semibold text-brand-dark">{{ page.props.auth?.user?.name }}</p>
                                <p class="truncate text-xs text-brand-muted">{{ page.props.auth?.user?.email }}</p>
                            </div>
                            <Link href="/profile" class="mt-1 block rounded-lg px-3 py-2 text-sm text-brand-text hover:bg-brand-bg">
                                Pengaturan Profil
                            </Link>
                            <button
                                type="button"
                                class="block w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-coral-text hover:bg-coral-light"
                                @click="logout"
                            >
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <slot />

                <!-- Toasts -->
                <div role="status" aria-live="polite" class="pointer-events-none fixed right-4 top-20 z-[60] space-y-2">
                    <div
                        v-for="toast in toasts"
                        :key="toast.id"
                        :class="[
                            'pointer-events-auto flex min-w-[260px] max-w-md items-start gap-3 rounded-xl border px-4 py-3 shadow-lg',
                            toast.type === 'success' ? 'border-brand-primary/30 bg-white text-brand-dark' : '',
                            toast.type === 'error' ? 'border-coral/40 bg-coral-light text-coral-text' : '',
                            toast.type === 'info' ? 'border-brand-dark/20 bg-white text-brand-dark' : '',
                        ]"
                    >
                        <span class="flex-1 text-sm">{{ toast.message }}</span>
                        <button type="button" aria-label="Tutup notifikasi" class="opacity-70 hover:opacity-100" @click="toasts = toasts.filter((t) => t.id !== toast.id)">
                            <X class="h-4 w-4" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
