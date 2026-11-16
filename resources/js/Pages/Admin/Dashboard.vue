<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { MapPin, FileText, Users, BarChart2, Eye, Clock } from 'lucide-vue-next'

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentDestinations: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <AdminLayout>
        <template #default>
            <h2 class="text-xl font-heading font-semibold text-[#003527] mb-6 tracking-tight">Ringkasan Dashboard</h2>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total Destinations -->
                <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[#404945]/70 uppercase tracking-wider">Total Destinasi</p>
                            <p class="mt-2 text-3xl font-bold text-[#003527]">{{ stats.totalDestinations }}</p>
                            <p class="mt-1 text-xs text-emerald-600">
                                {{ stats.activeDestinations }} aktif
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <MapPin class="w-5 h-5 text-emerald-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Categories -->
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[#404945]/70 uppercase tracking-wider">Total Kategori</p>
                            <p class="mt-2 text-3xl font-bold text-[#003527]">{{ stats.totalCategories }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Kategori destinasi
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <FileText class="w-5 h-5 text-blue-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Tourists -->
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[#404945]/70 uppercase tracking-wider">Wisatawan Terdaftar</p>
                            <p class="mt-2 text-3xl font-bold text-[#003527]">{{ stats.totalTourists }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Akun terdaftar
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                            <Users class="w-5 h-5 text-purple-600" />
                        </div>
                    </div>
                </div>

                <!-- Status Distribution -->
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[#404945]/70 uppercase tracking-wider">Status Destinasi</p>
                            <div class="mt-2 flex gap-4">
                                <div>
                                    <p class="text-3xl font-bold text-emerald-600">{{ stats.activeDestinations }}</p>
                                    <p class="text-xs text-gray-600">Aktif</p>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-400">{{ stats.inactiveDestinations }}</p>
                                    <p class="text-xs text-gray-600">Nonaktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <BarChart2 class="w-5 h-5 text-amber-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Placeholder -->
            <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6 mb-8 h-64 flex items-center justify-center">
                <div class="text-center">
                    <BarChart2 class="w-12 h-12 text-emerald-200 mx-auto mb-4" />
                    <h3 class="text-base font-semibold text-[#003527]">Statistik Kunjungan Destinasi</h3>
                    <p class="text-sm text-[#404945]/60 mt-1">
                        Grafik kunjungan per bulan akan ditampilkan di sini.
                    </p>
                </div>
            </div>

            <!-- Recent Destinations Table -->
            <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-emerald-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-heading font-semibold text-[#003527]">Destinasi Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-1">Daftar destinasi wisata terbaru dalam sistem</p>
                    </div>
                    <Link
                        href="/admin/destinations"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#174c3c] hover:bg-[#003527] text-white text-xs font-semibold rounded-xl transition-colors"
                    >
                        Lihat Semua
                        <MapPin class="w-3 h-3" />
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-emerald-50 border-b border-emerald-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#404945]/60 uppercase tracking-wider">Nama Destinasi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#404945]/60 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#404945]/60 uppercase tracking-wider">Estimasi Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#404945]/60 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#404945]/60 uppercase tracking-wider">Terakhir Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-50">
                            <tr 
                                v-for="dest in recentDestinations" 
                                :key="dest.id"
                                class="hover:bg-emerald-25 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 overflow-hidden border border-emerald-100">
                                            <template v-if="dest.images && dest.images.length > 0">
                                                <img 
                                                    :src="dest.images[0].url_gambar" 
                                                    :alt="dest.nama"
                                                    class="w-full h-full object-cover"
                                                />
                                            </template>
                                            <template v-else>
                                                <MapPin class="w-5 h-5 text-emerald-600" />
                                            </template>
                                        </div>
                                        <div>
                                            <p class="font-medium text-[#003527]">{{ dest.nama }}</p>
                                            <p class="text-xs text-gray-500">{{ dest.kota }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ dest.category?.nama_kategori }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#003527]">
                                    Rp {{ Number(dest.estimasi_harga).toLocaleString('id-ID') }}
                                    <span class="text-xs text-gray-500 ml-1">({{ dest.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }})</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            dest.status === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600',
                                        ]"
                                    >
                                        {{ dest.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <Clock class="w-4 h-4" />
                                        {{ formatDate(dest.updated_at) }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="recentDestinations.length === 0">
                                <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                    Belum ada destinasi. Silakan tambahkan destinasi pertama.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>

<script>
function formatDate(date) {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>