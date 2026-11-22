<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    Compass, MapPin, Calendar, Users, Wallet, ArrowRight,
    Search, Heart, Sliders, CheckCircle2, ChevronRight,
    Calculator, Sparkles, ExternalLink, Menu, X, ArrowUpRight
} from 'lucide-vue-next'

const props = defineProps({
    featuredDestinations: {
        type: Array,
        default: () => []
    },
    categories: {
        type: Array,
        default: () => []
    }
})

// Search Form State
const searchQuery = ref('')
const searchDate = ref('2026-12-04')
const selectedCategory = ref('')
const selectedTravelers = ref('2')
const autocompleteOpen = ref(false)

// Active category filter
const activeCat = ref('all')

// Wishlist store
const wishlist = ref([])

// Mockup interactive tab
const mockupTab = ref('budget')

// Budget simulation state
const simDays = ref(3)
const simPersons = ref(2)
const simTier = ref('standar')
const simTotal = ref(1450000)
const simCap = ref(1800000)

// Static curated destinations matching the reference screenshot exactly
const sampleDestinations = [
    {
        id: 1,
        nama: 'Candi Borobudur',
        lokasi: 'Magelang, Jawa Tengah',
        kategori: 'Wisata Budaya',
        catKey: 'budaya',
        deskripsi: 'Mahakarya arsitektur candi Buddha terbesar di dunia yang dikelilingi panorama fajar dramatis dan relief bersejarah.',
        harga: 50000,
        jam: '06:30 - 16:30 WIB',
        image: 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 2,
        nama: 'Pantai Parangtritis',
        lokasi: 'Bantul, D.I. Yogyakarta',
        kategori: 'Pantai & Bahari',
        catKey: 'pantai',
        deskripsi: 'Garis pantai berpasir hitam yang ikonik dengan deburan ombak megah serta panorama golden sunset yang memukau.',
        harga: 15000,
        jam: 'Buka 24 Jam',
        image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 3,
        nama: 'HeHa Sky View',
        lokasi: 'Gunungkidul, D.I. Yogyakarta',
        kategori: 'Wisata Modern',
        catKey: 'modern',
        deskripsi: 'Spot panorama favorit di perbukitan dengan instalasi foto modern, pemandangan lampu kota malam hari, dan kafe santai.',
        harga: 30000,
        jam: '10:00 - 21:00 WIB',
        image: 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 4,
        nama: 'Taman Sari',
        lokasi: 'Kota Yogyakarta, DIY',
        kategori: 'Sejarah & Budaya',
        catKey: 'budaya',
        deskripsi: 'Situs bekas pemandian kerajaan yang kental akan nuansa arsitektur kuno Portugis-Jawa dan lorong bawah tanah Sumur Gumuling.',
        harga: 15000,
        jam: '09:00 - 15:00 WIB',
        image: 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=80'
    }
]

// Modal states
const detailModal = ref(false)
const selectedDetail = ref(null)
const calcModal = ref(false)
const aiModal = ref(false)
const aiInput = ref('')
const aiResult = ref(null)
const toastMessage = ref('')
const showToast = ref(false)

function triggerToast(msg) {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
    }, 3200)
}

function toggleWishlist(name) {
    const idx = wishlist.value.indexOf(name)
    if (idx === -1) {
        wishlist.value.push(name)
        triggerToast(`❤️ ${name} disimpan ke daftar impian`)
    } else {
        wishlist.value.splice(idx, 1)
        triggerToast(`Dihapus dari daftar impian`)
    }
}

function openDetail(dest) {
    selectedDetail.value = dest
    detailModal.value = true
}

function filterByCat(cat) {
    activeCat.value = cat
}

const filteredDestinations = computed(() => {
    if (activeCat.value === 'all') return sampleDestinations
    return sampleDestinations.filter(d => d.catKey === activeCat.value)
})

function submitSearch() {
    triggerToast("🔍 Menelusuri rekomendasi destinasi...")
    const el = document.getElementById('destinasi-section')
    if (el) el.scrollIntoView({ behavior: 'smooth' })
}

function recalculateBudget() {
    let hotelRate = simTier.value === 'hemat' ? 220000 : (simTier.value === 'standar' ? 320000 : 750000)
    let foodPerPerson = simTier.value === 'hemat' ? 60000 : (simTier.value === 'standar' ? 90000 : 180000)
    let transportDaily = simPersons.value <= 2 ? 150000 : 250000
    let ticketPerPerson = 55000

    let totalHotel = hotelRate * Math.max(1, simDays.value - 1) * Math.ceil(simPersons.value / 2)
    let totalFood = foodPerPerson * simDays.value * simPersons.value
    let totalTransport = transportDaily * simDays.value
    let totalTickets = ticketPerPerson * simPersons.value * Math.min(simDays.value, 3)

    simTotal.value = totalHotel + totalFood + totalTransport + totalTickets
    simCap.value = Math.round(simTotal.value * 1.25 / 100000) * 100000
}

function applyBudgetToMockup() {
    calcModal.value = false
    triggerToast("✨ Parameter estimasi berhasil diperbarui pada preview planner!")
}

