<script setup>
import { Link, router, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Plus, Calendar, MapPin, Trash2, ArrowRight, FileText } from 'lucide-vue-next'

const props = defineProps({
    trips: {
        type: Object,
        required: true,
    },
})

function formatPrice(val) {
    return 'Rp ' + Number(val).toLocaleString('id-ID')
}

function deleteTrip(trip) {
    if (confirm(`Yakin ingin menghapus rencana perjalanan "${trip.judul}"?`)) {
        router.delete(route('trips.destroy', trip.id), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Trip Saya - NUSA WANDER" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
                <div>
                    <h1 class="font-heading font-bold text-3xl md:text-4xl text-[#003527] tracking-tight">
                        Rencana Perjalanan Saya
                    </h1>
                    <p class="text-base text-[#404945]/70 mt-1">
                        Kelola itinerary, atur jadwal kunjungan, dan pantau estimasi biaya liburanmu.
                    </p>
                </div>
                <Link
                    :href="route('trips.create')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl shadow-lg shadow-emerald-200 transition-all shrink-0"
                >
                    <Plus class="w-4 h-4" />
                    Buat Trip Baru
                </Link>
            </div>

            <!-- Trips Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="trip in trips.data"
                    :key="trip.id"
                    class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 hover:shadow-lg transition-all flex flex-col justify-between"
                >
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800">
                                {{ trip.itinerary_items?.length || 0 }} Destinasi
                            </span>
                            <button
                                @click="deleteTrip(trip)"
                                class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors"
                                title="Hapus Trip"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>

                        <div>
                            <Link :href="route('trips.show', trip.id)">
                                <h3 class="font-heading font-bold text-xl text-[#003527] hover:text-[#174c3c] transition-colors line-clamp-1">
                                    {{ trip.judul }}
                                </h3>
                            </Link>
                            <p class="text-xs text-[#404945]/60 flex items-center gap-1 mt-1">
                                <MapPin class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                {{ trip.destination?.nama }} ({{ trip.destination?.kota }})
                            </p>
                        </div>

                        <p class="text-xs text-[#404945]/80 line-clamp-2 leading-relaxed">
                            {{ trip.deskripsi || 'Tidak ada catatan tambahan untuk trip ini.' }}
                        </p>
                    </div>

                    <div class="pt-6 mt-6 border-t border-emerald-100 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] uppercase tracking-wider text-[#404945]/60 font-semibold">Total Estimasi</span>
                            <p class="font-heading font-bold text-lg text-[#003527]">
                                {{ formatPrice(trip.total_estimasi_harga) }}
                            </p>
                        </div>
                        <Link
                            :href="route('trips.show', trip.id)"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl transition-colors"
                        >
                            Buka Planner
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <div v-if="trips.data.length === 0" class="col-span-full bg-white rounded-3xl border border-emerald-100 p-16 text-center">
                    <Calendar class="w-16 h-16 text-emerald-200 mx-auto mb-4" />
                    <h3 class="font-heading font-bold text-xl text-[#003527] mb-2">Belum Ada Rencana Perjalanan</h3>
                    <p class="text-sm text-[#404945]/70 max-w-md mx-auto mb-6">
                        Mulai rancang liburan impianmu! Tambahkan destinasi favorit dan hitung estimasi biaya sekarang.
                    </p>
                    <Link
                        :href="route('trips.create')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl shadow-sm transition-all"
                    >
                        <Plus class="w-4 h-4" />
                        Rencanakan Trip Pertama
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>