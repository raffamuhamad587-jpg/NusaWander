<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { Menu, X, ChevronDown, Compass, LogOut, User as UserIcon } from 'lucide-vue-next'

const mobileOpen = ref(false)
const userMenuOpen = ref(false)
const page = usePage()

const currentPath = computed(() => (page.url || '/').split('?')[0])

function isCurrent(path) {
    if (path === '/') return currentPath.value === '/'
    return currentPath.value === path || currentPath.value.startsWith(path + '/')
}

function logout() {
    router.post('/logout')
}

function onKeydown(event) {
    if (event.key === 'Escape') {
        mobileOpen.value = false
        userMenuOpen.value = false
    }
}

function onClickOutside(event) {
    if (!event.target.closest('.user-menu')) {
        userMenuOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('keydown', onKeydown)
    document.addEventListener('click', onClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown)
    document.removeEventListener('click', onClickOutside)
})

watch(() => page.url, () => {
    mobileOpen.value = false
    userMenuOpen.value = false
})
</script>

<template>
    <div class="min-h-screen flex flex-col bg-brand-bg font-body text-brand-text">
        <!-- R-32: Skip link for keyboard navigation -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[70] focus:px-4 focus:py-2.5 focus:rounded-lg focus:bg-brand-dark focus:text-white focus:text-sm focus:font-semibold"
        >
            Lewati ke konten utama
        </a>

        <!-- Header (R-10: single subtle translucent bar, solid border) -->
        <header class="sticky top-0 z-50 bg-brand-header backdrop-blur-sm border-b border-brand-dark/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 h-16 sm:h-[72px] flex items-center justify-between gap-4">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2.5 shrink-0 min-h-[44px]">
                    <span class="w-9 h-9 rounded-lg bg-brand-dark text-white flex items-center justify-center" aria-hidden="true">
                        <Compass class="w-5 h-5" />
                    </span>
                    <span class="font-heading font-bold text-base sm:text-lg tracking-tight text-brand-dark">
                        NUSA WANDER
                    </span>
                </Link>

                <!-- Desktop nav -->
                <nav aria-label="Navigasi utama" class="hidden md:flex items-center gap-1 text-sm font-medium">
                    <Link
                        href="/"
                        :aria-current="isCurrent('/') ? 'page' : undefined"
                        :class="[
                            'px-3.5 py-2 rounded-lg transition-colors min-h-[44px] inline-flex items-center',
                            isCurrent('/') ? 'text-brand-dark font-semibold bg-brand-input/80' : 'text-brand-muted hover:text-brand-dark hover:bg-brand-input/50',
                        ]"
                    >
                        Beranda
                    </Link>
                    <Link
                        href="/destinasi"
                        :aria-current="isCurrent('/destinasi') ? 'page' : undefined"
                        :class="[
                            'px-3.5 py-2 rounded-lg transition-colors min-h-[44px] inline-flex items-center',
                            isCurrent('/destinasi') ? 'text-brand-dark font-semibold bg-brand-input/80' : 'text-brand-muted hover:text-brand-dark hover:bg-brand-input/50',
                        ]"
                    >
                        Daftar Destinasi
                    </Link>
                    <Link
                        v-if="$page.props.auth?.user"
                        href="/trips/create"
                        :aria-current="currentPath === '/trips/create' ? 'page' : undefined"
                        :class="[
                            'px-3.5 py-2 rounded-lg transition-colors min-h-[44px] inline-flex items-center',
                            currentPath === '/trips/create' ? 'text-brand-dark font-semibold bg-brand-input/80' : 'text-brand-muted hover:text-brand-dark hover:bg-brand-input/50',
                        ]"
                    >
                        Rencanakan Perjalanan
                    </Link>
                    <Link
                        v-if="$page.props.auth?.user"
                        href="/trips"
                        :aria-current="currentPath === '/trips' ? 'page' : undefined"
                        :class="[
                            'px-3.5 py-2 rounded-lg transition-colors min-h-[44px] inline-flex items-center',
                            currentPath === '/trips' ? 'text-brand-dark font-semibold bg-brand-input/80' : 'text-brand-muted hover:text-brand-dark hover:bg-brand-input/50',
                        ]"
                    >
                        Itinerary Saya
                    </Link>
                </nav>

                <!-- Right side -->
                <div class="hidden md:flex items-center gap-2.5">
                    <template v-if="!$page.props.auth?.user">
                        <Link
                            href="/login"
                            class="text-sm font-medium text-brand-dark hover:bg-brand-input/70 transition-colors px-4 py-2.5 rounded-lg min-h-[44px] inline-flex items-center"
                        >
                            Masuk
                        </Link>
                        <Link
                            href="/register"
                            class="text-sm font-semibold text-white bg-brand-primary hover:bg-brand-primaryHover active:bg-brand-dark transition-colors px-5 py-2.5 rounded-lg min-h-[44px] inline-flex items-center"
                        >
                            Daftar Akun
                        </Link>
                    </template>

                    <!-- Logged-in user dropdown -->
                    <div v-else class="relative user-menu">
                        <button
                            type="button"
                            :aria-expanded="userMenuOpen"
                            aria-haspopup="menu"
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-brand-dark/10 bg-white hover:bg-brand-input/50 transition-colors min-h-[44px]"
                        >
                            <span class="w-7 h-7 rounded-md bg-brand-dark text-white flex items-center justify-center text-xs font-semibold" aria-hidden="true">
                                {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                            </span>
                            <span class="text-sm font-medium text-brand-dark max-w-[140px] truncate">{{ $page.props.auth.user.name }}</span>
                            <ChevronDown class="w-4 h-4 text-brand-muted" aria-hidden="true" />
                        </button>
                        <div
                            v-if="userMenuOpen"
                            role="menu"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-brand-dark/10 py-1.5 z-50"
                        >
                            <Link
                                role="menuitem"
                                :href="$page.props.auth.user.role === 'admin' ? '/admin/dashboard' : '/trips'"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-brand-text hover:bg-brand-bg"
                            >
                                <UserIcon class="w-4 h-4 text-brand-muted" aria-hidden="true" />
                                {{ $page.props.auth.user.role === 'admin' ? 'Dashboard Admin' : 'Itinerary Saya' }}
                            </Link>
                            <Link
                                role="menuitem"
                                href="/profile"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-brand-text hover:bg-brand-bg"
                            >
                                <UserIcon class="w-4 h-4 text-brand-muted" aria-hidden="true" />
                                Profil Akun
                            </Link>
                            <hr class="my-1.5 border-brand-dark/10" />
                            <button
                                type="button"
                                role="menuitem"
                                @click="logout"
                                class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm font-medium text-coral-text hover:bg-coral-light text-left"
                            >
                                <LogOut class="w-4 h-4" aria-hidden="true" />
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button with visible label (antislop-layoutmobile: no bare hamburger) -->
                <button
                    type="button"
                    :aria-expanded="mobileOpen"
                    aria-controls="mobile-nav-drawer"
                    @click="mobileOpen = !mobileOpen"
                    class="md:hidden inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-brand-dark/15 bg-white text-brand-dark text-sm font-medium min-h-[44px] min-w-[44px]"
                >
                    <Menu v-if="!mobileOpen" class="w-5 h-5" aria-hidden="true" />
                    <X v-else class="w-5 h-5" aria-hidden="true" />
                    <span>{{ mobileOpen ? 'Tutup' : 'Menu' }}</span>
                </button>
            </div>

            <!-- Mobile drawer -->
            <div
                v-if="mobileOpen"
                id="mobile-nav-drawer"
                class="md:hidden bg-white border-t border-brand-dark/10 px-4 py-4 space-y-1.5 shadow-lg"
            >
                <Link href="/" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-brand-dark hover:bg-brand-bg min-h-[44px]" @click="mobileOpen = false">
                    Beranda
                </Link>
                <Link href="/destinasi" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-brand-dark hover:bg-brand-bg min-h-[44px]" @click="mobileOpen = false">
                    Daftar Destinasi
                </Link>
                <Link v-if="$page.props.auth?.user" href="/trips/create" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-brand-dark hover:bg-brand-bg min-h-[44px]" @click="mobileOpen = false">
                    Rencanakan Perjalanan
                </Link>
                <Link v-if="$page.props.auth?.user" href="/trips" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-brand-dark hover:bg-brand-bg min-h-[44px]" @click="mobileOpen = false">
                    Itinerary Saya
                </Link>
                <Link v-if="$page.props.auth?.user?.role === 'admin'" href="/admin/dashboard" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-semibold text-brand-primary hover:bg-brand-bg min-h-[44px]" @click="mobileOpen = false">
                    Dashboard Admin
                </Link>

                <div class="pt-2 mt-2 border-t border-brand-dark/10 space-y-2">
                    <template v-if="!$page.props.auth?.user">
                        <Link href="/login" class="flex items-center justify-center w-full px-4 py-2.5 rounded-lg border border-brand-dark/20 text-sm font-semibold text-brand-dark min-h-[44px]" @click="mobileOpen = false">
                            Masuk
                        </Link>
                        <Link href="/register" class="flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-brand-primary text-white text-sm font-semibold min-h-[44px]" @click="mobileOpen = false">
                            Daftar Akun
                        </Link>
                    </template>
                    <button v-else type="button" @click="logout" class="flex items-center w-full px-3 py-2.5 rounded-lg text-sm font-semibold text-coral-text hover:bg-coral-light min-h-[44px]">
                        Keluar
                    </button>
                </div>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div
            v-if="$page.props.flash?.success || $page.props.flash?.error"
            role="status"
            aria-live="polite"
            class="fixed top-20 right-4 z-50 max-w-md space-y-2"
        >
            <div v-if="$page.props.flash?.success" class="rounded-xl border border-brand-primary/30 bg-white px-4 py-3 text-sm font-medium text-brand-dark shadow-lg">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="rounded-xl border border-coral-text/30 bg-coral-light px-4 py-3 text-sm font-medium text-coral-text shadow-lg">
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Main -->
        <main id="main-content" class="flex-1">
            <slot />
        </main>

        <!-- Footer (R-05: simple real links only, R-02: no em dash) -->
        <footer class="bg-brand-footer border-t border-brand-dark/10 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-sm">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-md bg-brand-dark text-white flex items-center justify-center" aria-hidden="true">
                        <Compass class="w-4 h-4" />
                    </span>
                    <span class="font-heading font-bold text-brand-dark">NUSA WANDER</span>
                </div>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-brand-muted">
                    <Link href="/destinasi" class="hover:text-brand-dark">Daftar Destinasi</Link>
                    <Link v-if="$page.props.auth?.user" href="/trips" class="hover:text-brand-dark">Itinerary Saya</Link>
                    <span>© {{ new Date().getFullYear() }} Nusa Wander. Explore Indonesia, Your Way.</span>
                </div>
            </div>
        </footer>
    </div>
</template>
