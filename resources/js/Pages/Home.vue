<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { MapPin, ArrowRight, Search } from 'lucide-vue-next'

const props = defineProps({
    featuredDestinations: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    kotas: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({ totalDestinations: 0, totalCategories: 0, totalCities: 0 }),
    },
})

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedKota = ref('')

const spotlightDestination = computed(() => props.featuredDestinations[0] ?? null)
const remainingDestinations = computed(() => props.featuredDestinations.slice(1, 7))

function submitSearch() {
    router.get('/destinasi', {
        search: searchQuery.value.trim() || undefined,
        category_id: selectedCategory.value || undefined,
        kota: selectedKota.value || undefined,
    })
}

function getCoverImage(dest) {
    if (dest?.images && dest.images.length > 0) {
        return dest.images[0].url_gambar
    }
    return null
}

function formatPrice(price) {
    if (price === null || price === undefined || price === '') return null
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}

const planningSteps = [
    {
        num: '01',
        title: 'Pilih wilayah dan kategori',
        desc: 'Telusuri katalog destinasi berdasarkan kota tujuan, kategori wisata, serta estimasi biaya.',
    },
    {
        num: '02',
        title: 'Isi parameter perjalanan',
        desc: 'Tentukan tanggal berangkat, durasi hari, jumlah peserta, batas anggaran, dan prioritas biaya.',
    },
    {
        num: '03',
        title: 'Sesuaikan jadwal per hari',
        desc: 'Sistem menyusun itinerary harian dari katalog; Anda bebas menambah, menghapus, atau menggeser urutannya.',
    },
    {
        num: '04',
        title: 'Pantau total dan sisa anggaran',
        desc: 'Lihat total estimasi terhadap batas anggaran, simpan di Itinerary Saya, atau unduh ringkasan PDF.',
    },
]
</script>