function askAI() {
    if (!aiInput.value.trim()) return
    aiResult.value = {
        rute: 'Eksplorasi Candi Borobudur & Sunset Parangtritis',
        durasi: '2 Hari 1 Malam',
        biaya: 'Rp480.000 / orang',
        notes: `Rekomendasi disesuaikan dengan permintaan Anda: "${aiInput.value}"`
    }
    triggerToast("✨ AI berhasil merumuskan rekomendasi perjalanan!")
}

// Mobile navigation
const mobileMenuOpen = ref(false)
</script>

<template>
    <Head title="NUSA WANDER — Jelajahi Keindahan Indonesia & Smart Trip Planner" />

    <div class="min-h-screen bg-[#FFF8F1] font-sans text-[#1F2933] flex flex-col antialiased">
        
        <!-- SECTION 01 — NAVBAR -->
        <header class="fixed top-0 inset-x-0 z-50 bg-[#FFFEFA]/95 backdrop-blur-md border-b border-[#E7E5DF]/80">
            <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 group">
                    <span class="w-10 h-10 rounded-xl bg-[#00253E] text-white flex items-center justify-center shadow-md shadow-[#173B57]/20 group-hover:bg-[#278C83] transition-colors">
                        <Compass class="w-5 h-5" />
                    </span>
                    <span class="font-heading font-bold text-xl tracking-tight text-[#00253E]">
                        NUSA WANDER
                    </span>
                </Link>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-[#687784]">
                    <a href="#hero" class="text-[#278C83] font-semibold relative after:content-[''] after:absolute after:-bottom-1.5 after:left-0 after:w-full after:h-0.5 after:bg-[#278C83] after:rounded-full">
                        Beranda
                    </a>
                    <a href="#destinasi-section" class="hover:text-[#00253E] transition-colors">Destinasi</a>
                    <a href="#how-it-works-section" class="hover:text-[#00253E] transition-colors">Rencanakan Perjalanan</a>
                    <a href="#trip-planner-section" class="hover:text-[#00253E] transition-colors">Fitur & Budget</a>
                    <a href="#footer-section" class="hover:text-[#00253E] transition-colors">Tentang Kami</a>
                </nav>

                <!-- Right Side Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <button @click="submitSearch" class="p-2 text-[#687784] hover:text-[#00253E] rounded-full hover:bg-[#F4EBDD]/50 transition-colors">
                        <Search class="w-5 h-5" />
                    </button>
                    <div class="h-5 w-px bg-[#E7E5DF] mx-1"></div>
                    
                    <Link :href="route('login')" class="text-sm font-semibold text-[#00253E] hover:text-[#278C83] px-3 py-2 transition-colors">
                        Masuk
                    </Link>
                    <a href="#trip-planner-section" class="inline-flex items-center gap-2 bg-[#278C83] hover:bg-[#1E6D66] text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-all">
                        <span>Mulai Perjalanan</span>
                        <ArrowRight class="w-4 h-4" />
                    </a>
                </div>

                <!-- Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2.5 rounded-xl text-[#00253E] hover:bg-[#F4EBDD]/60">
                    <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                    <X v-else class="w-6 h-6" />
                </button>
            </div>

            <!-- Mobile Drawer -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-[#FFFEFA] border-b border-[#E7E5DF] px-6 py-5 space-y-4 shadow-xl">
                <a href="#hero" @click="mobileMenuOpen = false" class="block text-base font-semibold text-[#278C83] py-1">Beranda</a>
                <a href="#destinasi-section" @click="mobileMenuOpen = false" class="block text-base font-medium text-[#00253E] py-1">Destinasi</a>
                <a href="#how-it-works-section" @click="mobileMenuOpen = false" class="block text-base font-medium text-[#00253E] py-1">Rencanakan Perjalanan</a>
                <a href="#trip-planner-section" @click="mobileMenuOpen = false" class="block text-base font-medium text-[#00253E] py-1">Smart Trip & Budget Planner</a>
                <div class="pt-2 flex gap-3">
                    <Link :href="route('login')" class="flex-1 text-center py-2 text-sm font-semibold text-[#00253E] border border-[#00253E]/20 rounded-xl">Masuk</Link>
                    <a href="#trip-planner-section" @click="mobileMenuOpen = false" class="flex-1 text-center py-2 text-sm font-semibold text-white bg-[#278C83] rounded-xl shadow">Mulai</a>
                </div>
            </div>
        </header>

        <main class="flex-1 pt-20">

            <!-- SECTION 02 — HERO SECTION -->
            <section id="hero" class="relative w-full min-h-[580px] lg:min-h-[620px] flex items-center bg-[#00253E] overflow-hidden">
                <img 
                    src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=2000&q=85" 
                    alt="Panorama Alam Indonesia" 
                    class="absolute inset-0 w-full h-full object-cover object-center select-none"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#00253E]/95 via-[#00253E]/75 md:to-transparent to-[#00253E]/50"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#00253E]/80 via-transparent to-transparent"></div>

                <div class="relative max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 w-full">
                    <div class="max-w-2xl lg:max-w-3xl">
                        
                        <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-semibold tracking-wide uppercase mb-6">
                            <span class="w-2 h-2 rounded-full bg-[#2CB7A6] animate-pulse"></span>
                            <span>Eksplorasi Autentik Seluruh Domestik Nusantara</span>
                        </div>

                        <p class="text-xs sm:text-sm font-bold tracking-widest text-[#2CB7A6] uppercase mb-3">
                            NUSA WANDER • PARIWISATA CERDAS NUSANTARA
                        </p>

                        <h1 class="font-heading text-4xl sm:text-5xl lg:text-[56px] font-bold text-white leading-[1.12] tracking-tight mb-5">
                            Jelajahi Keindahan Indonesia,<br class="hidden sm:inline">
                            Ciptakan Cerita Perjalananmu.
                        </h1>

                        <p class="text-base sm:text-lg text-white/90 font-normal leading-relaxed max-w-xl mb-8">
                            Temukan destinasi wisata menakjubkan, rencanakan perjalanan impian, dan nikmati pengalaman liburan yang terencana dengan estimasi anggaran terperinci.
                        </p>

                        <div class="flex flex-wrap items-center gap-4 mb-12">
                            <a href="#destinasi-section" class="inline-flex items-center gap-2.5 bg-[#278C83] hover:bg-[#1E6D66] text-white text-sm sm:text-base font-semibold px-6 sm:px-7 py-3.5 rounded-xl shadow-lg transition-all">
                                <Compass class="w-5 h-5" />
                                <span>Temukan Destinasi</span>
                            </a>

                            <a href="#trip-planner-section" class="inline-flex items-center gap-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white text-sm sm:text-base font-semibold px-6 sm:px-7 py-3.5 rounded-xl border border-white/40 hover:border-white transition-all">
                                <Calendar class="w-5 h-5" />
                                <span>Jelajahi Sekarang</span>
                            </a>
                        </div>

                        <!-- Trust Metrics -->
                        <div class="pt-6 border-t border-white/15 grid grid-cols-2 sm:grid-cols-3 gap-6 text-white/90">
                            <div>
                                <p class="text-xs text-white/60 uppercase font-medium">Katalog Terkurasi</p>
                                <p class="text-sm font-bold text-white">150+ Foto Real & Destinasi</p>
                            </div>
                            <div>
                                <p class="text-xs text-white/60 uppercase font-medium">Estimasi Biaya</p>
                                <p class="text-sm font-bold text-white">100% Transparansi Biaya & Tiket</p>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <p class="text-xs text-white/60 uppercase font-medium">Indeks Rating</p>
                                <p class="text-sm font-bold text-white">4.9 / 5 Kepuasan Pengguna</p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- SECTION 03 — FLOATING SEARCH & TRAVEL PLANNER -->
            <section class="relative z-20 -mt-10 sm:-mt-12 px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto">
                <div class="bg-[#FFFEFA] rounded-2xl sm:rounded-3xl shadow-xl border border-[#E7E5DF] p-4 sm:p-6 lg:p-7">
                    <form @submit.prevent="submitSearch" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        
                        <div class="md:col-span-4 relative">
                            <label class="block text-[11px] font-bold text-[#00253E] uppercase tracking-wider mb-1.5">
                                PILIH DESTINASI WISATA
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#278C83]">
                                    <MapPin class="w-4 h-4" />
                                </span>
                                <input 
                                    v-model="searchQuery" 
                                    @focus="autocompleteOpen = true"
                                    type="text" 
                                    placeholder="Mau eksplorasi ke mana? (mis. Labuan Bajo)" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-[#FFF8F1]/70 border border-[#E7E5DF] rounded-xl text-sm text-[#1F2933] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#278C83]/20 focus:border-[#278C83]"
                                />
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-[#00253E] uppercase tracking-wider mb-1.5">
                                TANGGAL ESTIMASI
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#278C83]">
                                    <Calendar class="w-4 h-4" />
                                </span>
                                <input 
                                    v-model="searchDate" 
                                    type="date" 
                                    class="w-full pl-10 pr-3 py-2.5 bg-[#FFF8F1]/70 border border-[#E7E5DF] rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#278C83]/20 focus:border-[#278C83]"
                                />
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-[#00253E] uppercase tracking-wider mb-1.5">
                                PILIH KATEGORI & WISATAWAN
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#278C83]">
                                    <Users class="w-4 h-4" />
                                </span>
                                <select v-model="selectedCategory" class="w-full pl-10 pr-8 py-2.5 bg-[#FFF8F1]/70 border border-[#E7E5DF] rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#278C83]/20 focus:border-[#278C83]">
                                    <option value="">Semua Kategori (2 Wisatawan)</option>
                                    <option value="budaya">Wisata Budaya & Sejarah</option>
                                    <option value="pantai">Pantai & Bahari</option>
                                    <option value="alam">Pegunungan & Alam</option>
                                    <option value="modern">Wisata Modern</option>
                                </select>
                            </div>
                        </div>

                        <div class="md:col-span-2 pt-2 md:pt-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-[#278C83] hover:bg-[#1E6D66] text-white text-sm font-semibold h-[46px] px-4 rounded-xl shadow-md transition-all">
                                <Search class="w-4 h-4" />
                                <span>Cari Destinasi</span>
                            </button>
                        </div>

                    </form>
                </div>
            </section>

            <!-- SECTION 04 — DESTINATION DISCOVERY -->
            <section id="destinasi-section" class="py-20 lg:py-28 px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 text-xs font-bold text-[#278C83] tracking-widest uppercase mb-2">
                            <MapPin class="w-4 h-4" />
                            <span>LOKASI TERPOPULER NUSANTARA</span>
                        </div>
                        <h2 class="font-heading text-3xl sm:text-4xl lg:text-[42px] font-bold text-[#00253E] leading-tight tracking-tight">
                            Destinasi Pilihan untuk Petualanganmu
                        </h2>
                        <p class="text-[#687784] text-sm sm:text-base mt-3 leading-relaxed">
                            Rekomendasi tempat terbaik yang dirancang khusus untuk memadukan pesona keindahan alam dan efisiensi anggaran Anda.
                        </p>
                    </div>

                    <button @click="filterByCat('all')" class="inline-flex items-center gap-2 text-sm font-semibold text-[#00253E] hover:text-[#278C83] transition-colors group shrink-0">
                        <span>Lihat 150+ Destinasi Lainnya</span>
                        <span class="w-6 h-6 rounded-full bg-[#F4EBDD] flex items-center justify-center group-hover:bg-[#278C83] group-hover:text-white transition-colors">
                            →
                        </span>
                    </button>
                </div>

                <!-- Category Filters -->
                <div class="flex flex-wrap items-center gap-2 mb-10 pb-2">
                    <button 
                        @click="filterByCat('all')" 
                        :class="activeCat === 'all' ? 'bg-[#00253E] text-white border-[#00253E]' : 'bg-[#FFFEFA] text-[#1F2933] border-[#E7E5DF] hover:border-[#278C83]'"
                        class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold border shadow-sm transition-all"
                    >
                        Semua Destinasi
                    </button>
                    <button 
                        @click="filterByCat('budaya')" 
                        :class="activeCat === 'budaya' ? 'bg-[#00253E] text-white border-[#00253E]' : 'bg-[#FFFEFA] text-[#1F2933] border-[#E7E5DF] hover:border-[#278C83]'"
                        class="px-4 py-2 rounded-full text-xs sm:text-sm font-medium border transition-all"
                    >
                        Wisata Budaya & Sejarah
                    </button>
                    <button 
                        @click="filterByCat('pantai')" 
                        :class="activeCat === 'pantai' ? 'bg-[#00253E] text-white border-[#00253E]' : 'bg-[#FFFEFA] text-[#1F2933] border-[#E7E5DF] hover:border-[#278C83]'"
                        class="px-4 py-2 rounded-full text-xs sm:text-sm font-medium border transition-all"
                    >
                        Pantai & Bahari
                    </button>
                    <button 
                        @click="filterByCat('modern')" 
                        :class="activeCat === 'modern' ? 'bg-[#00253E] text-white border-[#00253E]' : 'bg-[#FFFEFA] text-[#1F2933] border-[#E7E5DF] hover:border-[#278C83]'"
                        class="px-4 py-2 rounded-full text-xs sm:text-sm font-medium border transition-all"
                    >
                        Wisata Modern
                    </button>
                </div>

                <!-- 4 Destination Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-7">
                    <article 
                        v-for="dest in filteredDestinations" 
                        :key="dest.id"
                        class="group bg-[#FFFEFA] rounded-2xl border border-[#E7E5DF] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden bg-[#F4EBDD]">
                            <img 
                                :src="dest.image" 
                                :alt="dest.nama" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold bg-white/90 backdrop-blur-md text-[#00253E] shadow-sm">
                                {{ dest.kategori }}
                            </span>
                            <button 
                                @click="toggleWishlist(dest.nama)" 
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur-md flex items-center justify-center text-[#687784] hover:text-[#E98B70] transition-colors shadow-sm"
                            >
                                <Heart 
                                    class="w-4 h-4" 
                                    :fill="wishlist.includes(dest.nama) ? '#E98B70' : 'none'" 
                                    :stroke="wishlist.includes(dest.nama) ? '#E98B70' : 'currentColor'" 
                                />
                            </button>
                        </div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-1.5 text-xs font-medium text-[#687784] mb-1.5">
                                    <MapPin class="w-3.5 h-3.5 text-[#278C83] shrink-0" />
                                    <span>{{ dest.lokasi }}</span>
                                </div>
                                <h3 class="font-heading text-lg font-bold text-[#00253E] group-hover:text-[#278C83] transition-colors">
                                    {{ dest.nama }}
                                </h3>
                                <p class="text-xs text-[#687784] mt-2 line-clamp-2 leading-relaxed">
                                    {{ dest.deskripsi }}
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[#E7E5DF] flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-[#687784] block font-medium">Mulai Dari</span>
                                    <span class="text-sm font-bold text-[#00253E]">Rp{{ Number(dest.harga).toLocaleString('id-ID') }}</span>
                                    <span class="text-[10px] text-[#687784]">/orang</span>
                                </div>
                                <button @click="openDetail(dest)" class="w-8 h-8 rounded-full bg-[#F4EBDD] group-hover:bg-[#278C83] group-hover:text-white flex items-center justify-center text-[#00253E] transition-colors">
                                    <ArrowUpRight class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <!-- SECTION 05 — HOW IT WORKS -->
            <section id="how-it-works-section" class="py-20 lg:py-28 bg-[#F4EBDD]/50 border-y border-[#E7E5DF]/80">
                <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <div class="inline-flex items-center gap-2 text-xs font-bold text-[#278C83] tracking-widest uppercase mb-2">
                            <span>ALUR TERENCANA & EFISIEN</span>
                        </div>
                        <h2 class="font-heading text-3xl sm:text-4xl lg:text-[42px] font-bold text-[#00253E] leading-tight tracking-tight">
                            Merencanakan Perjalanan Jadi Lebih Mudah
                        </h2>
                        <p class="text-[#687784] text-sm sm:text-base mt-3 leading-relaxed">
                            Empat langkah ringkas membantu semua perjalanan yang mudah hingga mengoptimalkan biaya perjalanan nyata yang terduga.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-[#FFFEFA] rounded-2xl p-6 sm:p-7 border border-[#E7E5DF] shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-5">
                                    <span class="w-12 h-12 rounded-xl bg-[#E6F4F2] text-[#278C83] flex items-center justify-center">
                                        <Compass class="w-6 h-6" />
                                    </span>
                                    <span class="font-heading text-2xl font-bold text-[#E7E5DF]">01</span>
                                </div>
                                <h3 class="font-heading text-xl font-bold text-[#00253E] mb-2.5">Temukan Destinasi</h3>
                                <p class="text-[#687784] text-xs sm:text-sm leading-relaxed mb-6">
                                    Jelajahi lokasi tempat wisata nusantara lengkap dengan deskripsi, tiket, dan rute perjalanan akurat.
                                </p>
                            </div>
                            <a href="#destinasi-section" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#278C83]">
                                <span>Eksplorasi Seluruh Katalog</span>
                                <span>→</span>
                            </a>
                        </div>

                        <div class="bg-[#FFFEFA] rounded-2xl p-6 sm:p-7 border border-[#E7E5DF] shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-5">
                                    <span class="w-12 h-12 rounded-xl bg-[#00253E]/10 text-[#00253E] flex items-center justify-center">
                                        <Sliders class="w-6 h-6" />
                                    </span>
                                    <span class="font-heading text-2xl font-bold text-[#E7E5DF]">02</span>
                                </div>
                                <h3 class="font-heading text-xl font-bold text-[#00253E] mb-2.5">Tentukan Kebutuhan</h3>
                                <p class="text-[#687784] text-xs sm:text-sm leading-relaxed mb-6">
                                    Atur durasi hari, preferensi transportasi (mobil/bus/kereta), dan akomodasi sesuai selera pelesir keluarga.
                                </p>
                            </div>
                            <a href="#trip-planner-section" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#00253E]">
                                <span>Simulasi Preferensi Tambahan</span>
                                <span>→</span>
                            </a>
                        </div>

                        <div class="bg-[#FFFEFA] rounded-2xl p-6 sm:p-7 border border-[#E7E5DF] shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-5">
                                    <span class="w-12 h-12 rounded-xl bg-[#EBF2EA] text-[#2A3D28] flex items-center justify-center">
                                        <Calendar class="w-6 h-6" />
                                    </span>
                                    <span class="font-heading text-2xl font-bold text-[#E7E5DF]">03</span>
                                </div>
                                <h3 class="font-heading text-xl font-bold text-[#00253E] mb-2.5">Susun Itinerary</h3>
                                <p class="text-[#687784] text-xs sm:text-sm leading-relaxed mb-6">
                                    Rancang urutan kunjungan harian dengan visualisasi rute cerdas agar waktu liburan tidak terbuang percuma.
                                </p>
                            </div>
                            <a href="#trip-planner-section" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#2A3D28]">
                                <span>Mulai Buat Jadwal Teratur</span>
                                <span>→</span>
                            </a>
                        </div>

                        <div class="bg-[#FFFEFA] rounded-2xl p-6 sm:p-7 border border-[#E7E5DF] shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-5">
                                    <span class="w-12 h-12 rounded-xl bg-[#2CB7A6]/15 text-[#278C83] flex items-center justify-center">
                                        <Wallet class="w-6 h-6" />
                                    </span>
                                    <span class="font-heading text-2xl font-bold text-[#E7E5DF]">04</span>
                                </div>
                                <h3 class="font-heading text-xl font-bold text-[#00253E] mb-2.5">Periksa Anggaran</h3>
                                <p class="text-[#687784] text-xs sm:text-sm leading-relaxed mb-6">
                                    Semua estimasi biaya tiket, hotel, makan, dan bensin secara transparan langsung terhitung otomatis.
                                </p>
                            </div>
                            <a href="#trip-planner-section" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#278C83]">
                                <span>Kalkulasi Biaya Terperinci</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 06 & 07 — SMART TRIP & BUDGET PLANNER HIGHLIGHT -->
            <section id="trip-planner-section" class="py-20 lg:py-28 px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
                    
                    <div class="lg:col-span-5">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#E6F4F2] text-[#278C83] text-xs font-bold uppercase tracking-wider mb-3">
                            <CheckCircle2 class="w-4 h-4" />
                            <span>FITUR PINTAR NUSA WANDER</span>
                        </div>

                        <h2 class="font-heading text-3xl sm:text-4xl lg:text-[40px] font-bold text-[#00253E] leading-[1.18] tracking-tight mb-5">
                            Liburan Menyenangkan,<br>
                            Anggaran Tetap Terkendali
                        </h2>

                        <p class="text-[#687784] text-sm sm:text-base leading-relaxed mb-8">
                            Tak perlu lagi khawatir over-budget saat jalan-jalan bareng keluarga. Nusa Wander merangkum perkiraan pengeluaran dari ujung hingga ujung: tiket kegiatan, penginapan hingga konsumsi finansial yang teratur.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3.5 gap-x-4 mb-9">
                            <div class="flex items-start gap-2.5 text-xs sm:text-sm font-semibold text-[#00253E]">
                                <span class="w-5 h-5 rounded-full bg-[#E6F4F2] text-[#278C83] flex items-center justify-center shrink-0 mt-0.5">✓</span>
                                <span>Kalkulasi Otomatis Tiket & Retribusi</span>
                            </div>
                            <div class="flex items-start gap-2.5 text-xs sm:text-sm font-semibold text-[#00253E]">
                                <span class="w-5 h-5 rounded-full bg-[#E6F4F2] text-[#278C83] flex items-center justify-center shrink-0 mt-0.5">✓</span>
                                <span>Peringatan Batas Maksimal Budget</span>
                            </div>
                            <div class="flex items-start gap-2.5 text-xs sm:text-sm font-semibold text-[#00253E]">
                                <span class="w-5 h-5 rounded-full bg-[#E6F4F2] text-[#278C83] flex items-center justify-center shrink-0 mt-0.5">✓</span>
                                <span>Rekomendasi Penginapan & Kuliner</span>
                            </div>
                            <div class="flex items-start gap-2.5 text-xs sm:text-sm font-semibold text-[#00253E]">
                                <span class="w-5 h-5 rounded-full bg-[#E6F4F2] text-[#278C83] flex items-center justify-center shrink-0 mt-0.5">✓</span>
                                <span>Unduh PDF / Ekspor Ringkasan</span>
                            </div>
                        </div>

                        <button @click="calcModal = true" class="inline-flex items-center gap-2.5 bg-[#00253E] hover:bg-[#173B57] text-white text-sm sm:text-base font-semibold px-7 py-3.5 rounded-xl shadow-md transition-all">
                            <Calculator class="w-5 h-5" />
                            <span>Coba Kalkulator Anggaran Sekarang</span>
                        </button>
                    </div>

                    <!-- Right Column Mockup -->
                    <div class="lg:col-span-7">
                        <div class="bg-[#F4EBDD]/40 p-3 sm:p-5 rounded-3xl border border-[#E7E5DF] shadow-xl">
                            <div class="bg-[#FFFEFA] rounded-2xl border border-[#E7E5DF] p-5 sm:p-7 shadow-sm">
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-[#E7E5DF] gap-3">
                                    <div>
                                        <span class="text-[11px] font-bold text-[#687784] uppercase tracking-wider block">
                                            RENCANA PERJALANAN 3 HARI • 2 ORANG
                                        </span>
                                        <h4 class="font-heading text-xl sm:text-2xl font-bold text-[#00253E]">
                                            Jelajah Yogyakarta & Sekitarnya
                                        </h4>
                                    </div>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#EBF2EA] text-[#2A3D28] text-xs font-bold border border-[#A8BFA3]/30">
                                        <span class="w-2 h-2 rounded-full bg-[#A8BFA3] animate-ping"></span>
                                        <span>Kondisi: Aman (92.8%)</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 mt-5 mb-5 border-b border-[#E7E5DF] pb-3">
                                    <button 
                                        @click="mockupTab = 'budget'" 
                                        :class="mockupTab === 'budget' ? 'bg-[#00253E] text-white' : 'text-[#687784] hover:text-[#00253E]'" 
                                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all"
                                    >
                                        Rincian Anggaran
                                    </button>
                                    <button 
                                        @click="mockupTab = 'itinerary'" 
                                        :class="mockupTab === 'itinerary' ? 'bg-[#00253E] text-white' : 'text-[#687784] hover:text-[#00253E]'" 
                                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all"
                                    >
                                        Jadwal Itinerary
                                    </button>
                                </div>

                                <!-- Tab 1: Budget -->
                                <div v-if="mockupTab === 'budget'" class="space-y-4">
                                    <div>
                                        <div class="flex justify-between items-center text-xs font-semibold mb-1.5">
                                            <span class="text-[#687784]">Estimasi Terpakai: <strong class="text-[#00253E]">Rp{{ Number(simTotal).toLocaleString('id-ID') }}</strong></span>
                                            <span class="text-[#687784]">Plafon: <strong class="text-[#00253E]">Rp{{ Number(simCap).toLocaleString('id-ID') }}</strong></span>
                                        </div>
                                        <div class="w-full h-3 bg-[#F4EBDD] rounded-full overflow-hidden p-0.5">
                                            <div class="h-full bg-[#278C83] rounded-full transition-all duration-500" :style="`width: ${Math.min(100, Math.round((simTotal/simCap)*100))}%;`"></div>
                                        </div>
                                    </div>

                                    <div class="space-y-2.5 pt-2">
                                        <div class="flex items-center justify-between p-3 rounded-xl bg-[#FFF8F1]/70 border border-[#E7E5DF]">
                                            <div class="flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-lg bg-[#278C83]/10 text-[#278C83] flex items-center justify-center">🎫</span>
                                                <div>
                                                    <p class="text-xs sm:text-sm font-bold text-[#00253E]">Tiket & Wisata</p>
                                                    <p class="text-[11px] text-[#687784]">4 Destinasi (Borobudur, Tamansari, dll)</p>
                                                </div>
                                            </div>
                                            <span class="text-xs sm:text-sm font-bold text-[#00253E]">Rp110.000</span>
                                        </div>

                                        <div class="flex items-center justify-between p-3 rounded-xl bg-[#FFF8F1]/70 border border-[#E7E5DF]">
                                            <div class="flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-lg bg-[#00253E]/10 text-[#00253E] flex items-center justify-center">🚗</span>
                                                <div>
                                                    <p class="text-xs sm:text-sm font-bold text-[#00253E]">Transportasi & BBM</p>
                                                    <p class="text-[11px] text-[#687784]">Sewa Kendaraan & Bensin 3 Hari</p>
                                                </div>
                                            </div>
                                            <span class="text-xs sm:text-sm font-bold text-[#00253E]">Rp450.000</span>
                                        </div>

                                        <div class="flex items-center justify-between p-3 rounded-xl bg-[#FFF8F1]/70 border border-[#E7E5DF]">
                                            <div class="flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-lg bg-[#A8BFA3]/20 text-[#2A3D28] flex items-center justify-center">🏨</span>
                                                <div>
                                                    <p class="text-xs sm:text-sm font-bold text-[#00253E]">Akomodasi & Hotel</p>
                                                    <p class="text-[11px] text-[#687784]">2 Malam Guesthouse Bintang Tiga</p>
                                                </div>
                                            </div>
                                            <span class="text-xs sm:text-sm font-bold text-[#00253E]">Rp640.000</span>
                                        </div>

                                        <div class="flex items-center justify-between p-3 rounded-xl bg-[#FFF8F1]/70 border border-[#E7E5DF]">
                                            <div class="flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-lg bg-[#E98B70]/20 text-[#E98B70] flex items-center justify-center">🍲</span>
                                                <div>
                                                    <p class="text-xs sm:text-sm font-bold text-[#00253E]">Kuliner & Makan/Camilan</p>
                                                    <p class="text-[11px] text-[#687784]">Warung Kopi & Bakmi Jowo, Gudeg dll</p>
                                                </div>
                                            </div>
                                            <span class="text-xs sm:text-sm font-bold text-[#00253E]">Rp250.000</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Itinerary -->
                                <div v-else class="space-y-4">
                                    <div class="border-l-2 border-[#278C83] pl-4 space-y-1 text-xs">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#E6F4F2] text-[#278C83] font-bold">HARI 1 — Candi & Sejarah</span>
                                        <p><strong>08.00</strong> — Candi Prambanan (Tiket & Pemandu)</p>
                                        <p><strong>13.00</strong> — Makan Siang Gudeg Wijilan</p>
                                        <p><strong>16.00</strong> — Malioboro & Titik Nol KM</p>
                                    </div>
                                    <div class="border-l-2 border-[#00253E] pl-4 space-y-1 text-xs">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#00253E]/10 text-[#00253E] font-bold">HARI 2 — Istana & Sky View</span>
                                        <p><strong>08.30</strong> — Taman Sari Water Castle</p>
                                        <p><strong>16.30</strong> — Sunset di HeHa Sky View</p>
                                    </div>
                                    <div class="border-l-2 border-[#A8BFA3] pl-4 space-y-1 text-xs">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#EBF2EA] text-[#2A3D28] font-bold">HARI 3 — Pantai & Pulang</span>
                                        <p><strong>07.00</strong> — Pantai Parangtritis</p>
                                        <p><strong>14.00</strong> — Perjalanan Pulang</p>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-t border-[#E7E5DF] flex flex-col sm:flex-row sm:items-center justify-between text-xs text-[#687784] gap-2">
                                    <span>Data bersumber perkiraan pengguna terverifikasi</span>
                                    <button @click="calcModal = true" class="font-bold text-[#278C83] hover:underline">
                                        Ubah Parameter...
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- SECTION 08 — CTA BANNER -->
            <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto">
                <div class="relative bg-gradient-to-br from-[#173B57] to-[#00253E] rounded-3xl p-8 sm:p-12 lg:p-16 overflow-hidden shadow-2xl">
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-8 text-white">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#2CB7A6] text-xs font-bold uppercase tracking-wider mb-4 border border-white/10">
                                <Sparkles class="w-3.5 h-3.5" />
                                <span>Fitur Asisten Rekomendasi Terintegrasi</span>
                            </div>
                            <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight mb-4">
                                Belum Menemukan Destinasi yang Cocok?
                            </h2>
                            <p class="text-white/85 text-sm sm:text-base leading-relaxed max-w-2xl">
                                Gunakan mesin rekomendasi AI pendamping kami. Ketik suasana impian Anda — misalnya <em>"pantai tenang untuk santai keluarga dengan budget 500 ribu"</em> — dan biarkan sistem mencocokkannya dengan harta karun tersembunyi Nusantara.
                            </p>
                        </div>

                        <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-3 justify-end">
                            <button @click="aiModal = true" class="inline-flex items-center justify-center gap-2 bg-[#2CB7A6] hover:bg-[#278C83] text-[#00253E] hover:text-white text-sm font-bold px-6 py-3.5 rounded-xl shadow-lg transition-all">
                                <Sparkles class="w-4 h-4" />
                                <span>Cari Alternatif dengan AI</span>
                            </button>
                            <a href="#destinasi-section" class="inline-flex items-center justify-center gap-2 bg-transparent hover:bg-white/10 text-white text-sm font-semibold px-6 py-3.5 rounded-xl border border-white/30 hover:border-white transition-all">
                                <span>Buka Direktori Lengkap</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- SECTION 09 — FOOTER -->
        <footer id="footer-section" class="bg-[#FFF8F1] border-t border-[#E7E5DF] pt-16 pb-12">
            <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-12 border-b border-[#E7E5DF]">
                    <div class="lg:col-span-4 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-[#00253E] text-white flex items-center justify-center shadow-md">
                                <Compass class="w-5 h-5" />
                            </span>
                            <span class="font-heading font-bold text-xl text-[#00253E]">NUSA WANDER</span>
                        </div>
                        <p class="text-xs sm:text-sm text-[#687784] leading-relaxed">
                            Portal Informasi Destinasi & Perencanaan Liburan Cerdas Terpadu di Indonesia. Terintegrasi rute pintar, estimasi budget transparan, dan kurasi objek wisata terindah nusantara dari Sabang sampai Merauke.
                        </p>
                        <p class="text-xs text-[#687784]">
                            <strong>Email:</strong> halo@nusawander.id | <strong>WhatsApp:</strong> (+62) 821-2345-6789
                        </p>
                    </div>

                    <div class="lg:col-span-2 space-y-3">
                        <h5 class="text-sm font-bold text-[#00253E] uppercase tracking-wider">Navigasi Cepat</h5>
                        <ul class="space-y-2 text-xs sm:text-sm text-[#687784]">
                            <li><a href="#hero" class="hover:text-[#278C83]">Beranda</a></li>
                            <li><a href="#destinasi-section" class="hover:text-[#278C83]">Katalog Destinasi</a></li>
                            <li><a href="#how-it-works-section" class="hover:text-[#278C83]">Rencana Perjalanan</a></li>
                            <li><a href="#trip-planner-section" class="hover:text-[#278C83]">Eksplorasi Pintar</a></li>
                        </ul>
                    </div>

                    <div class="lg:col-span-3 space-y-3">
                        <h5 class="text-sm font-bold text-[#00253E] uppercase tracking-wider">Destinasi Populer</h5>
                        <ul class="space-y-2 text-xs sm:text-sm text-[#687784]">
                            <li><a href="#destinasi-section" class="hover:text-[#278C83]">Candi Borobudur, Magelang</a></li>
                            <li><a href="#destinasi-section" class="hover:text-[#278C83]">Pantai Parangtritis, Bantul</a></li>
                            <li><a href="#destinasi-section" class="hover:text-[#278C83]">Taman Nasional Komodo, NTT</a></li>
                            <li><a href="#destinasi-section" class="hover:text-[#278C83]">Raja Ampat, Papua</a></li>
                        </ul>
                    </div>

                    <div class="lg:col-span-3 space-y-3">
                        <h5 class="text-sm font-bold text-[#00253E] uppercase tracking-wider">Bantuan & Kontak</h5>
                        <div class="p-3 bg-[#FFFEFA] rounded-xl border border-[#E7E5DF] text-xs text-[#687784] space-y-1">
                            <p class="font-semibold text-[#00253E]">Kantor Operasional:</p>
                            <p>Sentra Wisata Indonesia, Jl. Jend. Sudirman Kav. 21, Jakarta Selatan</p>
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#687784]">
                    <p>© 2026 Nusa Wander. Explore Indonesia, Create Your Journey.</p>
                    <div class="flex items-center gap-6">
                        <a href="#" class="hover:text-[#00253E]">Kebijakan Privasi</a>
                        <a href="#" class="hover:text-[#00253E]">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Toast Notice -->
        <div 
            v-if="showToast" 
            class="fixed bottom-6 right-6 z-50 bg-[#00253E] text-white text-xs font-semibold px-4 py-3 rounded-xl shadow-2xl border border-white/10"
        >
            {{ toastMessage }}
        </div>

    </div>
</template>
