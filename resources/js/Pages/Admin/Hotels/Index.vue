<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    Plus, Search, Edit2, Trash2, MapPin, Eye, EyeOff, X, AlertCircle,
    ChevronLeft, ChevronRight, ImageIcon, Building
} from 'lucide-vue-next'

const props = defineProps({
    hotels: { type: Object, required: true },
    tipes: { type: Array, required: true },
    kotas: { type: Array, required: true },
    filters: { type: Object, required: true },
})

const search = ref(props.filters.search || '')
const tipe = ref(props.filters.tipe || '')
const kota = ref(props.filters.kota || '')
const status = ref(props.filters.status || '')

let searchTimeout = null
function applyFilters() {
    router.get(route('admin.hotels.index'), {
        search: search.value || undefined,
        tipe: tipe.value || undefined,
        kota: kota.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

function onSearchInput() {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => applyFilters(), 400)
}

watch([tipe, kota, status], () => applyFilters())

const isDeleteModalOpen = ref(false)
const hotelToDelete = ref(null)

function confirmDelete(hotel) {
    hotelToDelete.value = hotel
    isDeleteModalOpen.value = true
}

function deleteHotel() {
    if (hotelToDelete.value) {
        router.delete(route('admin.hotels.destroy', hotelToDelete.value.id), {
            onSuccess: () => {
                isDeleteModalOpen.value = false
                hotelToDelete.value = null
            },
        })
    }
}

function toggleStatus(hotel) {
    router.patch(route('admin.hotels.toggle-status', hotel.id), {}, { preserveScroll: true })
}

function formatPrice(price) {
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-heading font-bold text-3xl text-[#003527] tracking-tight">Kelola Hotel</h2>
                    <p class="text-sm text-[#404945]/70 mt-1">Data akomodasi, tipe, dan harga</p>
                </div>
                <Link :href="route('admin.hotels.create')" class="inline-flex items-center justify-center px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl transition-all shadow-lg shadow-emerald-200 gap-2">
                    <Plus class="w-4 h-4" />
                    Tambah Hotel
                </Link>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-emerald-100 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative md:col-span-1">
                    <input v-model="search" @input="onSearchInput" type="text" placeholder="Cari nama hotel..." class="w-full pl-10 pr-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" />
                    <Search class="w-4 h-4 text-[#404945]/50 absolute left-3.5 top-3.5" />
                </div>
                <select v-model="tipe" class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm text-[#404945] cursor-pointer">
                    <option value="">Semua Tipe</option>
                    <option v-for="t in tipes" :key="t" :value="t">{{ t }}</option>
                </select>
                <select v-model="kota" class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm text-[#404945] cursor-pointer">
                    <option value="">Semua Kota</option>
                    <option v-for="k in kotas" :key="k" :value="k">{{ k }}</option>
                </select>
                <select v-model="status" class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm text-[#404945] cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="bg-emerald-50/60 border-b border-emerald-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-[#404945]/60 uppercase tracking-wider">Hotel</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-[#404945]/60 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-[#404945]/60 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-[#404945]/60 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[#404945]/60 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-50">
                        <tr v-for="h in hotels.data" :key="h.id" class="hover:bg-emerald-50/30">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center shrink-0 border border-emerald-100">
                                        <Building class="w-6 h-6 text-emerald-300" />
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#003527] text-sm">{{ h.nama }}</p>
                                        <p class="text-xs text-[#404945]/60 mt-1">{{ h.kota }} • {{ h.bintang }} Bintang</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm">{{ h.tipe }}</td>
                            <td class="px-6 py-5 text-sm font-semibold">{{ formatPrice(h.harga_min) }} - {{ formatPrice(h.harga_max) }}</td>
                            <td class="px-6 py-5">
                                <button @click="toggleStatus(h)" :class="['px-3 py-1 rounded-full text-xs font-semibold cursor-pointer', h.status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600']">
                                    {{ h.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-5 text-right flex items-center justify-end gap-1">
                                <Link :href="route('admin.hotels.edit', h.id)" class="p-2 text-[#404945]/60 hover:text-emerald-700"><Edit2 class="w-4 h-4" /></Link>
                                <button @click="confirmDelete(h)" class="p-2 text-[#404945]/60 hover:text-rose-600"><Trash2 class="w-4 h-4" /></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-sm w-full p-7 shadow-2xl text-center">
                    <h3 class="font-heading font-bold text-lg text-[#003527]">Hapus Hotel?</h3>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button @click="isDeleteModalOpen = false" class="px-5 py-2.5 rounded-xl border border-emerald-100 text-sm font-semibold">Batal</button>
                        <button @click="deleteHotel" class="px-5 py-2.5 rounded-xl bg-rose-600 text-white text-sm font-semibold">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>