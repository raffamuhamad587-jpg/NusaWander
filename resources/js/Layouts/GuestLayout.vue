<script setup>
import { Link } from '@inertiajs/vue3'
import { Compass } from 'lucide-vue-next'
</script>

<template>
    <div class="min-h-screen flex flex-col bg-brand-bg font-body text-brand-text">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-brand-header backdrop-blur-sm border-b border-brand-dark/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 h-16 sm:h-[72px] flex items-center justify-between gap-4">
                <Link href="/" class="flex items-center gap-2.5 min-h-[44px]">
                    <span class="w-9 h-9 rounded-lg bg-brand-dark text-white flex items-center justify-center" aria-hidden="true">
                        <Compass class="w-5 h-5" />
                    </span>
                    <span class="font-heading font-bold text-base sm:text-lg tracking-tight text-brand-dark">
                        NUSA WANDER
                    </span>
                </Link>

                <nav aria-label="Navigasi tamu" class="hidden md:flex items-center gap-6 text-sm font-medium text-brand-muted">
                    <Link href="/" class="hover:text-brand-dark transition-colors">Beranda</Link>
                    <Link href="/destinasi" class="hover:text-brand-dark transition-colors">Daftar Destinasi</Link>
                </nav>

                <div class="flex items-center gap-2">
                    <Link
                        v-if="!$page.props.auth?.user"
                        href="/login"
                        class="text-sm font-medium text-brand-dark hover:bg-brand-input/70 transition-colors px-3.5 py-2 rounded-lg min-h-[44px] inline-flex items-center"
                    >
                        Masuk
                    </Link>
                    <Link
                        v-if="!$page.props.auth?.user"
                        href="/register"
                        class="text-sm font-semibold text-white bg-brand-primary hover:bg-brand-primaryHover transition-colors px-4 py-2 rounded-lg min-h-[44px] inline-flex items-center"
                    >
                        Daftar Akun
                    </Link>
                    <Link
                        v-else
                        :href="$page.props.auth.user.role === 'admin' ? '/admin/dashboard' : '/trips'"
                        class="text-sm font-semibold text-white bg-brand-primary hover:bg-brand-primaryHover transition-colors px-4 py-2 rounded-lg min-h-[44px] inline-flex items-center"
                    >
                        {{ $page.props.auth.user.role === 'admin' ? 'Dashboard Admin' : 'Itinerary Saya' }}
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-brand-footer border-t border-brand-dark/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-brand-muted">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-brand-dark text-white flex items-center justify-center" aria-hidden="true">
                        <Compass class="w-3.5 h-3.5" />
                    </span>
                    <span class="font-heading font-bold text-brand-dark">NUSA WANDER</span>
                </div>
                <p>© {{ new Date().getFullYear() }} Nusa Wander. Smart Travel &amp; Budget Planner.</p>
            </div>
        </footer>
    </div>
</template>
