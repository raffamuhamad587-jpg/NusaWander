<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useForm, router, Link, Head } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AiAlternativesModal from '@/Components/AiAlternativesModal.vue'
import {
    ArrowLeft, Plus, Trash2, GripVertical, Download, MapPin, Calendar,
    Wallet, Users, Globe, AlertTriangle, CheckCircle2, Edit3, X
} from 'lucide-vue-next'

const props = defineProps({
    trip: {
        type: Object,
        required: true,
    },
    budgetSummary: {
        type: Object,
        required: true,
    },
    needsAiAlternative: {
        type: Boolean,
        default: false,
    },
    availableDestinations: {
        type: Array,
        required: true,
    },
})

const dayBuckets = ref({})

function rebuildDayBuckets() {
    const buckets = {}
    const totalDays = Math.max(1, Number(props.trip.durasi_hari || 1))
    for (let d = 1; d <= totalDays; d++) {
        buckets[d] = []
    }
    ;(props.trip.itinerary_items || []).forEach((item) => {
        const day = Math.min(totalDays, Math.max(1, Number(item.hari_ke || 1)))
        buckets[day].push(item)
    })
    Object.keys(buckets).forEach((day) => {
        buckets[day].sort((a, b) => a.urutan_kunjungan - b.urutan_kunjungan)
    })
    dayBuckets.value = buckets
}

watch(
    () => [props.trip.itinerary_items, props.trip.durasi_hari],
    () => rebuildDayBuckets(),
    { deep: true, immediate: true }
)

const daysList = computed(() => {
    const arr = []
    const totalDays = Math.max(1, Number(props.trip.durasi_hari || 1))
    for (let d = 1; d <= totalDays; d++) arr.push(d)
    return arr
})

const showAddForm = ref(false)
const showCostForm = ref(false)
const showEditTripModal = ref(false)
const showAiModal = ref(false)

function onKeydown(event) {
    if (event.key === 'Escape') {
        showEditTripModal.value = false
    }
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))

const addForm = useForm({
    destination_id: '',
    hari_ke: 1,
    catatan: '',
})

const costForm = useForm({
    nama_biaya: '',
    jenis_biaya: 'transportasi',
    dasar_perhitungan: 'per_hari',
    jumlah: props.trip.durasi_hari,
    harga_satuan: '',
    belum_dihitung: false,
})

const editTripForm = useForm({
    nama_trip: props.trip.nama_trip,
    wilayah_tujuan: props.trip.wilayah_tujuan,
    tanggal_mulai: props.trip.tanggal_mulai,
    durasi_hari: props.trip.durasi_hari,
    jumlah_peserta: props.trip.jumlah_peserta,
    anggaran: props.trip.anggaran,
})

watch(() => props.trip, (t) => {
    editTripForm.nama_trip = t.nama_trip
    editTripForm.wilayah_tujuan = t.wilayah_tujuan
    editTripForm.tanggal_mulai = t.tanggal_mulai
    editTripForm.durasi_hari = t.durasi_hari
    editTripForm.jumlah_peserta = t.jumlah_peserta
    editTripForm.anggaran = t.anggaran
}, { deep: true })

watch(() => costForm.dasar_perhitungan, (basis) => {
    if (basis === 'per_orang') costForm.jumlah = props.trip.jumlah_peserta
    else if (basis === 'per_hari') costForm.jumlah = props.trip.durasi_hari
    else costForm.jumlah = 1
})

function formatPrice(val) {
    if (val === null || val === undefined || val === '') return null
    return 'Rp ' + Number(val).toLocaleString('id-ID')
}

function formatDate(dateStr) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function onDragEnd() {
    const payload = []
    daysList.value.forEach((day) => {
        ;(dayBuckets.value[day] || []).forEach((item, idx) => {
            payload.push({
                id: item.id,
                hari_ke: day,
                urutan_kunjungan: idx + 1,
            })
        })
    })

    if (payload.length === 0) return

    router.patch(`/trips/${props.trip.id}/items/reorder`, {
        items: payload,
    }, {
        preserveScroll: true,
    })
}

