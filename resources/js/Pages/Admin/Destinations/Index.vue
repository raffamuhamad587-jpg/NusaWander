<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    Plus, Search, Edit2, Trash2, MapPin, Eye, EyeOff, X, AlertCircle,
    ChevronLeft, ChevronRight, ImageIcon, Clock
} from 'lucide-vue-next'

const props = defineProps({
    destinations: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
})

const search = ref(props.filters.search || '')
const categoryId = ref(props.filters.category_id || '')
const status = ref(props.filters.status || '')

let searchTimeout = null

function applyFilters() {
    router.get(route('admin.destinations.index'), {
        search: search.value || undefined,
        category_id: categoryId.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    })
}

function onSearchInput() {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => applyFilters(), 400)
}

watch([categoryId, status], () => applyFilters())

const isDeleteModalOpen = ref(false)
const destinationToDelete = ref(null)

function confirmDelete(dest) {
    destinationToDelete.value = dest
    isDeleteModalOpen.value = true
}

function deleteDestination() {
    if (destinationToDelete.value) {
        router.delete(route('admin.destinations.destroy', destinationToDelete.value.id), {
            onSuccess: () => {
                isDeleteModalOpen.value = false
                destinationToDelete.value = null
            },
        })
    }
}

function toggleStatus(dest) {
    router.patch(route('admin.destinations.toggle-status', dest.id), {}, {
        preserveScroll: true,
    })
}

function formatPrice(price) {
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}

function getCoverImage(dest) {
    if (dest.images && dest.images.length > 0) {
        return dest.images.reduce((lowest, img) =>
            img.urutan_tampil < lowest.urutan_tampil ? img : lowest, dest.images[0]).url_gambar
    }
    return null
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Katalog Destinasi</h2>
                    <p class="text-sm text-gray-500">Kelola data destinasi wisata, koordinat peta, dan estimasi harga</p>
                </div>
                <Link
                    :href="route('admin.destinations.create')"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl transition-all shadow-sm shadow-emerald-200 gap-2"
                >
                    <Plus class="w-4 h-4" />
                    Tambah Destinasi
                </Link>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Search -->
                <div class="relative md:col-span-1">
                    <input
                        v-model="search"
                        @input="onSearchInput"
                        type="text"
                        placeholder="Cari nama atau kota..."
                        class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                    />
                    <Search class="w-4 h-4 text-gray-400 absolute left-3 top-3" />
                </div>

                <!-- Category Filter -->
                <select
                    v-model="categoryId"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all cursor-pointer"
                >
                    <option value="">Semua Kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                        {{ cat.nama_kategori }}
                    </option>
                </select>

                <!-- Status Filter -->
                <select
                    v-model="status"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all cursor-pointer"
                >
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Destinasi</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estimasi Harga</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr
                                v-for="dest in destinations.data"
                                :key="dest.id"
                                class="hover:bg-gray-50/50 transition-colors"
                            >
                                <!-- Destination info w/ cover -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center shrink-0 overflow-hidden border border-gray-100">
                                            <img
                                                v-if="getCoverImage(dest)"
                                                :src="getCoverImage(dest)"
                                                :alt="dest.nama"
                                                class="w-full h-full object-cover"
                                            />
                                            <ImageIcon v-else class="w-6 h-6 text-gray-300" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate max-w-[220px]">{{ dest.nama }}</p>
                                            <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                                <MapPin class="w-3.5 h-3.5 shrink-0" />
                                                {{ dest.kota }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                        {{ dest.category?.nama_kategori }}
                                    </span>
                                </td>

                                <!-- Price -->
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-gray-900">{{ formatPrice(dest.estimasi_harga) }}</p>
                                    <p class="text-[11px] text-gray-400">
                                        {{ dest.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }}
                                    </p>
                                </td>

                                <!-- Status badge + toggle -->
                                <td class="px-5 py-4">
                                    <button
                                        @click="toggleStatus(dest)"
                                        :class="[
                                            'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition-colors',
                                            dest.status === 'aktif'
                                                ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                                        ]"
                                        :title="`Klik untuk mengubah status menjadi ${dest.status === 'aktif' ? 'nonaktif' : 'aktif'}`"
                                    >
                                        <component :is="dest.status === 'aktif' ? Eye : EyeOff" class="w-3.5 h-3.5" />
                                        {{ dest.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('admin.destinations.edit', dest.id)"
                                            class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                            title="Edit Destinasi"
                                        >
                                            <Edit2 class="w-4 h-4" />
                                        </Link>
                                        <button
                                            @click="confirmDelete(dest)"
                                            class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                            title="Hapus Destinasi"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="destinations.data.length === 0">
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <ImageIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                    <h4 class="text-base font-semibold text-gray-700">Belum Ada Destinasi</h4>
                                    <p class="text-sm text-gray-400 mt-1">Tambahkan destinasi wisata pertama atau ubah filter pencarian.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="destinations.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <strong>{{ destinations.from ?? 0 }}</strong> - <strong>{{ destinations.to ?? 0 }}</strong> dari <strong>{{ destinations.total }}</strong> data
                    </p>
                    <div class="flex items-center gap-1">
                        <component
                            :is="destinations.prev_page_url ? 'a' : 'span'"
                            :href="destinations.prev_page_url || undefined"
                            :class="[
                                'p-2 rounded-lg border transition-colors',
                                destinations.prev_page_url ? 'border-gray-200 text-gray-600 hover:bg-gray-50' : 'border-gray-100 text-gray-300 cursor-not-allowed',
                            ]"
                        >
                            <ChevronLeft class="w-4 h-4" />
                        </component>

                        <span class="px-3 text-sm text-gray-600 font-medium">
                            {{ destinations.current_page }} / {{ destinations.last_page }}
                        </span>

                        <component
                            :is="destinations.next_page_url ? 'a' : 'span'"
                            :href="destinations.next_page_url || undefined"
                            :class="[
                                'p-2 rounded-lg border transition-colors',
                                destinations.next_page_url ? 'border-gray-200 text-gray-600 hover:bg-gray-50' : 'border-gray-100 text-gray-300 cursor-not-allowed',
                            ]"
                        >
                            <ChevronRight class="w-4 h-4" />
                        </component>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div
                v-if="isDeleteModalOpen"
                class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            >
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center">
                    <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                        <AlertCircle class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Hapus Destinasi?</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Destinasi <strong>"{{ destinationToDelete?.nama }}"</strong> beserta seluruh fotonya akan dihapus secara permanen.
                    </p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button
                            @click="isDeleteModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            @click="deleteDestination"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium shadow-sm shadow-rose-200 cursor-pointer"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
