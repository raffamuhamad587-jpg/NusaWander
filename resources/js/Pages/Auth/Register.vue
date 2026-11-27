<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Daftar Akun | NUSA WANDER" />

    <GuestLayout>
        <div class="w-full max-w-4xl bg-white rounded-xl border border-brand-dark/10 shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12">
            <!-- Left brand info panel (R-17 & R-36: real features only, no fake counts) -->
            <div class="lg:col-span-5 bg-brand-dark text-white p-7 sm:p-9 flex flex-col justify-between gap-8">
                <div class="space-y-4">
                    <h1 class="font-heading font-bold text-2xl sm:text-3xl leading-snug">
                        Mulai rencanakan perjalanan Anda di Indonesia.
                    </h1>
                    <p class="text-sm text-white/80 leading-relaxed">
                        Dengan akun wisatawan, Anda dapat menyusun itinerary per hari, menghitung estimasi biaya serta sisa anggaran, dan menyimpan rencana perjalanan agar mudah dibuka kembali.
                    </p>
                </div>

                <ul class="space-y-2.5 text-xs text-white/85 border-t border-white/15 pt-5">
                    <li>Susunan kunjungan per hari otomatis dari katalog</li>
                    <li>Rincian biaya tiket, transport, penginapan, dan kuliner</li>
                    <li>Simpan banyak rencana perjalanan dan ekspor ke PDF</li>
                </ul>
            </div>

            <!-- Right Form -->
            <div class="lg:col-span-7 p-6 sm:p-9">
                <div class="mb-6">
                    <h2 class="font-heading font-bold text-2xl text-brand-dark">Daftar akun baru</h2>
                    <p class="text-sm text-brand-muted mt-1">Isi data diri Anda untuk membuat akun wisatawan.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Nama lengkap" class="text-xs font-semibold text-brand-dark mb-1.5" />
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Nama lengkap Anda"
                            class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary min-h-[44px]"
                        />
                        <InputError class="mt-1.5 text-xs" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" class="text-xs font-semibold text-brand-dark mb-1.5" />
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="nama@email.com"
                            class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary min-h-[44px]"
                        />
                        <InputError class="mt-1.5 text-xs" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="phone" value="Nomor telepon (opsional)" class="text-xs font-semibold text-brand-dark mb-1.5" />
                        <input
                            id="phone"
                            type="tel"
                            v-model="form.phone"
                            placeholder="08xxxxxxxxxx"
                            class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary min-h-[44px]"
                        />
                        <InputError class="mt-1.5 text-xs" :message="form.errors.phone" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="password" value="Kata sandi" class="text-xs font-semibold text-brand-dark mb-1.5" />
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary min-h-[44px]"
                            />
                            <InputError class="mt-1.5 text-xs" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Konfirmasi kata sandi" class="text-xs font-semibold text-brand-dark mb-1.5" />
                            <input
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi kata sandi"
                                class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary min-h-[44px]"
                            />
                            <InputError class="mt-1.5 text-xs" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full mt-2 px-5 py-3 bg-brand-primary hover:bg-brand-primaryHover active:bg-brand-dark text-white font-semibold text-sm rounded-lg transition-colors disabled:opacity-50 min-h-[44px]"
                    >
                        {{ form.processing ? 'Membuat akun...' : 'Buat Akun Wisatawan' }}
                    </button>

                    <p class="text-center text-sm text-brand-muted pt-3 border-t border-brand-dark/10">
                        Sudah punya akun?
                        <Link href="/login" class="text-brand-primary font-semibold hover:underline ml-1">
                            Masuk di sini
                        </Link>
                    </p>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