function addItem() {
    addForm.post(`/trips/${props.trip.id}/items`, {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset('destination_id', 'catatan')
            showAddForm.value = false
        },
    })
}

function removeItem(item) {
    router.delete(`/trips/${props.trip.id}/items/${item.id}`, {
        preserveScroll: true,
    })
}

function addCost() {
    costForm.transform((data) => ({
        ...data,
        harga_satuan: data.belum_dihitung || data.harga_satuan === '' ? null : data.harga_satuan,
    })).post(`/trips/${props.trip.id}/costs`, {
        preserveScroll: true,
        onSuccess: () => {
            costForm.reset('nama_biaya', 'harga_satuan', 'belum_dihitung')
            showCostForm.value = false
        },
    })
}

function deleteCost(cost) {
    router.delete(`/trips/${props.trip.id}/costs/${cost.id}`, {
        preserveScroll: true,
    })
}

function submitEditTrip() {
    editTripForm.put(`/trips/${props.trip.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditTripModal.value = false
        },
    })
}

const budgetUsagePercent = computed(() => {
    const anggaran = Number(props.budgetSummary.anggaran || 0)
    if (anggaran <= 0) return 0
    return Math.min(100, Math.round((Number(props.budgetSummary.total_estimasi || 0) / anggaran) * 100))
})
</script>

<template>
    <Head :title="`${trip.nama_trip} | NUSA WANDER`" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 sm:py-10">
            <!-- Top Bar -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-7">
                <div>
                    <Link href="/trips" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-muted hover:text-brand-dark transition-colors mb-2 min-h-[36px]">
                        <ArrowLeft class="w-3.5 h-3.5" aria-hidden="true" /> Kembali ke Itinerary Saya
                    </Link>
                    <h1 class="font-heading font-bold text-2xl sm:text-3xl text-brand-dark">
                        {{ trip.nama_trip }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-brand-muted mt-1.5">
                        <span class="inline-flex items-center gap-1.5"><MapPin class="w-4 h-4 text-brand-primary" aria-hidden="true" /> {{ trip.wilayah_tujuan }}</span>
                        <span class="inline-flex items-center gap-1.5"><Calendar class="w-4 h-4 text-brand-primary" aria-hidden="true" /> {{ formatDate(trip.tanggal_mulai) }} ({{ trip.durasi_hari }} Hari)</span>
                        <span class="inline-flex items-center gap-1.5"><Users class="w-4 h-4 text-brand-primary" aria-hidden="true" /> {{ trip.jumlah_peserta }} Peserta</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="showEditTripModal = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white hover:bg-brand-input/60 border border-brand-dark/15 text-brand-dark text-xs font-semibold rounded-lg transition-colors min-h-[44px]"
                    >
                        <Edit3 class="w-4 h-4" aria-hidden="true" /> Ubah Detail Trip
                    </button>
                    <button
                        type="button"
                        @click="showAiModal = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-coral-light hover:bg-coral/25 border border-coral/40 text-coral-text text-xs font-semibold rounded-lg transition-colors min-h-[44px]"
                    >
                        <Globe class="w-4 h-4" aria-hidden="true" /> Cari Alternatif AI
                    </button>
                    <a
                        :href="`/trips/${trip.id}/export-pdf`"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white text-xs font-semibold rounded-lg transition-colors min-h-[44px]"
                    >
                        <Download class="w-4 h-4" aria-hidden="true" /> Unduh PDF
                    </a>
                </div>
            </div>

            <!-- Banner FR-13 when catalog results are insufficient -->
            <div v-if="needsAiAlternative" class="mb-6 bg-coral-light border border-coral/40 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <Globe class="w-5 h-5 text-coral-text shrink-0 mt-0.5" aria-hidden="true" />
                    <div class="text-xs sm:text-sm text-coral-text">
                        <p class="font-bold">Kandidat destinasi dari katalog untuk {{ trip.wilayah_tujuan }} masih terbatas.</p>
                        <p class="mt-0.5 opacity-90">Tambahkan destinasi katalog secara manual atau minta alternatif eksternal sementara dengan AI.</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="showAiModal = true"
                    class="px-4 py-2.5 bg-coral-text hover:bg-brand-dark text-white text-xs font-semibold rounded-lg shrink-0 min-h-[44px]"
                >
                    Cari Alternatif dengan AI
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">
                <!-- LEFT: Day-by-Day Itinerary & Cost Breakdown -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Itinerary Card -->
                    <section class="bg-white rounded-xl border border-brand-dark/10 p-5 sm:p-7">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                            <div>
                                <h2 class="font-heading font-bold text-xl text-brand-dark">Susunan itinerary per hari</h2>
                                <p class="text-xs text-brand-muted mt-0.5">Geser kartu untuk mengatur urutan kunjungan atau memindahkan destinasi antar hari.</p>
                            </div>
                            <button
                                type="button"
                                @click="showAddForm = !showAddForm"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-brand-input hover:bg-brand-dark hover:text-white text-brand-dark text-xs font-semibold rounded-lg transition-colors shrink-0 min-h-[44px]"
                            >
                                <Plus class="w-4 h-4" aria-hidden="true" /> Tambah Destinasi
                            </button>
                        </div>

                        <!-- Add Item Form -->
                        <div v-if="showAddForm" class="bg-brand-bg rounded-xl p-4 mb-5 border border-brand-dark/10 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label for="add-day" class="block text-xs font-semibold text-brand-dark mb-1">Hari kunjungan</label>
                                    <select id="add-day" v-model.number="addForm.hari_ke" class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs min-h-[44px]">
                                        <option v-for="day in daysList" :key="day" :value="day">Hari {{ day }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="add-dest" class="block text-xs font-semibold text-brand-dark mb-1">Pilih destinasi</label>
                                    <select id="add-dest" v-model="addForm.destination_id" class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs min-h-[44px]">
                                        <option value="" disabled>Pilih destinasi</option>
                                        <option v-for="d in availableDestinations" :key="d.id" :value="d.id">
                                            {{ d.nama }} ({{ d.kota }}): {{ formatPrice(d.estimasi_harga) || 'Belum dihitung' }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="add-note" class="block text-xs font-semibold text-brand-dark mb-1">Catatan (opsional)</label>
                                    <input id="add-note" v-model="addForm.catatan" type="text" placeholder="Misal: Berangkat pukul 08.00" class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs min-h-[44px]" />
                                </div>
                            </div>
                            <p v-if="addForm.errors.destination_id || addForm.errors.hari_ke" class="text-xs text-coral-text">
                                {{ addForm.errors.destination_id || addForm.errors.hari_ke }}
                            </p>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showAddForm = false" class="px-4 py-2 text-xs font-semibold text-brand-muted hover:text-brand-dark min-h-[40px]">Batal</button>
                                <button type="button" @click="addItem" :disabled="addForm.processing" class="px-4 py-2 bg-brand-primary hover:bg-brand-primaryHover text-white text-xs font-semibold rounded-lg disabled:opacity-50 min-h-[40px]">Simpan</button>
                            </div>
                        </div>

                        <!-- Days List -->
                        <div class="space-y-5">
                            <div v-for="day in daysList" :key="day" class="rounded-xl border border-brand-dark/10 bg-brand-bg/60 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-heading font-bold text-sm text-brand-dark">
                                        Hari {{ day }}
                                    </h3>
                                    <span class="text-xs font-medium text-brand-muted tabular-nums">
                                        {{ (dayBuckets[day] || []).length }} kunjungan
                                    </span>
                                </div>

                                <draggable
                                    v-model="dayBuckets[day]"
                                    group="itinerary-days"
                                    item-key="id"
                                    handle=".drag-handle"
                                    tag="div"
                                    class="space-y-2.5 min-h-[44px]"
                                    @end="onDragEnd"
                                >
                                    <template #item="{ element, index }">
                                        <div class="flex items-start gap-3 p-3.5 bg-white border border-brand-dark/10 rounded-lg">
                                            <button
                                                type="button"
                                                aria-label="Geser untuk mengubah urutan kunjungan"
                                                class="drag-handle p-1.5 text-brand-muted hover:text-brand-dark cursor-grab active:cursor-grabbing shrink-0 min-h-[36px] min-w-[36px] flex items-center justify-center"
                                                title="Geser urutan"
                                            >
                                                <GripVertical class="w-4 h-4" aria-hidden="true" />
                                            </button>

                                            <div class="w-7 h-7 rounded-md bg-brand-dark text-white flex items-center justify-center font-bold text-xs shrink-0 tabular-nums mt-0.5">
                                                {{ index + 1 }}
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <Link :href="`/destinasi/${element.destination_id}`" class="font-semibold text-sm text-brand-dark hover:text-brand-primary line-clamp-1">
                                                    {{ element.destination?.nama || 'Destinasi' }}
                                                </Link>
                                                <p class="text-xs text-brand-muted mt-0.5">
                                                    {{ element.destination?.kota }} &bull; {{ element.destination?.category?.nama_kategori }}
                                                </p>
                                                <p v-if="element.catatan" class="text-xs text-brand-dark bg-brand-input/70 inline-block px-2 py-0.5 rounded-md mt-1.5">
                                                    {{ element.catatan }}
                                                </p>
                                            </div>

                                            <div class="text-right shrink-0 space-y-1">
                                                <p v-if="formatPrice(element.destination?.estimasi_harga)" class="font-bold text-xs text-brand-dark tabular-nums">
                                                    {{ formatPrice(element.destination.estimasi_harga) }}
                                                    <span class="block text-[11px] font-normal text-brand-muted">
                                                        /{{ element.destination.jenis_harga === 'per_orang' ? 'orang' : 'kelompok' }}
                                                    </span>
                                                </p>
                                                <span v-else class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold bg-coral-light text-coral-text">
                                                    Belum dihitung
                                                </span>
                                                <button
                                                    type="button"
                                                    @click="removeItem(element)"
                                                    class="inline-flex items-center gap-1 ml-auto text-xs font-medium text-coral-text hover:underline pt-1"
                                                >
                                                    <Trash2 class="w-3.5 h-3.5" aria-hidden="true" /> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>

                                <p v-if="(dayBuckets[day] || []).length === 0" class="text-xs text-center text-brand-muted py-3 border border-dashed border-brand-dark/20 rounded-lg">
                                    Belum ada kunjungan pada Hari {{ day }}. Tambahkan destinasi atau seret kartu ke sini.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Structured Cost Breakdown -->
                    <section class="bg-white rounded-xl border border-brand-dark/10 p-5 sm:p-7">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <h2 class="font-heading font-bold text-xl text-brand-dark">Rincian komponen biaya</h2>
                                <p class="text-xs text-brand-muted mt-0.5">Tiket destinasi otomatis terhitung sesuai jumlah peserta; tambahkan transport, penginapan, atau konsumsi lainnya.</p>
                            </div>
                            <button
                                type="button"
                                @click="showCostForm = !showCostForm"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-brand-input hover:bg-brand-dark hover:text-white text-brand-dark text-xs font-semibold rounded-lg transition-colors shrink-0 min-h-[44px]"
                            >
                                <Plus class="w-4 h-4" aria-hidden="true" /> Tambah Biaya
                            </button>
                        </div>

                        <!-- Add Cost Form -->
                        <div v-if="showCostForm" class="bg-brand-bg rounded-xl p-4 mb-5 border border-brand-dark/10 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div>
                                    <label for="cost-name" class="block text-xs font-semibold text-brand-dark mb-1">Nama biaya *</label>
                                    <input id="cost-name" v-model="costForm.nama_biaya" type="text" placeholder="Misal: Sewa Motor / Hotel" class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs min-h-[44px]" />
                                </div>
                                <div>
                                    <label for="cost-type" class="block text-xs font-semibold text-brand-dark mb-1">Jenis biaya *</label>
                                    <select id="cost-type" v-model="costForm.jenis_biaya" class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs min-h-[44px]">
                                        <option value="transportasi">Transportasi</option>
                                        <option value="penginapan">Penginapan</option>
                                        <option value="kuliner">Kuliner</option>
                                        <option value="tiket">Tiket</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="cost-basis" class="block text-xs font-semibold text-brand-dark mb-1">Dasar perhitungan *</label>
                                    <select id="cost-basis" v-model="costForm.dasar_perhitungan" class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs min-h-[44px]">
                                        <option value="per_orang">Per Orang</option>
                                        <option value="per_hari">Per Hari</option>
                                        <option value="per_kelompok">Per Kelompok</option>
                                        <option value="tetap">Tetap (Sekali Bayar)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="cost-qty" class="block text-xs font-semibold text-brand-dark mb-1">Jumlah / pengali</label>
                                    <input id="cost-qty" v-model.number="costForm.jumlah" type="number" min="1" class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs tabular-nums min-h-[44px]" />
                                </div>
                                <div>
                                    <label for="cost-price" class="block text-xs font-semibold text-brand-dark mb-1">Harga satuan (Rp)</label>
                                    <input
                                        id="cost-price"
                                        v-model="costForm.harga_satuan"
                                        :disabled="costForm.belum_dihitung"
                                        type="number"
                                        min="0"
                                        placeholder="Kosongkan jika belum tahu"
                                        class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs tabular-nums disabled:opacity-50 min-h-[44px]"
                                    />
                                </div>
                                <div class="flex items-end pb-2">
                                    <label for="cost-unknown" class="inline-flex items-center gap-2 text-xs font-medium text-brand-dark">
                                        <input id="cost-unknown" v-model="costForm.belum_dihitung" type="checkbox" class="rounded border-brand-dark/30 text-brand-primary" />
                                        Tandai "Belum dihitung"
                                    </label>
                                </div>
                            </div>
                            <p v-if="costForm.errors.nama_biaya" class="text-xs text-coral-text">{{ costForm.errors.nama_biaya }}</p>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showCostForm = false" class="px-4 py-2 text-xs font-semibold text-brand-muted min-h-[40px]">Batal</button>
                                <button type="button" @click="addCost" :disabled="costForm.processing" class="px-4 py-2 bg-brand-primary hover:bg-brand-primaryHover text-white text-xs font-semibold rounded-lg disabled:opacity-50 min-h-[40px]">
                                    Simpan Biaya
                                </button>
                            </div>
                        </div>

                        <!-- Cost List -->
                        <div class="divide-y divide-brand-dark/10">
                            <div v-for="cost in trip.costs" :key="cost.id" class="flex items-center justify-between gap-3 py-3.5">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-brand-dark">{{ cost.nama_biaya }}</p>
                                        <span class="px-2 py-0.5 rounded-md bg-brand-input text-brand-dark text-[11px] font-medium capitalize">
                                            {{ cost.jenis_biaya }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-brand-muted mt-0.5 tabular-nums">
                                        Dasar: {{ cost.dasar_perhitungan.replace('_', ' ') }} &bull; Jumlah: {{ cost.jumlah }}
                                        <template v-if="formatPrice(cost.harga_satuan)">
                                            &bull; Satuan: {{ formatPrice(cost.harga_satuan) }}
                                        </template>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <span v-if="formatPrice(cost.subtotal)" class="font-bold text-sm text-brand-dark tabular-nums">
                                        {{ formatPrice(cost.subtotal) }}
                                    </span>
                                    <span v-else class="px-2.5 py-1 rounded-md text-xs font-semibold bg-coral-light text-coral-text">
                                        Belum dihitung
                                    </span>
                                    <button
                                        type="button"
                                        @click="deleteCost(cost)"
                                        :aria-label="`Hapus komponen biaya ${cost.nama_biaya}`"
                                        class="p-2 text-brand-muted hover:text-coral-text hover:bg-coral-light rounded-lg min-h-[40px] min-w-[40px] flex items-center justify-center"
                                        title="Hapus komponen biaya"
                                    >
                                        <Trash2 class="w-4 h-4" aria-hidden="true" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p v-if="!trip.costs || trip.costs.length === 0" class="text-xs text-brand-muted py-6 text-center">
                            Belum ada komponen biaya tercatat.
                        </p>
                    </section>
                </div>

                <!-- RIGHT: Budget Summary (elevated sticky summary) -->
                <div>
                    <aside class="bg-white rounded-xl border border-brand-dark/10 shadow-sm p-6 sticky top-24 space-y-5">
                        <h2 class="font-heading font-bold text-lg text-brand-dark flex items-center gap-2">
                            <Wallet class="w-5 h-5 text-brand-primary" aria-hidden="true" /> Ringkasan anggaran
                        </h2>

                        <div
                            :class="[
                                'rounded-lg p-3.5 border flex items-start gap-2.5 text-xs',
                                budgetSummary.is_over_budget
                                    ? 'bg-coral-light border-coral/40 text-coral-text'
                                    : 'bg-teal-light border-brand-primary/25 text-brand-dark',
                            ]"
                        >
                            <AlertTriangle v-if="budgetSummary.is_over_budget" class="w-4 h-4 shrink-0 mt-0.5" aria-hidden="true" />
                            <CheckCircle2 v-else class="w-4 h-4 text-brand-primary shrink-0 mt-0.5" aria-hidden="true" />
                            <div>
                                <p class="font-bold">
                                    {{ budgetSummary.is_over_budget ? 'Melebihi anggaran' : 'Dalam batas anggaran' }}
                                </p>
                                <p class="mt-0.5">
                                    {{ budgetSummary.is_over_budget
                                        ? 'Total estimasi melampaui plafon anggaran Anda.'
                                        : 'Total estimasi biaya masih di bawah batas anggaran.' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold text-brand-muted mb-1.5 tabular-nums">
                                <span>Pemakaian anggaran</span>
                                <span>{{ budgetUsagePercent }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-brand-input rounded-full overflow-hidden">
                                <div
                                    :class="['h-full rounded-full', budgetSummary.is_over_budget ? 'bg-coral-text' : 'bg-brand-primary']"
                                    :style="{ width: `${budgetUsagePercent}%` }"
                                />
                            </div>
                        </div>

                        <dl class="space-y-2.5 text-sm pt-1">
                            <div class="flex items-center justify-between">
                                <dt class="text-brand-muted">Anggaran trip</dt>
                                <dd class="font-semibold text-brand-dark tabular-nums">{{ formatPrice(budgetSummary.anggaran) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-brand-muted">Total estimasi</dt>
                                <dd class="font-semibold text-brand-dark tabular-nums">{{ formatPrice(budgetSummary.total_estimasi) }}</dd>
                            </div>
                        </dl>

                        <hr class="border-brand-dark/10" />

                        <div :class="['rounded-xl p-4 text-center space-y-1', budgetSummary.is_over_budget ? 'bg-coral-light' : 'bg-brand-bg border border-brand-dark/10']">
                            <p class="text-xs font-semibold text-brand-muted">Sisa anggaran</p>
                            <p :class="['font-heading font-bold text-2xl tabular-nums', budgetSummary.is_over_budget ? 'text-coral-text' : 'text-brand-dark']">
                                {{ formatPrice(budgetSummary.sisa_anggaran) }}
                            </p>
                            <p class="text-[11px] text-brand-muted">Anggaran dikurangi total estimasi</p>
                        </div>

                        <div v-if="budgetSummary.belum_dihitung_count > 0" class="rounded-lg bg-coral-light border border-coral/40 p-3 text-xs text-coral-text">
                            Ada <strong>{{ budgetSummary.belum_dihitung_count }} komponen biaya</strong> bertanda <em>"Belum dihitung"</em> yang belum masuk total estimasi (tidak dianggap gratis).
                        </div>

                        <p class="text-[11px] text-brand-muted leading-relaxed">
                            Harga pada basis data adalah estimasi yang dikelola Admin, bukan tarif real-time.
                        </p>
                    </aside>
                </div>
            </div>
        </div>

        <!-- Edit Trip Metadata Modal (R-26 & R-32: closable with Escape) -->
        <div
            v-if="showEditTripModal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-trip-title"
            class="fixed inset-0 z-50 bg-brand-dark/50 flex items-center justify-center p-4"
            @click.self="showEditTripModal = false"
        >
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-brand-dark/10 space-y-5">
                <div class="flex items-center justify-between border-b border-brand-dark/10 pb-3">
                    <h3 id="edit-trip-title" class="font-heading font-bold text-lg text-brand-dark">Ubah detail perjalanan</h3>
                    <button
                        type="button"
                        @click="showEditTripModal = false"
                        aria-label="Tutup dialog"
                        class="p-2 text-brand-muted hover:text-brand-dark rounded-lg min-h-[40px] min-w-[40px] flex items-center justify-center"
                    >
                        <X class="w-5 h-5" aria-hidden="true" />
                    </button>
                </div>

                <form @submit.prevent="submitEditTrip" class="space-y-4">
                    <div>
                        <label for="edit-nama" class="block text-xs font-semibold text-brand-dark mb-1">Nama trip</label>
                        <input id="edit-nama" v-model="editTripForm.nama_trip" type="text" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm min-h-[44px]" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit-area" class="block text-xs font-semibold text-brand-dark mb-1">Wilayah tujuan</label>
                            <input id="edit-area" v-model="editTripForm.wilayah_tujuan" type="text" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm min-h-[44px]" />
                        </div>
                        <div>
                            <label for="edit-date" class="block text-xs font-semibold text-brand-dark mb-1">Tanggal mulai</label>
                            <input id="edit-date" v-model="editTripForm.tanggal_mulai" type="date" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm min-h-[44px]" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="edit-days" class="block text-xs font-semibold text-brand-dark mb-1">Durasi (hari)</label>
                            <input id="edit-days" v-model.number="editTripForm.durasi_hari" type="number" min="1" max="30" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm tabular-nums min-h-[44px]" />
                        </div>
                        <div>
                            <label for="edit-people" class="block text-xs font-semibold text-brand-dark mb-1">Peserta</label>
                            <input id="edit-people" v-model.number="editTripForm.jumlah_peserta" type="number" min="1" max="100" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm tabular-nums min-h-[44px]" />
                        </div>
                        <div>
                            <label for="edit-budget" class="block text-xs font-semibold text-brand-dark mb-1">Anggaran (Rp)</label>
                            <input id="edit-budget" v-model.number="editTripForm.anggaran" type="number" min="0" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm tabular-nums min-h-[44px]" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-brand-dark/10">
                        <button type="button" @click="showEditTripModal = false" class="px-4 py-2 text-sm font-medium text-brand-muted min-h-[44px]">Batal</button>
                        <button type="submit" :disabled="editTripForm.processing" class="px-5 py-2 bg-brand-primary hover:bg-brand-primaryHover text-white text-sm font-semibold rounded-lg min-h-[44px]">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <AiAlternativesModal
            :show="showAiModal"
            :initial-area="trip.wilayah_tujuan"
            :initial-duration="trip.durasi_hari"
            :initial-budget="trip.anggaran"
            @close="showAiModal = false"
        />
    </PublicLayout>
</template>
