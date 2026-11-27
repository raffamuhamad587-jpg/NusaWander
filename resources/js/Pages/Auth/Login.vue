<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import { Mail, Lock, Eye, EyeOff } from 'lucide-vue-next'

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
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Masuk Akun | NUSA WANDER" />

    <GuestLayout>
        <div class="w-full max-w-md bg-white rounded-xl border border-brand-dark/10 shadow-sm p-6 sm:p-8">
            <div class="mb-6">
                <h1 class="font-heading font-bold text-2xl text-brand-dark">
                    Masuk ke akun Anda
                </h1>
                <p class="text-sm text-brand-muted mt-1.5">
                    Lanjutkan menyusun itinerary dan pantau anggaran perjalanan yang sudah Anda simpan.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-brand-dark mb-1.5">
                        Email
                    </label>
                    <div class="relative">
                        <Mail class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-brand-muted" aria-hidden="true" />
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nama@email.com"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary min-h-[44px]"
                        />
                    </div>
                    <InputError class="mt-1.5" :message="form.errors.email" />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-brand-dark">
                            Kata sandi
                        </label>
                        <Link
                            v-if="canResetPassword"
                            href="/forgot-password"
                            class="text-xs font-semibold text-brand-primary hover:underline"
                        >
                            Lupa kata sandi?
                        </Link>
                    </div>
                    <div class="relative">
                        <Lock class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-brand-muted" aria-hidden="true" />
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi"
                            class="w-full pl-10 pr-11 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary min-h-[44px]"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-brand-muted hover:text-brand-dark rounded-md"
                            :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        >
                            <component :is="showPassword ? EyeOff : Eye" class="w-4 h-4" aria-hidden="true" />
                        </button>
                    </div>
                    <InputError class="mt-1.5" :message="form.errors.password" />
                </div>

                <label for="remember" class="flex items-center gap-2.5 select-none pt-1">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        class="w-4 h-4 rounded border-brand-dark/30 text-brand-primary focus:ring-brand-primary"
                    />
                    <span class="text-sm text-brand-muted">Ingat saya di perangkat ini</span>
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 px-4 rounded-lg bg-brand-primary hover:bg-brand-primaryHover active:bg-brand-dark text-white font-semibold text-sm transition-colors disabled:opacity-60 min-h-[44px]"
                >
                    {{ form.processing ? 'Memproses...' : 'Masuk' }}
                </button>
            </form>

            <p class="text-center text-sm text-brand-muted mt-6 pt-5 border-t border-brand-dark/10">
                Belum punya akun?
                <Link href="/register" class="font-semibold text-brand-primary hover:underline ml-1">
                    Daftar akun wisatawan
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>
