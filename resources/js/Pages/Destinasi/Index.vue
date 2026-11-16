<script setup>
import { ref, watch, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Search, MapPin, Users, Calendar, ChevronRight } from 'lucide-vue-next'

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
const status = ref(props.filters.status || 'aktif')

let searchTimeout = null

function applyFilters() {
    router.get(route('destinasi.index'), {
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

const getCoverImage = (dest) => {
    if (dest.images && dest.images.length > 0) {
        return dest.images.reduce((lowest, img) =>
            img.urutan_tampil < lowest.urutan_tampil ? img : lowest, dest.images[0]).url_gambar
    }
    return null
}

function formatPrice(price) {
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}
</script>

<template>
    <PublicLayout>
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-[#eefdf5] via-white to-white">
            <div class="absolute inset-0 -z-10">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-100 rounded-full blur-[120px] opacity-50" />
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-100 rounded-full blur-[120px] opacity-30" />
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-32">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="font-heading font-bold text-4xl md:text-5xl lg:text-6xl text-[#003527] tracking-tight mb-6 leading-tight">
                        Temukan Destinasi<br>
                        <span class="text-[#174c3c]">Impianmu</span> di Indonesia
                    </h1>
                    <p class="text-lg md:text-xl text-[#404945] leading-relaxed mb-10 max-w-2xl mx-auto">
                        Jelajahi ribuan destinasi wisata, kuliner, budaya, dan alam terkurasi. Rencanakan perjalananmu dengan estimasi biaya transparan.
                    </p>
                    <Link
                        href="/destinasi"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-base rounded-2xl shadow-lg shadow-emerald-200 transition-all"
                    >
                        Mulai Jelajahi
                        <ChevronRight class="w-5 h-5" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Stats Bar -->
        <section class="py-12 bg-white border-y border-emerald-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <p class="text-3xl md:text-4xl font-bold text-[#003527]">{{ destinations.total }}</p>
                    <p class="text-sm text-[#404945]/70 mt-1">Destinasi Terkurasi</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-bold text-[#003527]">{{ categories.length }}</p>
                    <p class="text-sm text-[#404945]/70 mt-1">Kategori Wisata</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-bold text-[#003527]">{{ destinations.data.filter(d => d.status === 'aktif').length }}</p>
                    <p class="text-sm text-[#404945]/70 mt-1">Destinasi Aktif</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-bold text-[#003527]">100%</p>
                    <p class="text-sm text-[#404945]/70 mt-1">Data Koordinat Peta</p>
                </div>
            </div>
        </section>

        <!-- Catalog Section -->
        <section class="py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <!-- Filters -->
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 md:p-6 mb-8">
                    <h2 class="font-heading font-semibold text-[#003527] mb-4">Filter & Pencarian</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="relative md:col-span-1">
                            <input
                                v-model="search"
                                @input="onSearchInput"
                                type="text"
                                placeholder="Cari nama destinasi atau kota..."
                                class="w-full pl-10 pr-4 py-3 bg-emerald-50 border border-emerald-100 rounded-xl text-base focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                            />
                            <Search class="w-5 h-5 text-[#404945]/50 absolute left-3 top-3.5" />
                        </div>

                        <select
                            v-model="categoryId"
                            class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-xl text-base text-[#404945] focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all cursor-pointer"
                        >
                            <option value="">Semua Kategori</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.nama_kategori }}
                            </option>
                        </select>

                        <select
                            v-model="status"
                            class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-xl text-base text-[#404945] focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all cursor-pointer"
                        >
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Destinations Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <article
                        v-for="dest in destinations.data"
                        :key="dest.id"
                        class="group bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden hover:shadow-xl transition-shadow duration-300"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img
                                v-if="getCoverImage(dest)"
                                :src="getCoverImage(dest)"
                                :alt="dest.nama"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <template v-else>
                                <div class="w-full h-full bg-emerald-50 flex items-center justify-center">
                                    <MapPin class="w-12 h-12 text-emerald-200" />
                                </div>
                            </template>

                            <!-- Status badge -->
                            <span
                                :class="[
                                    'absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-semibold',
                                    dest.status === 'aktif' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-600',
                                ]"
                            >
                                {{ dest.status === 'aktif' ? 'Tersedia' : 'Nonaktif' }}
                            </span>

                            <!-- Category badge -->
                            <span class="absolute top-3 right-3 px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-medium text-[#003527] border border-emerald-100">
                                {{ dest.category?.nama_kategori }}
                            </span>
                        </div>

                        <div class="p-5 space-y-3">
                            <Link :href="`/destinasi/${dest.id}`">
                                <h3 class="font-heading font-semibold text-lg text-[#003527] group-hover:text-[#174c3c] transition-colors line-clamp-1">
                                    {{ dest.nama }}
                                </h3>
                            </Link>

                            <div class="flex items-center gap-2 text-sm text-[#404945]/70">
                                <MapPin class="w-4 h-4 shrink-0" />
                                <span class="truncate">{{ dest.kota }}</span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-emerald-100">
                                <div>
                                    <p class="font-heading font-bold text-[#003527]">{{ formatPrice(dest.estimasi_harga) }}</p>
                                    <p class="text-xs text-[#404945]/60">
                                        {{ dest.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }}
                                    </p>
                                </div>
                                <Link
                                    :href="`/destinasi/${dest.id}`"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl transition-colors"
                                >
                                    Detail
                                    <ChevronRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </article>

                    <div v-if="destinations.data.length === 0" class="col-span-full text-center py-20">
                        <MapPin class="w-16 h-16 text-emerald-200 mx-auto mb-4" />
                        <h3 class="font-heading font-semibold text-xl text-[#003527] mb-2">Destinasi Tidak Ditemukan</h3>
                        <p class="text-[#404945]/70 mb-6">Coba ubah kata kunci pencarian atau filter kategori.</p>
                        <button
                            @click="() => { search.value = ''; categoryId.value = ''; status.value = 'aktif'; applyFilters(); }"
                            class="px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold rounded-xl shadow-sm transition-colors"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="destinations.last_page > 1" class="mt-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-[#404945]/70">
                        Menampilkan <strong>{{ destinations.from ?? 0 }}</strong> - <strong>{{ destinations.to ?? 0 }}</strong> dari <strong>{{ destinations.total }}</strong> destinasi
                    </p>
                    <div class="flex items-center gap-1">
                        <component
                            :is="destinations.prev_page_url ? 'a' : 'span'"
                            :href="destinations.prev_page_url || undefined"
                            :class="[
                                'px-4 py-2 rounded-xl border transition-colors',
                                destinations.prev_page_url ? 'border-emerald-100 text-[#003527] hover:bg-emerald-50' : 'border-emerald-100 text-gray-300 cursor-not-allowed',
                            ]"
                        >
                            <ChevronRight class="w-5 h-5 rotate-180" />
                        </component>

                        <span class="px-4 text-sm font-medium text-[#003527]">
                            {{ destinations.current_page }} / {{ destinations.last_page }}
                        </span>

                        <component
                            :is="destinations.next_page_url ? 'a' : 'span'"
                            :href="destinations.next_page_url || undefined"
                            :class="[
                                'px-4 py-2 rounded-xl border transition-colors',
                                destinations.next_page_url ? 'border-emerald-100 text-[#003527] hover:bg-emerald-50' : 'border-emerald-100 text-gray-300 cursor-not-allowed',
                            ]"
                        >
                            <ChevronRight class="w-5 h-5" />
                        </component>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 lg:py-24 bg-[#003527] relative overflow-hidden">
            <div class="absolute inset-0 -z-10">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-600/20 rounded-full blur-[120px]" />
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-10 text-center">
                <h2 class="font-heading font-bold text-3xl md:text-4xl text-white mb-4">
                    Siap Memulai Perjalanan?
                </h2>
                <p class="text-emerald-100 text-lg mb-8 max-w-2xl mx-auto">
                    Buat perencanaan perjalanan lengkap dengan itinerary harian, estimasi biaya otomatis, dan export PDF.
                </p>
                <Link
                    v-if="$page.props.auth?.user"
                    href="/trips/create"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-base rounded-2xl shadow-lg shadow-emerald-500/30 transition-all"
                >
                    Buat Trip Baru
                    <ChevronRight class="w-5 h-5" />
                </Link>
                <Link
                    v-else
                    :href="route('register')"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-white hover:bg-emerald-50 text-[#003527] font-semibold text-base rounded-2xl shadow-lg transition-all"
                >
                    Daftar Gratis
                    <ChevronRight class="w-5 h-5" />
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>