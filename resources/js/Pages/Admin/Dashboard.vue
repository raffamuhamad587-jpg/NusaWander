<script setup>
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
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Ringkasan Dashboard</h2>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Destinations -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Destinasi</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.totalDestinations }}</p>
                            <p class="mt-1 text-xs text-green-600">
                                {{ stats.activeDestinations }} aktif
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <MapPin class="w-5 h-5 text-green-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Categories -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Kategori</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.totalCategories }}</p>
                            <p class="mt-1 text-xs text-blue-600">
                                Kategori destinasi
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <FileText class="w-5 h-5 text-blue-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Tourists -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Wisatawan Terdaftar</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.totalTourists }}</p>
                            <p class="mt-1 text-xs text-purple-600">
                                Akun terdaftar
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <Users class="w-5 h-5 text-purple-600" />
                        </div>
                    </div>
                </div>

                <!-- Status Distribution -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Status Destinasi</p>
                            <div class="mt-2 flex gap-4">
                                <div>
                                    <p class="text-3xl font-bold text-green-600">{{ stats.activeDestinations }}</p>
                                    <p class="text-xs text-gray-600">Aktif</p>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-400">{{ stats.inactiveDestinations }}</p>
                                    <p class="text-xs text-gray-600">Nonaktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <BarChart2 class="w-5 h-5 text-amber-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Destinations Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">5 Destinasi Terakhir Diperbarui</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Destinasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr 
                                v-for="dest in recentDestinations" 
                                :key="dest.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center shrink-0 overflow-hidden">
                                            <template v-if="dest.images && dest.images.length > 0">
                                                <img 
                                                    :src="dest.images[0].url_gambar" 
                                                    :alt="dest.nama"
                                                    class="w-full h-full object-cover"
                                                />
                                            </template>
                                            <template v-else>
                                                <MapPin class="w-5 h-5 text-green-600" />
                                            </template>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ dest.nama }}</p>
                                            <p class="text-xs text-gray-500">{{ dest.kota }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ dest.category?.nama_kategori }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    Rp {{ Number(dest.estimasi_harga).toLocaleString('id-ID') }}
                                    <span class="text-xs text-gray-500">({{ dest.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }})</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            dest.status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800',
                                        ]"
                                    >
                                        {{ dest.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <Clock class="w-4 h-4" />
                                        {{ $page.props.ziggy ? formatDate(dest.updated_at) : dest.updated_at }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="recentDestinations.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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