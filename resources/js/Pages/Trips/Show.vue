<script setup>
import { ref, computed } from 'vue'
import { useForm, router, Link, Head } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import {
    ArrowLeft, Plus, Trash2, GripVertical, Download, MapPin, Clock,
    Wallet, FileText, ChevronDown, Check, X
} from 'lucide-vue-next'

const props = defineProps({
    trip: {
        type: Object,
        required: true,
    },
    availableDestinations: {
        type: Array,
        required: true,
    },
})

const itineraryItems = ref([...props.trip.itinerary_items])

const addForm = useForm({
    destination_id: '',
    catatan: '',
})

const costForm = useForm({
    harga_per_orang: 0,
    harga_per_kelompok: 0,
    keterangan: '',
})

const showAddForm = ref(false)
const showCostForm = ref(false)
const isReordering = ref(false)

function formatPrice(val) {
    return 'Rp ' + Number(val).toLocaleString('id-ID')
}

// Drag & drop reorder handler
async function onDragEnd() {
    isReordering.value = true
    const orderPayload = itineraryItems.value.map((item, index) => ({
        id: item.id,
        urutan: index + 1,
    }))

    router.patch(route('trips.items.reorder', props.trip.id), {
        items: orderPayload,
    }, {
        preserveScroll: true,
        onFinish: () => { isReordering.value = false },
    })
}

function addItem() {
    addForm.post(route('trips.items.add', props.trip.id), {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset()
            showAddForm.value = false
        },
    })
}

function removeItem(item) {
    router.delete(route('trips.items.remove', [props.trip.id, item.id]), {
        preserveScroll: true,
    })
}

function addCost() {
    costForm.post(route('trips.costs.store', props.trip.id), {
        preserveScroll: true,
        onSuccess: () => {
            costForm.reset()
            showCostForm.value = false
        },
    })
}

function deleteCost(cost) {
    router.delete(route('trips.costs.destroy', [props.trip.id, cost.id]), {
        preserveScroll: true,
    })
}

const itemsSubtotal = computed(() =>
    itineraryItems.value.reduce((sum, item) => sum + Number(item.estimasi_harga), 0)
)

const costsSubtotal = computed(() =>
    (props.trip.costs || []).reduce((sum, c) => sum + Number(c.harga_per_orang) + Number(c.harga_per_kelompok), 0)
)

const grandTotal = computed(() => itemsSubtotal.value + costsSubtotal.value)
</script>

