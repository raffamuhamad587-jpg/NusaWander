<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AiAlternativesModal from '@/Components/AiAlternativesModal.vue'
import { Search, MapPin, Globe, RotateCcw } from 'lucide-vue-next'

const props = defineProps({
    destinations: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    kotas: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        required: true,
    },
})

const search = ref(props.filters.search || '')
const categoryId = ref(props.filters.category_id || '')
const kota = ref(props.filters.kota || '')
const showAiModal = ref(false)
const isFiltering = ref(false)

let searchTimeout = null

function applyFilters() {
    isFiltering.value = true
    router.get('/destinasi', {
        search: search.value.trim() || undefined,
        category_id: categoryId.value || undefined,
        kota: kota.value || undefined,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
        onFinish: () => {
            isFiltering.value = false
        },
    })
}

function resetFilters() {
    search.value = ''
    categoryId.value = ''
    kota.value = ''
    applyFilters()
}

function onSearchInput() {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => applyFilters(), 350)
}

watch([categoryId, kota], () => applyFilters())

function getCoverImage(dest) {
    if (dest.images && dest.images.length > 0) {
        return dest.images[0].url_gambar
    }
    return null
}

function formatPrice(price) {
    if (price === null || price === undefined || price === '') return null
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}
</script>

<template>
    <Head title="Daftar Destinasi | NUSA WANDER" />

    <PublicLayout>
        <section class="py-8 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-7">
                    <div>
                        <h1 class="font-heading font-bold text-2xl sm:text-3xl lg:text-4xl text-brand-dark tracking-tight">
                            Daftar Destinasi Indonesia
                        </h1>
                        <p class="text-sm text-brand-muted mt-1.5">
                            Cari dan saring destinasi berdasarkan kategori serta wilayah sebelum menyusun jadwal perjalanan.
                        </p>
                    </div>
                    <Link
                        :href="$page.props.auth?.user ? '/trips/create' : '/login'"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors shrink-0 min-h-[44px]"
                    >
                        Susun Rencana Perjalanan
                    </Link>
                </div>

                <!-- Filter Bar -->
                <div class="bg-white rounded-xl border border-brand-dark/10 p-4 sm:p-5 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
                        <div class="md:col-span-5">
                            <label for="catalog-search" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Kata kunci
                            </label>
                            <div class="relative">
                                <input
                                    id="catalog-search"
                                    v-model="search"
                                    @input="onSearchInput"
                                    type="text"
                                    placeholder="Nama destinasi, kota, atau alamat..."
                                    class="w-full pl-9 pr-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                                />
                                <Search class="w-4 h-4 text-brand-muted absolute left-3 top-3.5" aria-hidden="true" />
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <label for="catalog-category" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Kategori
                            </label>
                            <select
                                id="catalog-category"
                                v-model="categoryId"
                                class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                            >
                                <option value="">Semua kategori</option>
                                <option v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                    {{ cat.nama_kategori }}
                                </option>
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label for="catalog-kota" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Wilayah / Kota
                            </label>
                            <select
                                id="catalog-kota"
                                v-model="kota"
                                class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                            >
                                <option value="">Semua wilayah</option>
                                <option v-for="k in kotas" :key="k" :value="k">
                                    {{ k }}
                                </option>
                            </select>
                        </div>

                        <div class="md:col-span-1">
                            <button
                                type="button"
                                @click="resetFilters"
                                aria-label="Reset semua filter"
                                title="Reset filter"
                                class="w-full py-2.5 px-3 rounded-lg border border-brand-dark/15 bg-white text-brand-dark hover:bg-brand-input/60 flex items-center justify-center gap-1.5 text-xs font-semibold transition-colors min-h-[44px]"
                            >
                                <RotateCcw class="w-4 h-4" aria-hidden="true" />
                                <span class="md:hidden">Reset Filter</span>
                            </button>
                        </div>
                    </div>

                    <p v-if="isFiltering" role="status" class="text-xs text-brand-muted mt-3">
                        Memperbarui daftar destinasi...
                    </p>
                </div>

                <!-- Destinations Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <article
                        v-for="dest in destinations.data"
                        :key="dest.id"
                        class="bg-white rounded-xl border border-brand-dark/10 overflow-hidden hover:border-brand-primary/40 transition-colors flex flex-col justify-between"
                    >
                        <div>
                            <div class="relative aspect-[16/10] overflow-hidden bg-brand-input/70">
                                <img
                                    v-if="getCoverImage(dest)"
                                    :src="getCoverImage(dest)"
                                    :alt="dest.nama"
                                    loading="lazy"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <MapPin class="w-10 h-10 text-brand-dark/25" aria-hidden="true" />
                                </div>

                                <span class="absolute top-3 left-3 px-2.5 py-0.5 bg-white/95 rounded-md text-xs font-medium text-brand-dark border border-brand-dark/10">
                                    {{ dest.category?.nama_kategori }}
                                </span>
                            </div>

                            <div class="p-5 space-y-2">
                                <div class="flex items-center gap-1.5 text-xs text-brand-muted">
                                    <MapPin class="w-3.5 h-3.5 shrink-0 text-brand-primary" aria-hidden="true" />
                                    <span class="truncate">{{ dest.kota }}</span>
                                </div>
                                <h2 class="font-heading font-semibold text-lg text-brand-dark leading-snug line-clamp-1">
                                    <Link :href="`/destinasi/${dest.id}`" class="hover:text-brand-primary">
                                        {{ dest.nama }}
                                    </Link>
                                </h2>
                                <p class="text-xs text-brand-muted line-clamp-2 leading-relaxed">
                                    {{ dest.deskripsi }}
                                </p>
                            </div>
                        </div>

                        <div class="px-5 pb-5 pt-3 border-t border-brand-dark/5 flex items-center justify-between gap-3">
                            <div>
                                <template v-if="formatPrice(dest.estimasi_harga)">
                                    <p class="font-bold text-sm text-brand-dark tabular-nums">{{ formatPrice(dest.estimasi_harga) }}</p>
                                    <p class="text-[11px] text-brand-muted">
                                        {{ dest.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }}
                                    </p>
                                </template>
                                <span v-else class="inline-flex px-2.5 py-0.5 rounded-md text-xs font-semibold bg-coral-light text-coral-text">
                                    Belum dihitung
                                </span>
                            </div>
                            <Link
                                :href="`/destinasi/${dest.id}`"
                                class="inline-flex items-center px-4 py-2 bg-brand-bg hover:bg-brand-input text-brand-dark text-xs font-semibold rounded-lg transition-colors min-h-[40px]"
                            >
                                Lihat Detail
                            </Link>
                        </div>
                    </article>

                    <!-- Empty state + AI alternative prompt (R-27 + FR-13) -->
                    <div v-if="destinations.data.length === 0" class="col-span-full bg-white rounded-xl border border-brand-dark/10 text-center py-14 px-6">
                        <MapPin class="w-10 h-10 text-brand-muted mx-auto mb-3" aria-hidden="true" />
                        <h2 class="font-heading font-bold text-xl text-brand-dark mb-2">
                            Tidak ada destinasi yang cocok di katalog
                        </h2>
                        <p class="text-sm text-brand-muted mb-6 max-w-md mx-auto">
                            Tidak ditemukan hasil untuk filter saat ini. Reset pencarian atau gunakan opsi pencarian alternatif eksternal dengan AI.
                        </p>
                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <button
                                type="button"
                                @click="resetFilters"
                                class="px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors min-h-[44px]"
                            >
                                Reset Filter
                            </button>
                            <button
                                v-if="$page.props.auth?.user"
                                type="button"
                                @click="showAiModal = true"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-coral-light hover:bg-coral/25 text-coral-text border border-coral/40 font-semibold text-sm rounded-lg transition-colors min-h-[44px]"
                            >
                                <Globe class="w-4 h-4" aria-hidden="true" />
                                Cari Alternatif dengan AI
                            </button>
                            <Link
                                v-else
                                href="/login"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-input hover:bg-brand-dark hover:text-white text-brand-dark font-semibold text-sm rounded-lg transition-colors min-h-[44px]"
                            >
                                <Globe class="w-4 h-4" aria-hidden="true" />
                                Masuk untuk Cari Alternatif AI
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="destinations.last_page > 1" class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-brand-muted tabular-nums">
                        Menampilkan <strong class="text-brand-dark">{{ destinations.from ?? 0 }}</strong> sampai <strong class="text-brand-dark">{{ destinations.to ?? 0 }}</strong> dari <strong class="text-brand-dark">{{ destinations.total }}</strong> destinasi
                    </p>
                    <div class="flex items-center gap-2">
                        <Link
                            v-if="destinations.prev_page_url"
                            :href="destinations.prev_page_url"
                            class="px-4 py-2 rounded-lg border border-brand-dark/15 bg-white text-sm font-medium text-brand-dark hover:bg-brand-input/60 min-h-[44px] inline-flex items-center"
                        >
                            Sebelumnya
                        </Link>
                        <span class="px-3 text-sm font-semibold text-brand-dark tabular-nums">
                            {{ destinations.current_page }} / {{ destinations.last_page }}
                        </span>
                        <Link
                            v-if="destinations.next_page_url"
                            :href="destinations.next_page_url"
                            class="px-4 py-2 rounded-lg border border-brand-dark/15 bg-white text-sm font-medium text-brand-dark hover:bg-brand-input/60 min-h-[44px] inline-flex items-center"
                        >
                            Selanjutnya
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <AiAlternativesModal
            :show="showAiModal"
            :initial-area="kota || search"
            @close="showAiModal = false"
        />
    </PublicLayout>
</template>
