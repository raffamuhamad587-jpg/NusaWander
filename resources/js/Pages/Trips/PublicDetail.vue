<script setup>
import { Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ArrowLeft, Download, MapPin, Wallet, Calendar, User } from 'lucide-vue-next'

const props = defineProps({
    trip: {
        type: Object,
        required: true,
    },
})

function formatPrice(val) {
    return 'Rp ' + Number(val).toLocaleString('id-ID')
}
</script>

<template>
    <Head :title="`${trip.judul} - Inspirasi NusaWander`" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
            <!-- Top bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <Link href="/inspirasi" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#404945]/60 hover:text-[#003527] transition-colors mb-2">
                        <ArrowLeft class="w-3.5 h-3.5" /> Kembali ke Galeri Inspirasi
                    </Link>
                    <h1 class="font-heading font-bold text-2xl md:text-3xl text-[#003527]">
                        {{ trip.judul }}
                    </h1>
                    <p class="text-sm text-[#404945]/70 flex items-center gap-2 mt-1">
                        <User class="w-4 h-4 text-emerald-600 shrink-0" />
                        Oleh: <strong>{{ trip.user?.name }}</strong> &bull; {{ trip.destination?.nama }} ({{ trip.destination?.kota }})
                    </p>
                </div>

                <a
                    :href="route('trips.export-pdf', trip.id)"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-[#174c3c] hover:bg-[#003527] text-white text-sm font-semibold rounded-2xl shadow-lg shadow-emerald-200 transition-all shrink-0"
                >
                    <Download class="w-4 h-4" /> Download PDF Itinerary
                </a>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Itinerary Items -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 md:p-8 space-y-4">
                        <h2 class="font-heading font-bold text-xl text-[#003527]">Rangkaian Jadwal</h2>

                        <div class="space-y-3">
                            <div
                                v-for="(item, idx) in trip.itinerary_items"
                                :key="item.id"
                                class="flex items-start gap-4 p-4 bg-emerald-50/40 border border-emerald-100 rounded-2xl"
                            >
                                <div class="w-8 h-8 rounded-xl bg-[#003527] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ idx + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <Link :href="`/destinasi/${item.destination_id}`" class="font-semibold text-sm text-[#003527] hover:text-[#174c3c] transition-colors">
                                        {{ item.judul }}
                                    </Link>
                                    <p class="text-xs text-[#404945]/60 flex items-center gap-1 mt-0.5">
                                        <MapPin class="w-3 h-3 shrink-0" /> {{ item.destination?.kota }}
                                    </p>
                                    <p v-if="item.catatan" class="text-[11px] text-emerald-800 bg-emerald-100/60 inline-block px-2 py-0.5 rounded-md mt-1.5">
                                        {{ item.catatan }}
                                    </p>
                                </div>
                                <p class="font-heading font-bold text-sm text-[#003527] shrink-0">
                                    {{ formatPrice(item.estimasi_harga) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 md:p-8 sticky top-28 space-y-6">
                        <h2 class="font-heading font-bold text-xl text-[#003527] flex items-center gap-2">
                            <Wallet class="w-5 h-5 text-emerald-600" /> Total Anggaran
                        </h2>

                        <div class="bg-emerald-50 rounded-2xl p-5 text-center space-y-1">
                            <p class="text-xs uppercase font-bold text-[#404945]/60 tracking-wider">Estimasi Perjalanan</p>
                            <p class="font-heading font-bold text-3xl text-[#003527]">
                                {{ formatPrice(trip.total_estimasi_harga) }}
                            </p>
                        </div>

                        <Link
                            v-if="$page.props.auth?.user"
                            :href="`/trips/create?destination_id=${trip.destination_id}`"
                            class="block w-full py-4 text-center font-semibold text-white bg-[#174c3c] hover:bg-[#003527] rounded-2xl shadow-lg transition-all"
                        >
                            Buat Perjalanan Serupa
                        </Link>
                        <Link
                            v-else
                            :href="route('register')"
                            class="block w-full py-4 text-center font-semibold text-white bg-[#174c3c] hover:bg-[#003527] rounded-2xl shadow-lg transition-all"
                        >
                            Daftar untuk Menyusun Rencana
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>