<template>
    <Head :title="`${trip.judul} - NUSA WANDER Planner`" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
            <!-- Top Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <Link :href="route('trips.index')" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#404945]/60 hover:text-[#003527] transition-colors mb-2">
                        <ArrowLeft class="w-3.5 h-3.5" /> Kembali ke Trip Saya
                    </Link>
                    <h1 class="font-heading font-bold text-2xl md:text-3xl text-[#003527]">
                        {{ trip.judul }}
                    </h1>
                    <p class="text-sm text-[#404945]/70 flex items-center gap-1.5 mt-1">
                        <MapPin class="w-4 h-4 text-emerald-600 shrink-0" />
                        Destinasi Utama: {{ trip.destination?.nama }} ({{ trip.destination?.kota }})
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="route('trips.export-pdf', trip.id)"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-[#174c3c] hover:bg-[#003527] text-white text-sm font-semibold rounded-2xl shadow-lg shadow-emerald-200 transition-all"
                    >
                        <Download class="w-4 h-4" /> Export PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT: Itinerary Planner -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 md:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="font-heading font-bold text-xl text-[#003527]">Jadwal Itinerary</h2>
                                <p class="text-xs text-[#404945]/60 mt-0.5">Geser kartu untuk mengatur urutan kunjungan.</p>
                            </div>
                            <button
                                @click="showAddForm = !showAddForm"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                            >
                                <Plus class="w-4 h-4" /> Tambah Destinasi
                            </button>
                        </div>

                        <!-- Add Item Form -->
                        <div v-if="showAddForm" class="bg-emerald-50/70 rounded-2xl p-4 mb-6 border border-emerald-100 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-[#404945]/70 mb-1">Pilih Destinasi</label>
                                    <select v-model="addForm.destination_id" class="w-full px-3 py-2 bg-white border border-emerald-100 rounded-xl text-xs cursor-pointer">
                                        <option value="" disabled>Pilih destinasi</option>
                                        <option v-for="d in availableDestinations" :key="d.id" :value="d.id">
                                            {{ d.nama }} ({{ d.kota }})
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-[#404945]/70 mb-1">Catatan (Opsional)</label>
                                    <input v-model="addForm.catatan" type="text" placeholder="Contoh: Titik kumpul jam 09.00" class="w-full px-3 py-2 bg-white border border-emerald-100 rounded-xl text-xs" />
                                </div>
                            </div>
                            <p v-if="addForm.errors.destination_id" class="text-xs text-rose-500">{{ addForm.errors.destination_id }}</p>
                            <div class="flex justify-end gap-2">
                                <button @click="showAddForm = false" class="px-3 py-2 text-xs font-medium text-gray-600 hover:text-gray-800">Batal</button>
                                <button @click="addItem" :disabled="addForm.processing" class="px-4 py-2 bg-[#174c3c] hover:bg-[#003527] text-white text-xs font-bold rounded-lg disabled:opacity-50 cursor-pointer">Simpan</button>
                            </div>
                        </div>

                        <!-- Draggable List -->
                        <draggable
                            v-model="itineraryItems"
                            item-key="id"
                            handle=".drag-handle"
                            tag="div"
                            class="space-y-3"
                            @end="onDragEnd"
                        >
                            <template #item="{ element, index }">
                                <div class="flex items-start gap-3 p-4 bg-emerald-50/40 hover:bg-emerald-50/80 border border-emerald-100 rounded-2xl transition-colors group">
                                    <!-- Drag handle -->
                                    <button class="drag-handle p-1 text-gray-400 group-hover:text-emerald-600 cursor-grab active:cursor-grabbing shrink-0 mt-0.5" title="Geser untuk mengubah urutan">
                                        <GripVertical class="w-5 h-5" />
                                    </button>

                                    <!-- Sequence number -->
                                    <div class="w-8 h-8 rounded-xl bg-[#003527] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ index + 1 }}
                                    </div>

                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <Link :href="`/destinasi/${element.destination_id}`" class="font-semibold text-sm text-[#003527] hover:text-[#174c3c] transition-colors line-clamp-1">
                                            {{ element.judul }}
                                        </Link>
                                        <p class="text-xs text-[#404945]/60 flex items-center gap-1 mt-0.5">
                                            <MapPin class="w-3 h-3 shrink-0" />
                                            {{ element.destination?.kota ?? 'Lokasi tidak diketahui' }}
                                        </p>
                                        <p v-if="element.catatan" class="text-[11px] text-emerald-800 bg-emerald-100/60 inline-block px-2 py-0.5 rounded-md mt-1.5">
                                            {{ element.catatan }}
                                        </p>
                                    </div>

                                    <!-- Price & Delete -->
                                    <div class="text-right shrink-0 space-y-1.5">
                                        <p class="font-heading font-bold text-sm text-[#003527]">{{ formatPrice(element.estimasi_harga) }}</p>
                                        <button @click="removeItem(element)" class="inline-flex items-center gap-1 text-[11px] text-rose-500 hover:text-rose-700 transition-colors cursor-pointer">
                                            <Trash2 class="w-3 h-3" /> Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </draggable>

                        <div v-if="itineraryItems.length === 0" class="text-center py-12 bg-emerald-50/40 rounded-2xl border border-dashed border-emerald-200">
                            <MapPin class="w-10 h-10 text-emerald-200 mx-auto mb-3" />
                            <p class="text-sm text-[#404945]/60">Belum ada destinasi di itinerary. Klik "Tambah Destinasi" di atas.</p>
                        </div>
                    </div>

                    <!-- Cost Breakdown (Extra budget) -->
                    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 md:p-8">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="font-heading font-bold text-xl text-[#003527]">Rincian Biaya Tambahan</h2>
                                <p class="text-xs text-[#404945]/60 mt-0.5">Transport, penginapan, konsumsi, dan lainnya.</p>
                            </div>
                            <button
                                @click="showCostForm = !showCostForm"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                            >
                                <Plus class="w-4 h-4" /> Tambah Biaya
                            </button>
                        </div>

                        <!-- Add Cost Form -->
                        <div v-if="showCostForm" class="bg-emerald-50/70 rounded-2xl p-4 mb-5 border border-emerald-100 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-[#404945]/70 mb-1">Keterangan</label>
                                <input v-model="costForm.keterangan" type="text" placeholder="Contoh: Hotel" class="w-full px-3 py-2 bg-white border border-emerald-100 rounded-xl text-xs" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-[#404945]/70 mb-1">Per Orang</label>
                                <input v-model="costForm.harga_per_orang" type="number" min="0" class="w-full px-3 py-2 bg-white border border-emerald-100 rounded-xl text-xs" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-[#404945]/70 mb-1">Per Kelompok</label>
                                <input v-model="costForm.harga_per_kelompok" type="number" min="0" class="w-full px-3 py-2 bg-white border border-emerald-100 rounded-xl text-xs" />
                            </div>
                            <button @click="addCost" :disabled="costForm.processing" class="px-4 py-2 bg-[#174c3c] hover:bg-[#003527] text-white text-xs font-bold rounded-xl disabled:opacity-50 cursor-pointer">
                                Simpan Biaya
                            </button>
                        </div>

                        <!-- Cost List -->
                        <div class="divide-y divide-emerald-50">
                            <div v-for="cost in trip.costs" :key="cost.id" class="flex items-center justify-between py-3">
                                <div>
                                    <p class="text-sm font-medium text-[#003527]">{{ cost.keterangan }}</p>
                                    <p class="text-[11px] text-[#404945]/60 mt-0.5">
                                        Per Orang: {{ formatPrice(cost.harga_per_orang) }} &bull; Per Kelompok: {{ formatPrice(cost.harga_per_kelompok) }}
                                    </p>
                                </div>
                                <button @click="deleteCost(cost)" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <p v-if="!trip.costs || trip.costs.length === 0" class="text-xs text-[#404945]/50 py-4 text-center">
                            Belum ada rincian biaya tambahan.
                        </p>
                    </div>
                </div>

                <!-- RIGHT: Budget Summary -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 md:p-8 sticky top-28 space-y-6">
                        <h2 class="font-heading font-bold text-xl text-[#003527] flex items-center gap-2">
                            <Wallet class="w-5 h-5 text-emerald-600" /> Ringkasan Anggaran
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex item-center justify-between">
                                <span class="text-[#404945]/70">Tiket Destinasi ({{ itineraryItems.length }} item)</span>
                                <span class="font-semibold text-[#003527]">{{ formatPrice(itemsSubtotal) }}</span>
                            </div>
                            <div class="flex item-center justify-between">
                                <span class="text-[#404945]/70">Biaya Tambahan ({{ trip.costs?.length || 0 }} item)</span>
                                <span class="font-semibold text-[#003527]">{{ formatPrice(costsSubtotal) }}</span>
                            </div>
                        </div>

                        <hr class="border-emerald-100" />

                        <div class="bg-emerald-50 rounded-2xl p-5 text-center space-y-1">
                            <p class="text-xs uppercase font-bold text-[#404945]/60 tracking-wider">Total Estimasi</p>
                            <p class="font-heading font-bold text-2xl text-[#003527]">{{ formatPrice(grandTotal) }}</p>
                            <p class="text-[11px] text-[#404945]/50">*Estimasi dapat berubah sewaktu-waktu</p>
                        </div>

                        <div class="text-[11px] text-[#404945]/60 leading-relaxed bg-emerald-50/50 p-3 rounded-xl border border-emerald-100">
                            <strong>Tips:</strong> Geser urutan jadwal untuk menentukan kunjungan pertama hingga terakhir, lalu unduh PDF sebagai panduan perjalanan.
                        </div>

                        <a
                            :href="route('trips.export-pdf', trip.id)"
                            class="flex items-center justify-center gap-2 w-full py-4 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl shadow-lg shadow-emerald-200 transition-all"
                        >
                            <Download class="w-4 h-4" /> Download Itinerary PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>