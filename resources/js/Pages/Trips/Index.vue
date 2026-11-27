<script setup>
import { Link, router, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Plus, Calendar, MapPin, Trash2, Users } from 'lucide-vue-next'

defineProps({
    trips: {
        type: Object,
        required: true,
    },
})

function formatPrice(val) {
    return 'Rp ' + Number(val ?? 0).toLocaleString('id-ID')
}

function formatDate(dateStr) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function deleteTrip(trip) {
    if (confirm(`Yakin ingin menghapus rencana perjalanan "${trip.nama_trip}"?`)) {
        router.delete(`/trips/${trip.id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Itinerary Saya | NUSA WANDER" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 sm:py-12">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <h1 class="font-heading font-bold text-2xl sm:text-3xl lg:text-4xl text-brand-dark tracking-tight">
                        Itinerary Saya
                    </h1>
                    <p class="text-sm text-brand-muted mt-1.5">
                        Daftar rencana perjalanan yang Anda simpan beserta estimasi biaya dan sisa anggaran.
                    </p>
                </div>
                <Link
                    href="/trips/create"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors shrink-0 min-h-[44px]"
                >
                    <Plus class="w-4 h-4" aria-hidden="true" />
                    Buat Rencana Baru
                </Link>
            </div>

            <!-- Trips Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <article
                    v-for="trip in trips.data"
                    :key="trip.id"
                    class="bg-white rounded-xl border border-brand-dark/10 p-5 hover:border-brand-primary/40 transition-colors flex flex-col justify-between"
                >
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-brand-input text-brand-dark tabular-nums">
                                    {{ trip.durasi_hari }} Hari
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium bg-brand-bg text-brand-dark border border-brand-dark/10 tabular-nums">
                                    <Users class="w-3 h-3" aria-hidden="true" /> {{ trip.jumlah_peserta }} Org
                                </span>
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-brand-bg text-brand-muted border border-brand-dark/10 tabular-nums">
                                    {{ trip.itinerary_items_count ?? 0 }} Destinasi
                                </span>
                            </div>
                            <button
                                type="button"
                                @click="deleteTrip(trip)"
                                :aria-label="`Hapus rencana ${trip.nama_trip}`"
                                class="p-2 text-brand-muted hover:text-coral-text rounded-lg hover:bg-coral-light transition-colors min-h-[40px] min-w-[40px] flex items-center justify-center"
                                title="Hapus itinerary"
                            >
                                <Trash2 class="w-4 h-4" aria-hidden="true" />
                            </button>
                        </div>

                        <div>
                            <h2 class="font-heading font-bold text-lg text-brand-dark line-clamp-1">
                                <Link :href="`/trips/${trip.id}`" class="hover:text-brand-primary">
                                    {{ trip.nama_trip }}
                                </Link>
                            </h2>
                            <p class="text-xs text-brand-muted flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5">
                                <span class="inline-flex items-center gap-1">
                                    <MapPin class="w-3.5 h-3.5 text-brand-primary shrink-0" aria-hidden="true" />
                                    {{ trip.wilayah_tujuan }}
                                </span>
                                <span aria-hidden="true">&bull;</span>
                                <span class="inline-flex items-center gap-1">
                                    <Calendar class="w-3.5 h-3.5 text-brand-primary shrink-0" aria-hidden="true" />
                                    {{ formatDate(trip.tanggal_mulai) }}
                                </span>
                            </p>
                        </div>

                        <dl class="rounded-lg bg-brand-bg border border-brand-dark/10 p-3.5 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-brand-muted">Anggaran</dt>
                                <dd class="font-semibold text-brand-dark tabular-nums">{{ formatPrice(trip.anggaran) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-brand-muted">Total estimasi</dt>
                                <dd class="font-semibold text-brand-dark tabular-nums">{{ formatPrice(trip.total_estimasi) }}</dd>
                            </div>
                            <div class="flex justify-between pt-1.5 border-t border-brand-dark/10">
                                <dt class="font-semibold text-brand-dark">Sisa anggaran</dt>
                                <dd :class="['font-bold tabular-nums', Number(trip.sisa_anggaran) < 0 ? 'text-coral-text' : 'text-brand-primary']">
                                    {{ formatPrice(trip.sisa_anggaran) }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="pt-4 mt-4 border-t border-brand-dark/10 flex justify-end">
                        <Link
                            :href="`/trips/${trip.id}`"
                            class="inline-flex items-center px-4 py-2 bg-brand-input hover:bg-brand-dark hover:text-white text-brand-dark text-xs font-semibold rounded-lg transition-colors min-h-[40px]"
                        >
                            Buka Itinerary
                        </Link>
                    </div>
                </article>

                <div v-if="trips.data.length === 0" class="col-span-full bg-white rounded-xl border border-brand-dark/10 p-12 text-center">
                    <Calendar class="w-10 h-10 text-brand-muted mx-auto mb-3" aria-hidden="true" />
                    <h2 class="font-heading font-bold text-xl text-brand-dark mb-2">Belum ada itinerary tersimpan</h2>
                    <p class="text-sm text-brand-muted max-w-md mx-auto mb-6">
                        Buat rencana perjalanan pertama Anda untuk mendapatkan susunan kunjungan harian dan perhitungan sisa anggaran.
                    </p>
                    <Link
                        href="/trips/create"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors min-h-[44px]"
                    >
                        <Plus class="w-4 h-4" aria-hidden="true" />
                        Rencanakan Perjalanan Pertama
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
