<script setup>
import { ref } from 'vue'
import { useForm, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ArrowLeft, MapPin, Plus, Check } from 'lucide-vue-next'

const props = defineProps({
    destinations: {
        type: Array,
        required: true,
    },
    selectedDestinationId: {
        type: Number,
        default: null,
    },
})

const form = useForm({
    judul: '',
    deskripsi: '',
    destination_id: props.selectedDestinationId || '',
    destination_ids: props.selectedDestinationId ? [props.selectedDestinationId] : [],
})

function toggleExtraDestination(id) {
    const idx = form.destination_ids.indexOf(id)
    if (idx > -1) {
        form.destination_ids.splice(idx, 1)
    } else {
        form.destination_ids.push(id)
    }
}

function submit() {
    form.post(route('trips.store'))
}

const inputClass =
    'w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all'
</script>

<template>
    <Head title="Buat Trip Baru - NUSA WANDER" />

    <PublicLayout>
        <div class="max-w-4xl mx-auto px-6 py-12">
            <!-- Back button -->
            <Link :href="route('trips.index')" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#404945]/70 hover:text-[#003527] transition-colors mb-6">
                <ArrowLeft class="w-4 h-4" /> Kembali ke Trip Saya
            </Link>

            <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-8 md:p-10">
                <div class="mb-8">
                    <h1 class="font-heading font-bold text-2xl md:text-3xl text-[#003527] tracking-tight">
                        Rencanakan Perjalanan Baru
                    </h1>
                    <p class="text-sm text-[#404945]/70 mt-1">
                        Tentukan destinasi utama dan destinasi pendukung untuk jadwal liburanmu.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                            Judul Trip <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.judul"
                            type="text"
                            placeholder="Contoh: Liburan Seru Akhir Tahun di Jogja"
                            :class="inputClass"
                        />
                        <p v-if="form.errors.judul" class="text-xs text-rose-500 mt-1.5">{{ form.errors.judul }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                            Catatan / Deskripsi Liburan (Opsional)
                        </label>
                        <textarea
                            v-model="form.deskripsi"
                            rows="3"
                            placeholder="Tuliskan tujuan perjalanan, persiapan, atau teman yang ikut serta..."
                            :class="[inputClass, 'resize-none']"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                            Destinasi Utama <span class="text-rose-500">*</span>
                        </label>
                        <select v-model="form.destination_id" :class="[inputClass, 'cursor-pointer']">
                            <option value="" disabled>Pilih destinasi utama</option>
                            <option v-for="d in destinations" :key="d.id" :value="d.id">
                                {{ d.nama }} — {{ d.kota }} (Rp {{ Number(d.estimasi_harga).toLocaleString('id-ID') }})
                            </option>
                        </select>
                        <p v-if="form.errors.destination_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.destination_id }}</p>
                    </div>

                    <!-- Additional Destinations Selection -->
                    <div class="pt-4 border-t border-emerald-100">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                            Tambahkan Destinasi Lainnya Sekaligus (Opsional)
                        </label>
                        <p class="text-xs text-[#404945]/60 mb-3">
                            Pilih beberapa destinasi sekaligus untuk langsung dimasukkan ke dalam rangkaian itinerary:
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-1 border border-emerald-50 rounded-2xl">
                            <div
                                v-for="dest in destinations"
                                :key="dest.id"
                                @click="toggleExtraDestination(dest.id)"
                                :class="[
                                    'p-3 rounded-xl border text-xs flex items-center justify-between cursor-pointer transition-all',
                                    form.destination_ids.includes(dest.id)
                                        ? 'border-emerald-600 bg-emerald-50/70 text-[#003527] font-semibold'
                                        : 'border-emerald-100 hover:bg-emerald-25 text-gray-700',
                                ]"
                            >
                                <div class="min-w-0 pr-2">
                                    <p class="truncate">{{ dest.nama }}</p>
                                    <p class="text-[11px] text-[#404945]/60">{{ dest.kota }} &bull; Rp {{ Number(dest.estimasi_harga).toLocaleString('id-ID') }}</p>
                                </div>
                                <div
                                    :class="[
                                        'w-5 h-5 rounded-md flex items-center justify-center shrink-0',
                                        form.destination_ids.includes(dest.id) ? 'bg-emerald-600 text-white' : 'border border-gray-300',
                                    ]"
                                >
                                    <Check v-if="form.destination_ids.includes(dest.id)" class="w-3.5 h-3.5" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-6 border-t border-emerald-100 flex items-center justify-end gap-3">
                        <Link
                            :href="route('trips.index')"
                            class="px-5 py-3 rounded-2xl border border-emerald-100 text-sm font-semibold text-[#404945] hover:bg-emerald-50 transition-colors"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-7 py-3 bg-[#174c3c] hover:bg-[#003527] text-white text-sm font-semibold rounded-2xl shadow-lg shadow-emerald-200 transition-all disabled:opacity-50 cursor-pointer"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Buat Rencana & Atur Jadwal' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>