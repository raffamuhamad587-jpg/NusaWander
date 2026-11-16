<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Compass } from 'lucide-vue-next'

const page = usePage()
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

                <!-- Nav -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[#404945]">
                    <Link href="/" class="hover:text-[#174c3c] transition-colors">Beranda</Link>
                    <Link href="/" class="hover:text-[#174c3c] transition-colors">Destinasi</Link>
                    <Link href="/" class="hover:text-[#174c3c] transition-colors">Tentang</Link>
                </nav>

                <!-- Auth links -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="!$page.props.auth?.user"
                        :href="route('login')"
                        class="text-sm font-medium text-[#174c3c] hover:text-[#003527] transition-colors px-3 py-2"
                    >
                        Masuk
                    </Link>
                    <Link
                        v-if="!$page.props.auth?.user"
                        :href="route('register')"
                        class="text-sm font-semibold text-white bg-[#174c3c] hover:bg-[#003527] transition-colors px-5 py-2.5 rounded-full shadow-sm"
                    >
                        Daftar Akun
                    </Link>
                    <Link
                        v-else
                        :href="$page.props.auth.user.role === 'admin' ? '/admin/dashboard' : '/dashboard'"
                        class="text-sm font-semibold text-white bg-[#174c3c] hover:bg-[#003527] transition-colors px-5 py-2.5 rounded-full shadow-sm"
                    >
                        Dashboard
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 flex items-center justify-center pt-20 px-4 py-12">
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
                    © {{ new Date().getFullYear() }} Nusa Wander. Smart Travel &amp; Budget Planner.
                </p>
            </div>
        </footer>
    </div>
</template>
