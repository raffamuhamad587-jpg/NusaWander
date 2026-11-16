<script setup>
import { ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { Menu, X, ChevronDown, Compass, LogOut, User as UserIcon } from 'lucide-vue-next'

const mobileOpen = ref(false)
const userMenuOpen = ref(false)
const page = usePage()

function logout() {
    router.post(route('logout'))
}
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#eefdf5] font-body text-[#404945]">
        <!-- Header -->
        <header class="fixed top-0 inset-x-0 z-50 backdrop-blur-md bg-[rgba(238,253,245,0.85)] border-b border-[#003527]/5">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 h-20 flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 group">
                    <span class="w-10 h-10 rounded-xl bg-[#003527] text-white flex items-center justify-center shadow-md shadow-[#003527]/20 group-hover:scale-105 transition-transform">
                        <Compass class="w-5 h-5" />
                    </span>
                    <span class="font-heading font-semibold text-lg tracking-tight text-[#003527]">
                        NUSA WANDER
                    </span>
                </Link>

                <!-- Desktop nav -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[#404945]">
                    <Link href="/" class="hover:text-[#174c3c] transition-colors">Beranda</Link>
                    <Link href="/destinasi" class="hover:text-[#174c3c] transition-colors">Katalog Destinasi</Link>
                    <Link href="/trips" v-if="$page.props.auth?.user" class="hover:text-[#174c3c] transition-colors">Trip Saya</Link>
                </nav>

                <!-- Right side -->
                <div class="hidden md:flex items-center gap-3">
                    <template v-if="!$page.props.auth?.user">
                        <Link :href="route('login')" class="text-sm font-medium text-[#174c3c] hover:text-[#003527] transition-colors px-3 py-2">
                            Masuk
                        </Link>
                        <Link :href="route('register')" class="text-sm font-semibold text-white bg-[#174c3c] hover:bg-[#003527] transition-colors px-5 py-2.5 rounded-full shadow-sm">
                            Daftar Akun
                        </Link>
                    </template>

                    <!-- Logged-in user dropdown -->
                    <div v-else class="relative user-menu">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-white/60 transition-colors">
                            <span class="w-8 h-8 rounded-full bg-[#003527] text-white flex items-center justify-center text-xs font-bold">
                                {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                            </span>
                            <span class="text-sm font-medium text-[#003527]">{{ $page.props.auth.user.name }}</span>
                            <ChevronDown class="w-4 h-4 text-[#404945]/60" />
                        </button>
                        <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-emerald-100 py-2 z-50">
                            <Link :href="$page.props.auth.user.role === 'admin' ? '/admin/dashboard' : '/trips'" class="flex items-center px-4 py-2 text-sm text-[#003527]/80 hover:bg-emerald-50">
                                <UserIcon class="w-4 h-4 mr-3" /> Dashboard
                            </Link>
                            <Link href="/profile" class="flex items-center px-4 py-2 text-sm text-[#003527]/80 hover:bg-emerald-50">
                                <UserIcon class="w-4 h-4 mr-3" /> Profil
                            </Link>
                            <hr class="my-2 border-emerald-100" />
                            <button @click="logout" class="flex items-center w-full px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                <LogOut class="w-4 h-4 mr-3" /> Keluar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-xl text-[#003527] hover:bg-white/60">
                    <Menu v-if="!mobileOpen" class="w-6 h-6" />
                    <X v-else class="w-6 h-6" />
                </button>
            </div>

            <!-- Mobile drawer -->
            <div v-if="mobileOpen" class="md:hidden bg-white border-t border-emerald-100 px-6 py-4 space-y-3 shadow-lg">
                <Link href="/" class="block text-sm font-medium text-[#003527] py-2" @click="mobileOpen = false">Beranda</Link>
                <Link href="/destinasi" class="block text-sm font-medium text-[#003527] py-2" @click="mobileOpen = false">Katalog Destinasi</Link>
                <Link v-if="$page.props.auth?.user" href="/trips" class="block text-sm font-medium text-[#003527] py-2" @click="mobileOpen = false">Trip Saya</Link>
                <template v-if="!$page.props.auth?.user">
                    <Link :href="route('login')" class="block text-sm font-medium text-[#174c3c] py-2" @click="mobileOpen = false">Masuk</Link>
                    <Link :href="route('register')" class="block text-sm font-semibold text-white bg-[#174c3c] rounded-full px-5 py-2.5 text-center" @click="mobileOpen = false">Daftar Akun</Link>
                </template>
                <button v-else @click="logout" class="block w-full text-left text-sm font-medium text-rose-600 py-2">Keluar</button>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 pt-20">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-[rgba(232,247,239,0.7)] border-t border-[#003527]/5">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-[#003527] text-white flex items-center justify-center">
                        <Compass class="w-3.5 h-3.5" />
                    </span>
                    <span class="font-heading font-semibold text-[#003527]">NUSA WANDER</span>
                </div>
                <p class="text-[#404945]/80">
                    © {{ new Date().getFullYear() }} Nusa Wander. Explore Indonesia, Your Way.
                </p>
            </div>
        </footer>
    </div>
</template>