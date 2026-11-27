<script setup>
import { ref, computed } from 'vue'
import { Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { MapPin, Clock, ArrowLeft } from 'lucide-vue-next'

const props = defineProps({
    destination: {
        type: Object,
        required: true,
    },
    relatedDestinations: {
        type: Array,
        default: () => [],
    },
})

const activeImageIndex = ref(0)

const images = computed(() => {
    if (props.destination.images && props.destination.images.length > 0) {
        return [...props.destination.images].sort((a, b) => a.urutan_tampil - b.urutan_tampil)
    }
    return []
})

function formatPrice(price) {
    if (price === null || price === undefined || price === '') return null
    return 'Rp ' + Number(price).toLocaleString('id-ID')
}
</script>

<template>
    <Head :title="`${destination.nama} | NUSA WANDER`" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 sm:py-10">
            <!-- Breadcrumb -->
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs sm:text-sm text-brand-muted mb-6">
                <Link href="/destinasi" class="inline-flex items-center gap-1.5 hover:text-brand-dark min-h-[40px]">
                    <ArrowLeft class="w-4 h-4" aria-hidden="true" /> Kembali ke Daftar Destinasi
                </Link>
                <span aria-hidden="true">/</span>
                <span class="text-brand-dark font-medium truncate">{{ destination.nama }}</span>
            </nav>

            <!-- Header Title -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <span class="inline-block px-2.5 py-1 bg-brand-input text-brand-dark text-xs font-semibold rounded-md mb-2.5">
                        {{ destination.category?.nama_kategori }}
                    </span>
                    <h1 class="font-heading font-bold text-3xl sm:text-4xl text-brand-dark tracking-tight">
                        {{ destination.nama }}
                    </h1>
                    <p class="text-sm sm:text-base text-brand-muted flex items-center gap-1.5 mt-2">
                        <MapPin class="w-4 h-4 text-brand-primary shrink-0" aria-hidden="true" />
                        {{ destination.kota }}, Indonesia
                    </p>
                </div>

                <Link
                    :href="$page.props.auth?.user ? `/trips/create?destination_id=${destination.id}` : '/login'"
                    class="inline-flex items-center justify-center px-6 py-3 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors shrink-0 min-h-[44px]"
                >
                    {{ $page.props.auth?.user ? 'Masukkan ke Rencana Perjalanan' : 'Masuk untuk Merencanakan' }}
                </Link>
            </div>

            <!-- Gallery Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-10">
                <div class="lg:col-span-2 aspect-[16/10] bg-brand-input rounded-xl overflow-hidden border border-brand-dark/10 relative">
                    <img
                        v-if="images.length > 0"
                        :src="images[activeImageIndex]?.url_gambar"
                        :alt="images[activeImageIndex]?.keterangan || destination.nama"
                        fetchpriority="high"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <MapPin class="w-12 h-12 text-brand-dark/25" aria-hidden="true" />
                    </div>
                    <p
                        v-if="images[activeImageIndex]?.keterangan"
                        class="absolute bottom-3 left-3 right-3 bg-brand-dark/80 text-white text-xs px-3.5 py-2 rounded-lg"
                    >
                        {{ images[activeImageIndex].keterangan }}
                    </p>
                </div>

                <div class="space-y-3">
                    <h2 class="font-heading font-semibold text-sm text-brand-dark">
                        Galeri foto ({{ images.length }})
                    </h2>
                    <div v-if="images.length > 0" class="grid grid-cols-2 lg:grid-cols-1 gap-2.5 max-h-[420px] overflow-y-auto pr-1">
                        <button
                            v-for="(img, idx) in images"
                            :key="img.id"
                            type="button"
                            @click="activeImageIndex = idx"
                            :aria-label="`Tampilkan foto ${idx + 1}: ${img.keterangan || destination.nama}`"
                            :class="[
                                'aspect-video rounded-lg overflow-hidden border-2 transition-colors text-left',
                                activeImageIndex === idx ? 'border-brand-primary' : 'border-transparent hover:border-brand-dark/25',
                            ]"
                        >
                            <img :src="img.url_gambar" :alt="img.keterangan || destination.nama" loading="lazy" class="w-full h-full object-cover" />
                        </button>
                    </div>
                    <p v-else class="text-xs text-brand-muted bg-white rounded-xl border border-brand-dark/10 p-4">
                        Belum ada foto galeri yang diunggah untuk destinasi ini.
                    </p>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <section class="bg-white rounded-xl p-6 sm:p-7 border border-brand-dark/10 space-y-3">
                        <h2 class="font-heading font-bold text-xl text-brand-dark">Tentang destinasi</h2>
                        <p class="text-sm sm:text-base text-brand-text leading-relaxed whitespace-pre-line">
                            {{ destination.deskripsi }}
                        </p>
                    </section>

                    <section class="bg-white rounded-xl p-6 sm:p-7 border border-brand-dark/10 space-y-5">
                        <h2 class="font-heading font-bold text-xl text-brand-dark">Informasi kunjungan</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-lg bg-brand-bg text-brand-primary flex items-center justify-center shrink-0" aria-hidden="true">
                                    <Clock class="w-4 h-4" />
                                </span>
                                <div>
                                    <h3 class="font-semibold text-brand-dark text-sm">Jam operasional</h3>
                                    <p class="text-sm text-brand-muted mt-0.5">{{ destination.jam_operasional || 'Belum ada keterangan resmi' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-lg bg-brand-bg text-brand-primary flex items-center justify-center shrink-0" aria-hidden="true">
                                    <MapPin class="w-4 h-4" />
                                </span>
                                <div>
                                    <h3 class="font-semibold text-brand-dark text-sm">Alamat lengkap</h3>
                                    <p class="text-sm text-brand-muted mt-0.5 leading-relaxed">{{ destination.alamat }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="destination.latitude && destination.longitude" class="pt-5 border-t border-brand-dark/10">
                            <h3 class="font-semibold text-brand-dark text-sm mb-3">Peta lokasi</h3>
                            <div class="aspect-[16/9] rounded-xl overflow-hidden border border-brand-dark/10">
                                <iframe
                                    title="Peta lokasi destinasi"
                                    width="100%"
                                    height="100%"
                                    frameborder="0"
                                    scrolling="no"
                                    loading="lazy"
                                    :src="`https://www.openstreetmap.org/export/embed.html?bbox=${Number(destination.longitude) - 0.01}%2C${Number(destination.latitude) - 0.01}%2C${Number(destination.longitude) + 0.01}%2C${Number(destination.latitude) + 0.01}&layer=mapnik&marker=${destination.latitude}%2C${destination.longitude}`"
                                ></iframe>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right Sticky Price Summary -->
                <div>
                    <div class="bg-white rounded-xl p-6 border border-brand-dark/10 shadow-sm sticky top-24 space-y-5">
                        <div>
                            <p class="text-xs font-semibold text-brand-muted">Estimasi harga</p>
                            <div v-if="formatPrice(destination.estimasi_harga)" class="mt-1.5 flex items-baseline gap-2">
                                <span class="font-heading font-bold text-3xl text-brand-dark tabular-nums">{{ formatPrice(destination.estimasi_harga) }}</span>
                                <span class="text-sm text-brand-muted">/ {{ destination.jenis_harga === 'per_orang' ? 'orang' : 'kelompok' }}</span>
                            </div>
                            <div v-else class="mt-2">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-semibold bg-coral-light text-coral-text">
                                    Belum dihitung
                                </span>
                                <p class="text-xs text-brand-muted mt-2">
                                    Data estimasi harga belum tersedia dari Admin dan tidak dianggap gratis.
                                </p>
                            </div>
                            <p class="text-[11px] text-brand-muted mt-2">
                                Estimasi dikelola Admin, bukan tarif real-time.
                            </p>
                        </div>

                        <hr class="border-brand-dark/10" />

                        <dl class="space-y-2.5 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-brand-muted">Kategori</dt>
                                <dd class="font-semibold text-brand-dark">{{ destination.category?.nama_kategori }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-brand-muted">Wilayah</dt>
                                <dd class="font-semibold text-brand-dark">{{ destination.kota }}</dd>
                            </div>
                        </dl>

                        <Link
                            :href="$page.props.auth?.user ? `/trips/create?destination_id=${destination.id}` : '/register'"
                            class="flex items-center justify-center w-full py-3 px-4 font-semibold text-sm text-white bg-brand-primary hover:bg-brand-primaryHover rounded-lg transition-colors min-h-[44px]"
                        >
                            {{ $page.props.auth?.user ? 'Buat Rencana Perjalanan' : 'Daftar untuk Menyusun Rencana' }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Related Destinations -->
            <section v-if="relatedDestinations.length > 0" class="mt-14 pt-10 border-t border-brand-dark/10">
                <h2 class="font-heading font-bold text-xl sm:text-2xl text-brand-dark mb-6">
                    Destinasi serupa lainnya
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <Link
                        v-for="rel in relatedDestinations"
                        :key="rel.id"
                        :href="`/destinasi/${rel.id}`"
                        class="bg-white rounded-xl border border-brand-dark/10 overflow-hidden hover:border-brand-primary/40 transition-colors"
                    >
                        <div class="aspect-video bg-brand-input/70 overflow-hidden">
                            <img
                                v-if="rel.images && rel.images.length > 0"
                                :src="rel.images[0].url_gambar"
                                :alt="rel.nama"
                                loading="lazy"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <MapPin class="w-7 h-7 text-brand-dark/25" aria-hidden="true" />
                            </div>
                        </div>
                        <div class="p-4 space-y-1">
                            <h3 class="font-semibold text-brand-dark text-sm line-clamp-1">
                                {{ rel.nama }}
                            </h3>
                            <p class="text-xs text-brand-muted">{{ rel.kota }}</p>
                            <p class="text-xs font-bold text-brand-primary pt-1 tabular-nums">
                                {{ formatPrice(rel.estimasi_harga) || 'Belum dihitung' }}
                            </p>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
