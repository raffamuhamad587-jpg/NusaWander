<script setup>
import { ref, computed } from 'vue'
import { Link, Head, useForm, usePage, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { MapPin, Clock, Tag, ChevronRight, Share2, Heart, ArrowLeft, Star, Trash2, Send } from 'lucide-vue-next'

const props = defineProps({
    destination: {
        type: Object,
        required: true,
    },
    relatedDestinations: {
        type: Array,
        default: () => [],
    },
    canReview: {
        type: Boolean,
        default: false,
    },
})

const page = usePage()
const activeImageIndex = ref(0)

const reviewForm = useForm({
    rating: 0,
    komentar: '',
})

const hoverRating = ref(0)

const reviews = computed(() => props.destination.reviews ?? [])

const averageRating = computed(() => {
    if (reviews.value.length === 0) return 0
    const total = reviews.value.reduce((sum, r) => sum + r.rating, 0)
    return (total / reviews.value.length).toFixed(1)
})

function submitReview() {
    if (reviewForm.rating < 1) {
        reviewForm.setError('rating', 'Pilih rating bintang terlebih dahulu.')
        return
    }
    reviewForm.post(route('reviews.store', props.destination.id), {
        preserveScroll: true,
        onSuccess: () => reviewForm.reset(),
    })
}

function deleteReview(review) {
    if (confirm('Hapus ulasan Anda?')) {
        router.delete(route('reviews.destroy', review.id), { preserveScroll: true })
    }
}

const images = computed(() => {
    if (props.destination.images && props.destination.images.length > 0) {
        return props.destination.images.sort((a, b) => a.urutan_tampil - b.urutan_tampil)
    }
    return []
})

function formatPrice(price) {
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}
</script>

<template>
    <Head :title="`${destination.nama} - NUSA WANDER`" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
            <!-- Back & Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-[#404945]/70 mb-6">
                <Link href="/destinasi" class="inline-flex items-center gap-1 hover:text-[#003527] transition-colors">
                    <ArrowLeft class="w-4 h-4" /> Kembali ke Katalog
                </Link>
                <span>/</span>
                <span class="text-[#003527] font-medium truncate max-w-xs">{{ destination.nama }}</span>
            </div>

            <!-- Header Title -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full mb-3">
                        {{ destination.category?.nama_kategori }}
                    </span>
                    <h1 class="font-heading font-bold text-3xl md:text-4xl lg:text-5xl text-[#003527] tracking-tight">
                        {{ destination.nama }}
                    </h1>
                    <p class="text-base text-[#404945]/80 flex items-center gap-2 mt-2">
                        <MapPin class="w-4 h-4 text-emerald-600 shrink-0" />
                        {{ destination.kota }}, Indonesia
                    </p>
                </div>

                <!-- CTA Button -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="`/trips/create?destination_id=${destination.id}`"
                        class="inline-flex items-center gap-2 px-6 py-3.5 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl shadow-lg shadow-emerald-200 transition-all"
                    >
                        + Rencanakan ke Trip
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="inline-flex items-center gap-2 px-6 py-3.5 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl shadow-lg shadow-emerald-200 transition-all"
                    >
                        Masuk untuk Merencanakan
                    </Link>
                </div>
            </div>

            <!-- Gallery Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
                <!-- Main Featured Photo -->
                <div class="lg:col-span-2 aspect-[16/10] bg-emerald-50 rounded-3xl overflow-hidden border border-emerald-100 relative shadow-sm">
                    <img
                        v-if="images.length > 0"
                        :src="images[activeImageIndex]?.url_gambar"
                        :alt="destination.nama"
                        class="w-full h-full object-cover transition-all duration-300"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <MapPin class="w-16 h-16 text-emerald-200" />
                    </div>
                </div>

                <!-- Thumbnail Thumbnails -->
                <div class="space-y-4">
                    <h3 class="font-heading font-semibold text-[#003527] text-sm uppercase tracking-wider">
                        Foto Galeri ({{ images.length }})
                    </h3>
                    <div class="grid grid-cols-2 lg:grid-cols-1 gap-3 max-h-[460px] overflow-y-auto pr-1">
                        <div
                            v-for="(img, idx) in images"
                            :key="img.id"
                            @click="activeImageIndex = idx"
                            :class="[
                                'aspect-video rounded-2xl overflow-hidden border-2 cursor-pointer transition-all',
                                activeImageIndex === idx ? 'border-emerald-600 shadow-md scale-[0.98]' : 'border-transparent hover:border-emerald-200',
                            ]"
                        >
                            <img :src="img.url_gambar" :alt="img.keterangan || destination.nama" class="w-full h-full object-cover" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid: Details vs Sticky Price Card -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Left Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- About -->
                    <section class="bg-white rounded-3xl p-8 border border-emerald-100 shadow-sm space-y-4">
                        <h2 class="font-heading font-bold text-2xl text-[#003527]">Tentang Destinasi</h2>
                        <p class="text-[#404945] leading-relaxed whitespace-pre-line text-base">
                            {{ destination.deskripsi }}
                        </p>
                    </section>

                    <!-- Location & Hours -->
                    <section class="bg-white rounded-3xl p-8 border border-emerald-100 shadow-sm space-y-6">
                        <h2 class="font-heading font-bold text-2xl text-[#003527]">Informasi Kunjungan</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                    <Clock class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-[#003527] text-sm">Jam Operasional</h4>
                                    <p class="text-sm text-[#404945]/80 mt-0.5">{{ destination.jam_operasional || 'Tidak ada keterangan resmi' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                    <MapPin class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-[#003527] text-sm">Alamat Lengkap</h4>
                                    <p class="text-sm text-[#404945]/80 mt-0.5 leading-relaxed">{{ destination.alamat }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- OpenStreetMap Preview iframe if coords available -->
                        <div v-if="destination.latitude && destination.longitude" class="mt-4 pt-6 border-t border-emerald-100">
                            <h4 class="font-semibold text-[#003527] text-sm mb-3">Peta Lokasi</h4>
                            <div class="h-64 rounded-2xl overflow-hidden border border-emerald-100 shadow-inner">
                                <iframe
                                    width="100%"
                                    height="100%"
                                    frameborder="0"
                                    scrolling="no"
                                    marginheight="0"
                                    marginwidth="0"
                                    :src="`https://www.openstreetmap.org/export/embed.html?bbox=${Number(destination.longitude) - 0.01}%2C${Number(destination.latitude) - 0.01}%2C${Number(destination.longitude) + 0.01}%2C${Number(destination.latitude) + 0.01}&layer=mapnik&marker=${destination.latitude}%2C${destination.longitude}`"
                                ></iframe>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right Sticky Card -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-8 border border-emerald-100 shadow-sm sticky top-28 space-y-6">
                        <div>
                            <span class="text-xs uppercase font-semibold tracking-wider text-[#404945]/60">Estimasi Tiket / Biaya</span>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="font-heading font-bold text-3xl text-[#003527]">{{ formatPrice(destination.estimasi_harga) }}</span>
                                <span class="text-sm text-[#404945]/70">/ {{ destination.jenis_harga === 'per_orang' ? 'orang' : 'kelompok' }}</span>
                            </div>
                        </div>

                        <hr class="border-emerald-100" />

                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-[#404945]/70">Kategori</span>
                                <span class="font-semibold text-[#003527]">{{ destination.category?.nama_kategori }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-[#404945]/70">Kota / Wilayah</span>
                                <span class="font-semibold text-[#003527]">{{ destination.kota }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-[#404945]/70">Status Kunjungan</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    Aktif &amp; Terbuka
                                </span>
                            </div>
                        </div>

                        <Link
                            v-if="$page.props.auth?.user"
                            :href="`/trips/create?destination_id=${destination.id}`"
                            class="block w-full py-4 text-center font-semibold text-white bg-[#174c3c] hover:bg-[#003527] rounded-2xl shadow-lg shadow-emerald-200 transition-all cursor-pointer"
                        >
                            Tambahkan ke Rencana Perjalanan
                        </Link>
                        <Link
                            v-else
                            :href="route('register')"
                            class="block w-full py-4 text-center font-semibold text-white bg-[#174c3c] hover:bg-[#003527] rounded-2xl shadow-lg shadow-emerald-200 transition-all cursor-pointer"
                        >
                            Daftar untuk Menyusun Rencana
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="mt-20 pt-12 border-t border-emerald-100 space-y-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm px-5 py-4 text-center">
                            <p class="font-heading font-bold text-3xl text-[#003527] leading-none">{{ averageRating }}</p>
                            <div class="flex items-center justify-center gap-0.5 mt-1.5">
                                <Star
                                    v-for="n in 5"
                                    :key="n"
                                    :class="[
                                        'w-3.5 h-3.5',
                                        n <= Math.round(averageRating) ? 'fill-amber-400 text-amber-400' : 'text-gray-200',
                                    ]"
                                />
                            </div>
                            <p class="text-[10px] text-[#404945]/60 mt-1">{{ reviews.length }} ulasan</p>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-2xl text-[#003527]">Ulasan Wisatawan</h3>
                            <p class="text-sm text-[#404945]/70 mt-0.5">Pengalaman nyata dari sesama penjelajah.</p>
                        </div>
                    </div>
                </div>

                <!-- Review Form -->
                <div v-if="canReview" class="bg-white rounded-3xl p-8 border border-emerald-100 shadow-sm">
                    <h4 class="font-heading font-bold text-[#003527] mb-4">Tulis Ulasan Anda</h4>

                    <div class="mb-5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                            Rating Bintang <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-1.5">
                            <button
                                v-for="n in 5"
                                :key="n"
                                type="button"
                                @click="reviewForm.rating = n"
                                @mouseenter="hoverRating = n"
                                @mouseleave="hoverRating = 0"
                                class="transition-transform hover:scale-110 cursor-pointer"
                            >
                                <Star
                                    :class="[
                                        'w-8 h-8 transition-colors',
                                        (hoverRating ? n <= hoverRating : n <= reviewForm.rating)
                                            ? 'fill-amber-400 text-amber-400'
                                            : 'text-gray-200',
                                    ]"
                                />
                            </button>
                            <span v-if="reviewForm.rating > 0" class="ml-2 text-sm font-semibold text-[#003527]">
                                {{ reviewForm.rating }}/5
                            </span>
                        </div>
                        <p v-if="reviewForm.errors.rating" class="text-xs text-rose-500 mt-1.5">{{ reviewForm.errors.rating }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                            Komentar (Opsional)
                        </label>
                        <textarea
                            v-model="reviewForm.komentar"
                            rows="3"
                            placeholder="Ceritakan pengalaman kunjungan Anda..."
                            class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all resize-none"
                        ></textarea>
                        <p v-if="reviewForm.errors.komentar" class="text-xs text-rose-500 mt-1.5">{{ reviewForm.errors.komentar }}</p>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button
                            @click="submitReview"
                            :disabled="reviewForm.processing"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white text-sm font-semibold rounded-2xl shadow-lg shadow-emerald-200 transition-all disabled:opacity-50 cursor-pointer"
                        >
                            <Send class="w-4 h-4" />
                            {{ reviewForm.processing ? 'Mengirim...' : 'Kirim Ulasan' }}
                        </button>
                    </div>
                </div>

                <div v-else-if="!$page.props.auth?.user" class="bg-white rounded-3xl p-8 border border-emerald-100 shadow-sm text-center">
                    <p class="text-sm text-[#404945]/70 mb-4">
                        Masuk dengan akun Anda untuk memberikan ulasan dan rating destinasi.
                    </p>
                    <Link
                        :href="route('login')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white text-sm font-semibold rounded-2xl transition-all"
                    >
                        Masuk untuk Membagikan Ulasan
                    </Link>
                </div>

                <!-- Reviews List -->
                <div class="space-y-4">
                    <article
                        v-for="review in reviews"
                        :key="review.id"
                        class="bg-white rounded-2xl border border-emerald-100 p-6 shadow-sm"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#003527] text-white flex items-center justify-center text-xs font-bold">
                                    {{ review.user?.name?.charAt(0)?.toUpperCase() || 'A' }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[#003527]">{{ review.user?.name }}</p>
                                    <div class="flex items-center gap-0.5 mt-0.5">
                                        <Star
                                            v-for="n in 5"
                                            :key="n"
                                            :class="[
                                                'w-3.5 h-3.5',
                                                n <= review.rating ? 'fill-amber-400 text-amber-400' : 'text-gray-200',
                                            ]"
                                        />
                                    </div>
                                </div>
                            </div>

                            <button
                                v-if="review.user_id === $page.props.auth?.user?.id || $page.props.auth?.user?.role === 'admin'"
                                @click="deleteReview(review)"
                                class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                title="Hapus Ulasan"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>

                        <p v-if="review.komentar" class="text-sm text-[#404945] leading-relaxed mt-3">
                            {{ review.komentar }}
                        </p>

                        <p class="text-[11px] text-[#404945]/50 mt-3">
                            Dipublikasikan pada {{ new Date(review.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                        </p>
                    </article>

                    <div v-if="reviews.length === 0" class="text-center py-12 bg-white rounded-2xl border border-emerald-100">
                        <Star class="w-12 h-12 text-gray-200 mx-auto mb-3" />
                        <h4 class="text-base font-semibold text-[#003527]">Belum Ada Ulasan</h4>
                        <p class="text-sm text-[#404945]/70 mt-1">Jadilah orang pertama yang memberikan ulasan untuk destinasi ini.</p>
                    </div>
                </div>
            </div>

            <!-- Related Section -->
            <div v-if="relatedDestinations.length > 0" class="mt-20 pt-12 border-t border-emerald-100">
                <h3 class="font-heading font-bold text-2xl text-[#003527] mb-8">
                    Destinasi Terkait Lainnya
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Link
                        v-for="rel in relatedDestinations"
                        :key="rel.id"
                        :href="`/destinasi/${rel.id}`"
                        class="group bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                    >
                        <div class="aspect-video bg-emerald-50 overflow-hidden relative">
                            <img
                                v-if="rel.images && rel.images.length > 0"
                                :src="rel.images[0].url_gambar"
                                :alt="rel.nama"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <MapPin class="w-8 h-8 text-emerald-200" />
                            </div>
                        </div>
                        <div class="p-4 space-y-1">
                            <h4 class="font-semibold text-[#003527] text-sm line-clamp-1 group-hover:text-[#174c3c] transition-colors">
                                {{ rel.nama }}
                            </h4>
                            <p class="text-xs text-[#404945]/70">{{ rel.kota }}</p>
                            <p class="text-xs font-bold text-[#174c3c] pt-2">{{ formatPrice(rel.estimasi_harga) }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>