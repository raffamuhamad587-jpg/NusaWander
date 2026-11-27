<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ArrowLeft, Check } from 'lucide-vue-next'

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    kotas: {
        type: Array,
        default: () => [],
    },
    selectedDestination: {
        type: Object,
        default: null,
    },
})

const today = new Date().toISOString().split('T')[0]

const form = useForm({
    nama_trip: props.selectedDestination ? `Perjalanan ke ${props.selectedDestination.kota}` : '',
    wilayah_tujuan: props.selectedDestination?.kota ?? (props.kotas[0] || ''),
    tanggal_mulai: today,
    durasi_hari: 2,
    jumlah_peserta: 2,
    anggaran: 1000000,
    kategori_ids: props.selectedDestination?.category_id ? [props.selectedDestination.category_id] : [],
    prioritas_biaya: 'seimbang',
    destination_id: props.selectedDestination?.id ?? null,
})

function toggleCategory(catId) {
    const idx = form.kategori_ids.indexOf(catId)
    if (idx > -1) {
        form.kategori_ids.splice(idx, 1)
    } else {
        form.kategori_ids.push(catId)
    }
}

function submit() {
    form.post('/trips')
}

const inputClass =
    'w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary transition-colors min-h-[44px]'

const priorityOptions = [
    {
        value: 'hemat',
        label: 'Hemat',
        desc: 'Utamakan destinasi berbiaya rendah dan jaga estimasi tetap di bawah plafon anggaran.',
    },
    {
        value: 'seimbang',
        label: 'Seimbang',
        desc: 'Padukan variasi kategori sesuai minat dan kisaran anggaran yang tersedia.',
    },
    {
        value: 'nyaman',
        label: 'Eksplorasi lengkap',
        desc: 'Prioritaskan destinasi unggulan sesuai kategori pilihan dengan anggaran lebih longgar.',
    },
]
</script>

