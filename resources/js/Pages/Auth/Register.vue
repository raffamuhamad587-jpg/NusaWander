<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Daftar Akun" />

    <GuestLayout>
        <div class="relative w-full max-w-[448px] px-6 py-12">
            <!-- Overlay -->
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-[#eefdf5] to-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-100 rounded-full blur-[120px] opacity-50 -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" />
            </div>

            <!-- Card content -->
            <div class="relative z-10">
                <div class="text-center mb-10">
                    <h1 class="font-heading font-semibold text-3xl md:text-4xl text-[#003527] tracking-tight mb-2">
                        Buat Akun Baru
                    </h1>
                    <p class="text-lg text-[#404945] leading-relaxed">
                        Nikmati kemudahan merencanakan liburan Anda
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Nama Lengkap" />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            class="w-full px-4 py-3 bg-[#e8f7ef] border border-transparent rounded-3xl text-base placeholder-gray-400 focus:bg-white focus:border-emerald-600/50 focus:outline-none transition-all"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            class="w-full px-4 py-3 bg-[#e8f7ef] border border-transparent rounded-3xl text-base placeholder-gray-400 focus:bg-white focus:border-emerald-600/50 focus:outline-none transition-all"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 bg-[#e8f7ef] border border-transparent rounded-3xl text-base placeholder-gray-400 focus:bg-white focus:border-emerald-600/50 focus:outline-none transition-all"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 bg-[#e8f7ef] border border-transparent rounded-3xl text-base placeholder-gray-400 focus:bg-white focus:border-emerald-600/50 focus:outline-none transition-all"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <PrimaryButton
                        class="w-full px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white font-medium text-sm rounded-full shadow-sm shadow-emerald-200 transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Mendaftarkan...' : 'Daftar Sekarang' }}
                    </PrimaryButton>

                    <p class="text-center text-sm text-gray-500">
                        Sudah punya akun?
                        <Link :href="route('login')" class="text-[#174c3c] font-semibold hover:underline">
                            Masuk
                        </Link>
                    </p>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>