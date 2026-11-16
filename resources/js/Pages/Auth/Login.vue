<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import {
    Compass, Mail, Lock, Eye, EyeOff, ShieldCheck, ArrowRight,
    ArrowLeft, HelpCircle, User
} from 'lucide-vue-next'

defineProps({
    canResetPassword: { type: Boolean },
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Masuk Akun" />

    <div class="min-h-screen flex flex-col bg-[#eefdf5] font-body text-[#404945]">
        <!-- Header -->
        <header class="border-b border-[#003527]/5">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 h-20 flex items-center justify-between gap-4">
                <!-- Left -->
                <div class="flex items-center gap-5">
                    <Link href="/" class="hidden sm:flex items-center gap-2 text-sm text-[#404945]/70 hover:text-[#003527] transition-colors">
                        <ArrowLeft class="w-4 h-4" />
                        <span>Kembali ke Beranda</span>
                    </Link>
                    <span class="hidden md:block w-px h-6 bg-[#003527]/10"></span>
                    <Link href="/" class="flex items-center gap-2.5 group">
                        <span class="w-9 h-9 rounded-full bg-[#174c3c] text-white flex items-center justify-center shadow-sm shadow-[#174c3c]/30 group-hover:scale-105 transition-transform">
                            <Compass class="w-5 h-5" />
                        </span>
                        <span class="font-heading font-bold text-lg tracking-tight text-[#003527]">NUSA WANDER</span>
                    </Link>
                </div>

                <!-- Right -->
                <div class="flex items-center gap-3 sm:gap-5">
                    <Link :href="route('login')" class="px-5 py-2.5 rounded-xl bg-[#174c3c] hover:bg-[#003527] text-white text-sm font-semibold transition-colors shadow-sm">
                        Masuk
                    </Link>
                    <Link :href="route('register')" class="text-sm font-medium text-[#003527]/80 hover:text-[#003527] transition-colors">
                        Daftar
                    </Link>
                    <div class="hidden sm:flex items-center rounded-lg bg-emerald-100/60 p-0.5 text-xs font-semibold">
                        <span class="px-2.5 py-1 rounded-md bg-white text-[#003527] shadow-sm">ID</span>
                        <span class="px-2.5 py-1 text-[#404945]/50">EN</span>
                    </div>
                    <button type="button" class="hidden lg:flex items-center gap-1.5 text-sm text-[#003527]/80 hover:text-[#003527] transition-colors">
                        <HelpCircle class="w-4 h-4" />
                        <span>Bantuan</span>
                    </button>
                    <button type="button" class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-[#174c3c] hover:bg-emerald-200 transition-colors">
                        <User class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 flex items-center justify-center px-4 py-10 sm:py-14">
            <div class="w-full max-w-[460px] bg-white rounded-[26px] shadow-[0_24px_70px_-24px_rgba(0,53,39,0.22)] p-7 sm:p-10">
                <!-- Brand -->
                <div class="text-center mb-7">
                    <div class="flex items-center justify-center gap-2 mb-1.5">
                        <span class="w-7 h-7 rounded-full bg-[#174c3c] text-white flex items-center justify-center">
                            <Compass class="w-4 h-4" />
                        </span>
                        <span class="font-heading font-bold text-[#003527] tracking-wide">NUSA WANDER</span>
                    </div>
                    <p class="text-[10px] tracking-[0.34em] font-semibold text-[#404945]/45">SMART TRAVEL PLANNER</p>
                </div>

                <h1 class="text-center font-heading font-bold text-[26px] leading-tight text-[#003527]">
                    Masuk ke Akun Anda
                </h1>
                <p class="text-center text-sm text-[#404945]/65 mt-2 mb-7 leading-relaxed">
                    Lanjutkan rencana petualangan Anda menjelajah destinasi impian di Nusantara.
                </p>

                <!-- OAuth -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button type="button" class="flex items-center justify-center gap-2 h-[48px] rounded-2xl bg-[#eaf4ee] hover:bg-[#e0efe7] text-sm font-semibold text-[#003527] transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>
                    <button type="button" class="flex items-center justify-center gap-2 h-[48px] rounded-2xl bg-[#eaf4ee] hover:bg-[#e0efe7] text-sm font-semibold text-[#003527] transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M16.365 1.43c0 1.14-.493 2.27-1.177 3.08-.744.9-1.99 1.57-2.987 1.57-.12 0-.23-.02-.3-.03-.01-.06-.04-.22-.04-.39 0-1.15.572-2.27 1.206-2.98.804-.94 2.142-1.64 3.248-1.68.03.13.05.28.05.43zm4.565 15.71c-.03.07-.463 1.58-1.518 3.12-.945 1.34-1.94 2.71-3.43 2.71-1.517 0-1.9-.88-3.63-.88-1.698 0-2.302.91-3.67.91-1.377 0-2.332-1.26-3.428-2.8-1.287-1.82-2.323-4.63-2.323-7.28 0-4.28 2.797-6.55 5.552-6.55 1.448 0 2.675.95 3.6.95.865 0 2.222-1.01 3.902-1.01.613 0 2.886.06 4.374 2.19-.13.09-2.383 1.37-2.383 4.19 0 3.26 2.854 4.42 2.955 4.45z"/>
                        </svg>
                        Apple
                    </button>
                </div>

                <!-- Divider -->
                <div class="flex items-center gap-3 mb-6">
                    <span class="h-px flex-1 bg-[#003527]/10"></span>
                    <span class="text-xs text-[#404945]/45 font-medium">atau masuk dengan email</span>
                    <span class="h-px flex-1 bg-[#003527]/10"></span>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-[#003527] mb-2">Email atau Nama Pengguna</label>
                        <div class="relative">
                            <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#404945]/35" />
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="contoh: traveler@nusawander.id"
                                class="w-full h-[52px] pl-12 pr-4 bg-[#e8f7ef] border border-transparent rounded-2xl text-sm text-[#003527] placeholder-[#404945]/40 focus:bg-white focus:border-[#174c3c]/40 focus:ring-2 focus:ring-[#174c3c]/15 focus:outline-none transition-all"
                            />
                        </div>
                        <InputError class="mt-1.5" :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-[#003527]">Kata Sandi</label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-semibold text-[#174c3c] hover:underline"
                            >
                                Lupa kata sandi?
                            </Link>
                        </div>
                        <div class="relative">
                            <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#404945]/35" />
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••••"
                                class="w-full h-[52px] pl-12 pr-12 bg-[#e8f7ef] border border-transparent rounded-2xl text-sm text-[#003527] placeholder-[#404945]/40 focus:bg-white focus:border-[#174c3c]/40 focus:ring-2 focus:ring-[#174c3c]/15 focus:outline-none transition-all"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#404945]/45 hover:text-[#174c3c] transition-colors"
                                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                            >
                                <component :is="showPassword ? EyeOff : Eye" class="w-5 h-5" />
                            </button>
                        </div>
                        <InputError class="mt-1.5" :message="form.errors.password" />
                    </div>

                    <!-- Remember -->
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="w-[18px] h-[18px] rounded-md border-[#003527]/25 text-[#174c3c] focus:ring-[#174c3c]/25"
                        />
                        <span class="text-sm text-[#404945]/80">Ingat saya di perangkat ini</span>
                    </label>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full h-[52px] rounded-2xl bg-[#174c3c] hover:bg-[#003527] text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-[#174c3c]/20 transition-all disabled:opacity-60 cursor-pointer"
                    >
                        {{ form.processing ? 'Memproses...' : 'Masuk ke Akun' }}
                        <ArrowRight v-if="!form.processing" class="w-5 h-5" />
                    </button>
                </form>

                <!-- Security badge -->
                <div class="mt-5 flex items-center gap-2 rounded-xl bg-[#e8f7ef] px-4 py-3">
                    <ShieldCheck class="w-4 h-4 text-[#174c3c] shrink-0" />
                    <p class="text-xs text-[#404945]/70">Data Anda terenkripsi aman standar TLS 256-bit.</p>
                </div>

                <p class="text-center text-sm text-[#404945]/70 mt-6">
                    Belum memiliki akun NUSA WANDER?
                    <Link :href="route('register')" class="font-bold text-[#003527] hover:underline">Daftar Sekarang (Gratis)</Link>
                </p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-[#003527]/5">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#404945]/55">
                <p>© 2024 NUSA WANDER. Hak cipta dilindungi undang-undang.</p>
                <div class="flex items-center gap-6">
                    <Link href="#" class="hover:text-[#003527] transition-colors">Syarat &amp; Ketentuan</Link>
                    <Link href="#" class="hover:text-[#003527] transition-colors">Kebijakan Privasi</Link>
                    <Link href="#" class="hover:text-[#003527] transition-colors">Pusat Bantuan</Link>
                </div>
            </div>
        </footer>
    </div>
</template>