<template>
    <Head title="Rencanakan Perjalanan | NUSA WANDER" />

    <PublicLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
            <Link href="/trips" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-muted hover:text-brand-dark transition-colors mb-5 min-h-[40px]">
                <ArrowLeft class="w-4 h-4" aria-hidden="true" /> Kembali ke Itinerary Saya
            </Link>

            <div class="bg-white rounded-xl border border-brand-dark/10 p-6 sm:p-8">
                <div class="mb-7">
                    <h1 class="font-heading font-bold text-2xl sm:text-3xl text-brand-dark tracking-tight">
                        Rencanakan perjalanan baru
                    </h1>
                    <p class="text-sm text-brand-muted mt-1.5">
                        Isi wilayah tujuan, durasi, jumlah peserta, batas anggaran, dan preferensi minat. Sistem akan menyusun jadwal per hari serta estimasi biaya awal dari katalog.
                    </p>
                </div>

                <div v-if="selectedDestination" class="mb-6 p-4 rounded-lg bg-brand-bg border border-brand-primary/30 text-xs text-brand-dark">
                    Destinasi pilihan <strong>{{ selectedDestination.nama }} ({{ selectedDestination.kota }})</strong> akan otomatis dimasukkan ke Hari 1 rencana perjalanan ini.
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_trip" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Nama rencana perjalanan <span class="text-coral-text">*</span>
                            </label>
                            <input
                                id="nama_trip"
                                v-model="form.nama_trip"
                                type="text"
                                placeholder="Misal: Liburan Akhir Pekan Yogyakarta"
                                :class="inputClass"
                            />
                            <p v-if="form.errors.nama_trip" class="text-xs text-coral-text mt-1">{{ form.errors.nama_trip }}</p>
                        </div>

                        <div>
                            <label for="wilayah_tujuan" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Wilayah / kota tujuan <span class="text-coral-text">*</span>
                            </label>
                            <input
                                id="wilayah_tujuan"
                                v-model="form.wilayah_tujuan"
                                list="kotas-list"
                                type="text"
                                placeholder="Pilih atau ketik kota tujuan"
                                :class="inputClass"
                            />
                            <datalist id="kotas-list">
                                <option v-for="k in kotas" :key="k" :value="k" />
                            </datalist>
                            <p v-if="form.errors.wilayah_tujuan" class="text-xs text-coral-text mt-1">{{ form.errors.wilayah_tujuan }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Tanggal mulai <span class="text-coral-text">*</span>
                            </label>
                            <input id="tanggal_mulai" v-model="form.tanggal_mulai" type="date" :class="inputClass" />
                            <p v-if="form.errors.tanggal_mulai" class="text-xs text-coral-text mt-1">{{ form.errors.tanggal_mulai }}</p>
                        </div>

                        <div>
                            <label for="durasi_hari" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Durasi (hari) <span class="text-coral-text">*</span>
                            </label>
                            <input id="durasi_hari" v-model.number="form.durasi_hari" type="number" min="1" max="30" :class="[inputClass, 'tabular-nums']" />
                            <p v-if="form.errors.durasi_hari" class="text-xs text-coral-text mt-1">{{ form.errors.durasi_hari }}</p>
                        </div>

                        <div>
                            <label for="jumlah_peserta" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Jumlah peserta <span class="text-coral-text">*</span>
                            </label>
                            <input id="jumlah_peserta" v-model.number="form.jumlah_peserta" type="number" min="1" max="100" :class="[inputClass, 'tabular-nums']" />
                            <p v-if="form.errors.jumlah_peserta" class="text-xs text-coral-text mt-1">{{ form.errors.jumlah_peserta }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="anggaran" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Batas anggaran perjalanan (Rp) <span class="text-coral-text">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-sm font-semibold text-brand-muted" aria-hidden="true">Rp</span>
                            <input id="anggaran" v-model.number="form.anggaran" type="number" min="0" step="10000" :class="[inputClass, 'pl-10 tabular-nums']" />
                        </div>
                        <p class="text-xs text-brand-muted mt-1">
                            Dipakai untuk menghitung perbandingan estimasi biaya terhadap sisa anggaran Anda.
                        </p>
                        <p v-if="form.errors.anggaran" class="text-xs text-coral-text mt-1">{{ form.errors.anggaran }}</p>
                    </div>

                    <!-- Category Preferences -->
                    <fieldset class="pt-5 border-t border-brand-dark/10">
                        <legend class="text-xs font-semibold text-brand-dark mb-1">
                            Preferensi kategori / minat (opsional)
                        </legend>
                        <p class="text-xs text-brand-muted mb-3">
                            Pilih satu atau lebih kategori minat agar rekomendasi destinasi lebih tepat.
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            <button
                                v-for="cat in categories"
                                :key="cat.id"
                                type="button"
                                :aria-pressed="form.kategori_ids.includes(cat.id)"
                                @click="toggleCategory(cat.id)"
                                :class="[
                                    'px-3.5 py-2.5 rounded-lg border text-xs font-semibold flex items-center justify-between transition-colors min-h-[44px]',
                                    form.kategori_ids.includes(cat.id)
                                        ? 'border-brand-primary bg-teal-light text-brand-dark'
                                        : 'border-brand-dark/15 bg-white text-brand-text hover:bg-brand-bg',
                                ]"
                            >
                                <span>{{ cat.nama_kategori }}</span>
                                <span
                                    :class="[
                                        'w-5 h-5 rounded flex items-center justify-center shrink-0',
                                        form.kategori_ids.includes(cat.id) ? 'bg-brand-primary text-white' : 'border border-brand-dark/25',
                                    ]"
                                    aria-hidden="true"
                                >
                                    <Check v-if="form.kategori_ids.includes(cat.id)" class="w-3.5 h-3.5" />
                                </span>
                            </button>
                        </div>
                    </fieldset>

                    <!-- Cost Allocation Priority -->
                    <fieldset class="pt-5 border-t border-brand-dark/10">
                        <legend class="text-xs font-semibold text-brand-dark mb-3">
                            Prioritas alokasi biaya <span class="text-coral-text">*</span>
                        </legend>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <button
                                v-for="opt in priorityOptions"
                                :key="opt.value"
                                type="button"
                                :aria-pressed="form.prioritas_biaya === opt.value"
                                @click="form.prioritas_biaya = opt.value"
                                :class="[
                                    'p-4 rounded-xl border text-left transition-colors',
                                    form.prioritas_biaya === opt.value
                                        ? 'border-brand-primary bg-teal-light/70'
                                        : 'border-brand-dark/15 bg-white hover:bg-brand-bg',
                                ]"
                            >
                                <p class="text-sm font-semibold text-brand-dark">{{ opt.label }}</p>
                                <p class="text-xs text-brand-muted mt-1 leading-relaxed">{{ opt.desc }}</p>
                            </button>
                        </div>
                    </fieldset>

                    <!-- Actions -->
                    <div class="pt-5 border-t border-brand-dark/10 flex flex-wrap items-center justify-end gap-3">
                        <Link
                            href="/trips"
                            class="px-5 py-2.5 rounded-lg border border-brand-dark/15 text-sm font-semibold text-brand-dark hover:bg-brand-bg min-h-[44px] inline-flex items-center"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 bg-brand-primary hover:bg-brand-primaryHover active:bg-brand-dark text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50 min-h-[44px]"
                        >
                            {{ form.processing ? 'Menyusun itinerary...' : 'Susun Itinerary & Hitung Biaya' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