<template>
    <Head title="NUSA WANDER: Smart Travel &amp; Budget Planner" />

    <PublicLayout>
        <!-- Editorial Asymmetric Hero on Warm Cream (R-05, R-09: no eyebrow pill above H1) -->
        <section class="border-b border-brand-dark/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-10 sm:py-14 lg:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                    <!-- Left: Headline, copy, CTA, integrated search -->
                    <div class="lg:col-span-7 space-y-6">
                        <h1 class="font-heading font-bold text-3xl sm:text-4xl lg:text-[46px] text-brand-dark leading-[1.15] tracking-tight">
                            Susun rencana perjalanan Indonesia tanpa menebak anggaran.
                        </h1>

                        <p class="text-sm sm:text-base text-brand-muted leading-relaxed max-w-xl">
                            NUSA WANDER mengumpulkan katalog destinasi, menyusun itinerary per hari sesuai durasi dan minat Anda, lalu menghitung estimasi biaya beserta sisa anggaran dalam satu tempat.
                        </p>

                        <!-- Primary Actions (R-15 specific CTA, R-08 arrow only on main forward action) -->
                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <Link
                                :href="$page.props.auth?.user ? '/trips/create' : '/login'"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary hover:bg-brand-primaryHover active:bg-brand-dark text-white font-semibold text-sm rounded-lg transition-colors min-h-[44px]"
                            >
                                <span>Susun Rencana Perjalanan</span>
                                <ArrowRight class="w-4 h-4" aria-hidden="true" />
                            </Link>
                            <Link
                                href="/destinasi"
                                class="inline-flex items-center px-5 py-3 bg-white hover:bg-brand-input/60 border border-brand-dark/15 text-brand-dark font-semibold text-sm rounded-lg transition-colors min-h-[44px]"
                            >
                                Lihat {{ stats.totalDestinations }} Destinasi
                            </Link>
                        </div>

                        <!-- Integrated Catalog Search (no detached floating box) -->
                        <form
                            @submit.prevent="submitSearch"
                            aria-label="Pencarian katalog destinasi"
                            class="bg-white rounded-xl border border-brand-dark/10 p-4 sm:p-5 space-y-3"
                        >
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                <div class="sm:col-span-5">
                                    <label for="home-search" class="block text-xs font-semibold text-brand-dark mb-1">
                                        Cari destinasi
                                    </label>
                                    <div class="relative">
                                        <Search class="w-4 h-4 text-brand-muted absolute left-3 top-3.5" aria-hidden="true" />
                                        <input
                                            id="home-search"
                                            v-model="searchQuery"
                                            type="text"
                                            placeholder="Nama tempat atau kota..."
                                            class="w-full pl-9 pr-3 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                                        />
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="home-category" class="block text-xs font-semibold text-brand-dark mb-1">
                                        Kategori
                                    </label>
                                    <select
                                        id="home-category"
                                        v-model="selectedCategory"
                                        class="w-full px-3 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                                    >
                                        <option value="">Semua</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                            {{ cat.nama_kategori }}
                                        </option>
                                    </select>
                                </div>

                                <div class="sm:col-span-4">
                                    <label for="home-kota" class="block text-xs font-semibold text-brand-dark mb-1">
                                        Wilayah
                                    </label>
                                    <div class="flex gap-2">
                                        <select
                                            id="home-kota"
                                            v-model="selectedKota"
                                            class="flex-1 min-w-0 px-3 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                                        >
                                            <option value="">Semua kota</option>
                                            <option v-for="kota in kotas" :key="kota" :value="kota">
                                                {{ kota }}
                                            </option>
                                        </select>
                                        <button
                                            type="submit"
                                            class="px-4 py-2.5 bg-brand-dark hover:bg-ocean text-white text-sm font-semibold rounded-lg transition-colors shrink-0 min-h-[44px]"
                                        >
                                            Cari
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Real Catalog Facts (R-17: real DB counts only) -->
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-1 pt-1 text-xs text-brand-muted border-t border-brand-dark/5">
                                <span><strong class="text-brand-dark tabular-nums">{{ stats.totalDestinations }}</strong> destinasi aktif</span>
                                <span><strong class="text-brand-dark tabular-nums">{{ stats.totalCategories }}</strong> kategori wisata</span>
                                <span><strong class="text-brand-dark tabular-nums">{{ stats.totalCities }}</strong> wilayah tersedia</span>
                            </div>
                        </form>
                    </div>

                    <!-- Right: Single Focal-Point Spotlight Card from Real Database -->
                    <div class="lg:col-span-5">
                        <article
                            v-if="spotlightDestination"
                            class="bg-white rounded-xl border border-brand-dark/10 overflow-hidden"
                        >
                            <div class="relative aspect-[4/3] bg-brand-input overflow-hidden">
                                <img
                                    v-if="getCoverImage(spotlightDestination)"
                                    :src="getCoverImage(spotlightDestination)"
                                    :alt="spotlightDestination.nama"
                                    fetchpriority="high"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-brand-dark text-white">
                                    <MapPin class="w-8 h-8 text-coral mb-2" aria-hidden="true" />
                                    <p class="font-heading font-bold text-lg">{{ spotlightDestination.nama }}</p>
                                    <p class="text-xs text-white/80 mt-1">{{ spotlightDestination.kota }}, Indonesia</p>
                                </div>
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-brand-dark border border-brand-dark/10">
                                    {{ spotlightDestination.category?.nama_kategori }}
                                </span>
                            </div>

                            <div class="p-5 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-medium text-brand-muted flex items-center gap-1">
                                            <MapPin class="w-3.5 h-3.5 text-brand-primary shrink-0" aria-hidden="true" />
                                            {{ spotlightDestination.kota }}
                                        </p>
                                        <h2 class="font-heading font-bold text-xl text-brand-dark mt-0.5">
                                            <Link :href="`/destinasi/${spotlightDestination.id}`" class="hover:text-brand-primary">
                                                {{ spotlightDestination.nama }}
                                            </Link>
                                        </h2>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-coral-light text-coral-text shrink-0">
                                        Sorotan Katalog
                                    </span>
                                </div>

                                <p class="text-xs sm:text-sm text-brand-muted leading-relaxed line-clamp-3">
                                    {{ spotlightDestination.deskripsi }}
                                </p>

                                <div class="pt-3 border-t border-brand-dark/10 flex items-center justify-between gap-3">
                                    <div>
                                        <template v-if="formatPrice(spotlightDestination.estimasi_harga)">
                                            <p class="font-bold text-base text-brand-dark tabular-nums">
                                                {{ formatPrice(spotlightDestination.estimasi_harga) }}
                                            </p>
                                            <p class="text-xs text-brand-muted">
                                                {{ spotlightDestination.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }}
                                            </p>
                                        </template>
                                        <span v-else class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-coral-light text-coral-text">
                                            Belum dihitung
                                        </span>
                                    </div>
                                    <Link
                                        :href="`/destinasi/${spotlightDestination.id}`"
                                        class="px-4 py-2.5 rounded-lg bg-brand-input hover:bg-brand-dark hover:text-white text-brand-dark text-xs font-semibold transition-colors min-h-[44px] inline-flex items-center"
                                    >
                                        Lihat Detail
                                    </Link>
                                </div>
                            </div>
                        </article>

                        <div v-else class="bg-white rounded-xl border border-brand-dark/10 p-8 text-center">
                            <MapPin class="w-8 h-8 text-brand-muted mx-auto mb-2" aria-hidden="true" />
                            <p class="text-sm font-medium text-brand-dark">Katalog destinasi masih kosong.</p>
                            <p class="text-xs text-brand-muted mt-1">Tambahkan data destinasi melalui portal Admin.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Catalog Grid Section (RHYTHM 2: clean 3-column catalog below editorial hero) -->
        <section v-if="remainingDestinations.length > 0" class="py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-8">
                    <div>
                        <h2 class="font-heading font-bold text-2xl sm:text-3xl text-brand-dark">
                            Destinasi lain di katalog
                        </h2>
                        <p class="text-sm text-brand-muted mt-1">
                            Data lokasi, jam operasional, dan estimasi harga dikelola terpusat oleh Admin.
                        </p>
                    </div>
                    <Link
                        href="/destinasi"
                        class="text-sm font-semibold text-brand-primary hover:text-brand-dark underline underline-offset-4 shrink-0"
                    >
                        Buka semua destinasi
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <article
                        v-for="dest in remainingDestinations"
                        :key="dest.id"
                        class="bg-white rounded-xl border border-brand-dark/10 overflow-hidden hover:border-brand-primary/40 transition-colors flex flex-col justify-between"
                    >
                        <div>
                            <div class="relative aspect-[16/10] bg-brand-input overflow-hidden">
                                <img
                                    v-if="getCoverImage(dest)"
                                    :src="getCoverImage(dest)"
                                    :alt="dest.nama"
                                    loading="lazy"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-brand-input/70">
                                    <MapPin class="w-8 h-8 text-brand-dark/25" aria-hidden="true" />
                                </div>
                                <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-md text-xs font-medium bg-white/95 text-brand-dark border border-brand-dark/10">
                                    {{ dest.category?.nama_kategori }}
                                </span>
                            </div>

                            <div class="p-5 space-y-2">
                                <p class="text-xs text-brand-muted flex items-center gap-1">
                                    <MapPin class="w-3.5 h-3.5 text-brand-primary shrink-0" aria-hidden="true" />
                                    {{ dest.kota }}
                                </p>
                                <h3 class="font-heading font-semibold text-lg text-brand-dark leading-snug line-clamp-1">
                                    <Link :href="`/destinasi/${dest.id}`" class="hover:text-brand-primary">
                                        {{ dest.nama }}
                                    </Link>
                                </h3>
                                <p class="text-xs text-brand-muted line-clamp-2 leading-relaxed">
                                    {{ dest.deskripsi }}
                                </p>
                            </div>
                        </div>

                        <div class="px-5 pb-5 pt-3 border-t border-brand-dark/5 flex items-center justify-between gap-3">
                            <div>
                                <template v-if="formatPrice(dest.estimasi_harga)">
                                    <p class="font-bold text-sm text-brand-dark tabular-nums">{{ formatPrice(dest.estimasi_harga) }}</p>
                                    <p class="text-[11px] text-brand-muted">{{ dest.jenis_harga === 'per_orang' ? 'per orang' : 'per kelompok' }}</p>
                                </template>
                                <span v-else class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-coral-light text-coral-text">
                                    Belum dihitung
                                </span>
                            </div>
                            <Link
                                :href="`/destinasi/${dest.id}`"
                                class="px-3.5 py-2 rounded-lg text-xs font-semibold text-brand-dark bg-brand-bg hover:bg-brand-input transition-colors min-h-[40px] inline-flex items-center"
                            >
                                Detail
                            </Link>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Editorial Numbered Workflow Strip on Sand (R-05 & R-14: varied composition, no boxed icon clones) -->
        <section class="py-12 sm:py-16 bg-brand-input/55 border-t border-brand-dark/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
                <div class="max-w-xl mb-8">
                    <h2 class="font-heading font-bold text-2xl sm:text-3xl text-brand-dark">
                        Alur menyusun perjalanan di NUSA WANDER
                    </h2>
                    <p class="text-sm text-brand-muted mt-1.5">
                        Empat tahapan dari memilih katalog hingga memantau sisa anggaran sebelum berangkat.
                    </p>
                </div>

                <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    <li
                        v-for="step in planningSteps"
                        :key="step.num"
                        class="pt-4 border-t-2 border-brand-dark/20 space-y-2"
                    >
                        <span class="text-xs font-bold text-coral-text tabular-nums">{{ step.num }}</span>
                        <h3 class="font-heading font-semibold text-base text-brand-dark">{{ step.title }}</h3>
                        <p class="text-xs sm:text-sm text-brand-muted leading-relaxed">{{ step.desc }}</p>
                    </li>
                </ol>
            </div>
        </section>
    </PublicLayout>
</template>
