<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Plus, Search, Edit2, Trash2, MapPin, ImageIcon } from 'lucide-vue-next'

const props = defineProps({
    destinations: Object,
    categories: Array,
    stats: Object,
    filters: Object,
})

const search = ref(props.filters.search || '')
const categoryId = ref(props.filters.category_id || '')
const status = ref(props.filters.status || '')
const priceStatus = ref(props.filters.price_status || '')
const photoStatus = ref(props.filters.photo_status || '')
let searchTimeout = null

watch(
    () => props.filters,
    (f) => {
        search.value = f.search || ''
        categoryId.value = f.category_id || ''
        status.value = f.status || ''
        priceStatus.value = f.price_status || ''
        photoStatus.value = f.photo_status || ''
    },
    { deep: true }
)

function applyFilters() {
    router.get('/admin/destinations', {
        search: search.value || undefined,
        category_id: categoryId.value || undefined,
        status: status.value || undefined,
        price_status: priceStatus.value || undefined,
        photo_status: photoStatus.value || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

watch([categoryId, status, priceStatus, photoStatus], () => applyFilters())

function onSearchInput() {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => applyFilters(), 350)
}

const isDeleteModalOpen = ref(false)
const destinationToDelete = ref(null)

function confirmDelete(dest) {
    destinationToDelete.value = dest
    isDeleteModalOpen.value = true
}

function deleteDestination() {
    router.delete(`/admin/destinations/${destinationToDelete.value.id}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false
            destinationToDelete.value = null
        },
    })
}

function toggleStatus(dest) {
    router.patch(`/admin/destinations/${dest.id}/toggle-status`, {}, { preserveScroll: true })
}

function formatPrice(price) {
    if (price === null || price === undefined || price === '') return null
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}

function onKeydown(event) {
    if (event.key === 'Escape') isDeleteModalOpen.value = false
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))
</script>

<template>
    <AdminLayout title="Kelola Destinasi">
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="font-heading font-bold text-2xl sm:text-3xl text-brand-dark">Katalog Destinasi</h1>
                    <p class="text-sm text-brand-muted mt-1">Kelola destinasi, galeri foto, dan estimasi harga katalog.</p>
                </div>
                <Link href="/admin/destinations/create" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors min-h-[44px]">
                    <Plus class="w-4 h-4" aria-hidden="true" /> Tambah Destinasi
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl p-4 sm:p-5 border border-brand-dark/10 grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="relative md:col-span-2">
                    <label for="admin-dest-search" class="sr-only">Cari destinasi</label>
                    <input id="admin-dest-search" v-model="search" @input="onSearchInput" type="text" placeholder="Cari nama destinasi atau kota..." class="w-full pl-9 pr-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm min-h-[44px]" />
                    <Search class="w-4 h-4 text-brand-muted absolute left-3 top-3.5" aria-hidden="true" />
                </div>
                <select aria-label="Filter kategori" v-model="categoryId" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark min-h-[44px]">
                    <option value="">Semua kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nama_kategori }}</option>
                </select>
                <select aria-label="Filter status" v-model="status" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark min-h-[44px]">
                    <option value="">Semua status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <select aria-label="Filter estimasi harga" v-model="priceStatus" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark min-h-[44px]">
                    <option value="">Semua harga</option>
                    <option value="missing">Harga belum diisi</option>
                </select>
            </div>

            <div v-if="photoStatus === 'missing' || priceStatus === 'missing'" class="flex flex-wrap items-center justify-between gap-2 bg-coral-light border border-coral/40 rounded-xl px-4 py-3 text-sm text-coral-text">
                <span>
                    Menampilkan destinasi yang
                    <strong v-if="photoStatus === 'missing'">belum memiliki foto</strong>
                    <strong v-if="photoStatus === 'missing' && priceStatus === 'missing'"> dan </strong>
                    <strong v-if="priceStatus === 'missing'">belum memiliki estimasi harga</strong>.
                </span>
                <Link href="/admin/destinations" class="text-xs font-bold underline">Tampilkan semua</Link>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-brand-dark/10 overflow-x-auto">
                <table class="w-full min-w-[700px]">
                    <thead class="bg-brand-bg">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-brand-muted">Destinasi &amp; kota</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-brand-muted">Kategori</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-brand-muted">Estimasi harga</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-brand-muted">Foto</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-brand-muted">Status</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-brand-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-dark/10 text-sm">
                        <tr v-for="dest in destinations.data" :key="dest.id" class="hover:bg-brand-bg/50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-lg bg-brand-input flex items-center justify-center overflow-hidden shrink-0">
                                        <ImageIcon v-if="!dest.images?.length" class="w-5 h-5 text-brand-dark/25" aria-hidden="true" />
                                        <img v-else :src="dest.images[0].url_gambar" class="w-full h-full object-cover" :alt="dest.nama" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-brand-dark">{{ dest.nama }}</p>
                                        <p class="text-xs text-brand-muted">{{ dest.kota }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-brand-input text-brand-dark">{{ dest.category?.nama_kategori }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <template v-if="formatPrice(dest.estimasi_harga)">
                                    <span class="font-semibold text-brand-dark tabular-nums">{{ formatPrice(dest.estimasi_harga) }}</span>
                                    <span class="block text-xs text-brand-muted">{{ dest.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }}</span>
                                </template>
                                <span v-else class="inline-flex px-2.5 py-0.5 rounded-md text-xs font-semibold bg-coral-light text-coral-text">
                                    Belum dihitung
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-brand-muted tabular-nums">
                                {{ dest.images?.length || 0 }} foto
                            </td>
                            <td class="px-5 py-4">
                                <button
                                    type="button"
                                    @click="toggleStatus(dest)"
                                    :class="['px-2.5 py-1 rounded-md text-xs font-semibold min-h-[32px]', dest.status === 'aktif' ? 'bg-teal-light text-brand-primary' : 'bg-brand-input text-brand-muted']"
                                >
                                    {{ dest.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex justify-end gap-1">
                                    <Link
                                        :href="`/admin/destinations/${dest.id}/edit`"
                                        :aria-label="`Edit destinasi ${dest.nama}`"
                                        class="p-2 text-brand-muted hover:text-brand-dark hover:bg-brand-bg rounded-lg min-h-[40px] min-w-[40px] inline-flex items-center justify-center"
                                        title="Edit destinasi, harga & foto"
                                    >
                                        <Edit2 class="w-4 h-4" aria-hidden="true" />
                                    </Link>
                                    <button
                                        type="button"
                                        @click="confirmDelete(dest)"
                                        :aria-label="`Hapus destinasi ${dest.nama}`"
                                        class="p-2 text-brand-muted hover:text-coral-text hover:bg-coral-light rounded-lg min-h-[40px] min-w-[40px] inline-flex items-center justify-center"
                                        title="Hapus destinasi"
                                    >
                                        <Trash2 class="w-4 h-4" aria-hidden="true" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="destinations.data.length === 0">
                            <td colspan="6" class="px-5 py-14 text-center">
                                <MapPin class="w-10 h-10 text-brand-muted mx-auto mb-2" aria-hidden="true" />
                                <p class="font-semibold text-base text-brand-dark">Tidak ada destinasi</p>
                                <p class="text-xs text-brand-muted mt-1">Tambahkan destinasi baru atau ubah filter pencarian.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="destinations.last_page > 1" class="flex items-center justify-between text-sm">
                <div class="text-brand-muted tabular-nums">
                    Menampilkan {{ destinations.from ?? 0 }} sampai {{ destinations.to ?? 0 }} dari {{ destinations.total }} data
                </div>
                <div class="flex gap-2">
                    <Link v-if="destinations.prev_page_url" :href="destinations.prev_page_url" class="px-4 py-2 rounded-lg border border-brand-dark/15 bg-white font-medium text-brand-dark hover:bg-brand-bg min-h-[40px] inline-flex items-center">
                        Sebelumnya
                    </Link>
                    <Link v-if="destinations.next_page_url" :href="destinations.next_page_url" class="px-4 py-2 rounded-lg border border-brand-dark/15 bg-white font-medium text-brand-dark hover:bg-brand-bg min-h-[40px] inline-flex items-center">
                        Selanjutnya
                    </Link>
                </div>
            </div>

            <!-- Delete Modal -->
            <div v-if="isDeleteModalOpen" role="dialog" aria-modal="true" aria-labelledby="dest-delete-title" class="fixed inset-0 z-50 bg-brand-dark/50 flex items-center justify-center p-4" @click.self="isDeleteModalOpen = false">
                <div class="bg-white rounded-xl max-w-sm w-full p-6 text-center shadow-xl border border-brand-dark/10">
                    <h3 id="dest-delete-title" class="font-heading font-bold text-lg text-brand-dark">Hapus destinasi?</h3>
                    <p class="text-sm text-brand-muted mt-1.5">
                        Yakin ingin menghapus <strong>"{{ destinationToDelete?.nama }}"</strong>?
                    </p>
                    <div class="flex justify-center gap-3 mt-6">
                        <button type="button" @click="isDeleteModalOpen = false" class="px-4 py-2.5 rounded-lg border border-brand-dark/15 text-sm font-semibold text-brand-dark min-h-[44px]">Batal</button>
                        <button type="button" @click="deleteDestination" class="px-4 py-2.5 rounded-lg bg-coral-text text-white text-sm font-semibold min-h-[44px]">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
