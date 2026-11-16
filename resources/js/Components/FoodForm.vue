<script setup>
import { computed, ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import MapPicker from '@/Components/MapPicker.vue'
import { Save, ArrowLeft, AlertCircle, ImageIcon } from 'lucide-vue-next'

const props = defineProps({
    food: { type: Object, default: null },
})

const isEditing = computed(() => !!props.food)

const form = useForm({
    nama: props.food?.nama ?? '',
    deskripsi: props.food?.deskripsi ?? '',
    alamat: props.food?.alamat ?? '',
    kota: props.food?.kota ?? '',
    provinsi: props.food?.provinsi ?? '',
    latitude: props.food?.latitude ?? '',
    longitude: props.food?.longitude ?? '',
    jenis: props.food?.jenis ?? 'Restoran',
    jam_operasional: props.food?.jam_operasional ?? '',
    harga_min: props.food?.harga_min ?? 0,
    harga_max: props.food?.harga_max ?? 0,
    rating: props.food?.rating ?? 0,
    jumlah_ulasan: props.food?.jumlah_ulasan ?? 0,
    status: props.food?.status ?? 'aktif',
    image: null,
})

const imagePreview = ref(props.food?.image ?? null)

function onImageChange(event) {
    const file = event.target.files[0]
    form.image = file ?? null
    if (file) imagePreview.value = URL.createObjectURL(file)
}

function submit() {
    const options = { forceFormData: true, preserveScroll: true }
    if (isEditing.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.foods.update', props.food.id), options)
    } else {
        form.post(route('admin.foods.store'), options)
    }
}

const fieldClass =
    'w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm text-[#003527] placeholder-[#404945]/40 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all'
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.foods.index')" class="p-2.5 rounded-xl border border-emerald-100 text-[#404945]/70 hover:bg-emerald-50 hover:text-[#174c3c] transition-colors">
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="font-heading font-bold text-3xl text-[#003527] tracking-tight">
                        {{ isEditing ? 'Edit Tempat Makan' : 'Tambah Tempat Makan Baru' }}
                    </h2>
                    <p class="text-sm text-[#404945]/70 mt-1">Lengkapi detail tempat makan dan kisaran harga</p>
                </div>
            </div>
            <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl transition-all shadow-lg shadow-emerald-200 gap-2 disabled:opacity-50 cursor-pointer">
                <Save class="w-4 h-4" />
                {{ form.processing ? 'Menyimpan...' : 'Simpan Tempat Makan' }}
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-7 space-y-5">
                    <h3 class="font-heading font-bold text-lg text-[#003527]">Informasi Dasar</h3>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Nama Tempat Makan <span class="text-rose-500">*</span></label>
                        <input v-model="form.nama" type="text" :class="fieldClass" placeholder="Contoh: Warung Nasi Ibu Made" />
                        <p v-if="form.errors.nama" class="text-xs text-rose-500 mt-1.5">{{ form.errors.nama }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Deskripsi <span class="text-rose-500">*</span></label>
                        <textarea v-model="form.deskripsi" rows="5" :class="[fieldClass, 'resize-none']" placeholder="Deskripsikan menu andalan, suasana, dan keunikan tempat makan..."></textarea>
                        <p v-if="form.errors.deskripsi" class="text-xs text-rose-500 mt-1.5">{{ form.errors.deskripsi }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-7 space-y-5">
                    <h3 class="font-heading font-bold text-lg text-[#003527]">Alamat & Koordinat</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                            <input v-model="form.kota" type="text" :class="fieldClass" placeholder="Contoh: Yogyakarta" />
                            <p v-if="form.errors.kota" class="text-xs text-rose-500 mt-1.5">{{ form.errors.kota }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Provinsi</label>
                            <input v-model="form.provinsi" type="text" :class="fieldClass" placeholder="Contoh: DI Yogyakarta" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea v-model="form.alamat" rows="2" :class="[fieldClass, 'resize-none']" placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan..."></textarea>
                        <p v-if="form.errors.alamat" class="text-xs text-rose-500 mt-1.5">{{ form.errors.alamat }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Titik Koordinat Peta</label>
                        <MapPicker v-model:latitude="form.latitude" v-model:longitude="form.longitude" />
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-7 space-y-4">
                    <h3 class="font-heading font-bold text-lg text-[#003527]">Foto Utama</h3>
                    <div v-if="imagePreview" class="w-full aspect-video rounded-2xl overflow-hidden border border-emerald-100 bg-emerald-50">
                        <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover" />
                    </div>
                    <div v-else class="w-full aspect-video rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/50 flex flex-col items-center justify-center gap-2 text-emerald-300">
                        <ImageIcon class="w-10 h-10" />
                        <span class="text-xs">Belum ada foto</span>
                    </div>
                    <input type="file" accept="image/jpeg,image/png,image/webp" @change="onImageChange" class="block w-full text-sm text-[#404945]/70 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 cursor-pointer" />
                    <p class="text-xs text-[#404945]/60">Format JPG, PNG, WEBP. Maks 2MB.</p>
                    <p v-if="form.errors.image" class="text-xs text-rose-500">{{ form.errors.image }}</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-7 space-y-5">
                    <h3 class="font-heading font-bold text-lg text-[#003527]">Jenis & Harga</h3>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Jenis Tempat Makan <span class="text-rose-500">*</span></label>
                        <input v-model="form.jenis" type="text" :class="fieldClass" placeholder="Restoran / Warung / Cafe" />
                        <p v-if="form.errors.jenis" class="text-xs text-rose-500 mt-1.5">{{ form.errors.jenis }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Jam Operasional</label>
                        <input v-model="form.jam_operasional" type="text" :class="fieldClass" placeholder="08:00 - 22:00 WIB" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Harga Termurah <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-sm text-[#404945]/50">Rp</span>
                            <input v-model="form.harga_min" type="number" min="0" step="0.01" :class="[fieldClass, 'pl-10']" placeholder="0" />
                        </div>
                        <p v-if="form.errors.harga_min" class="text-xs text-rose-500 mt-1.5">{{ form.errors.harga_min }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Harga Termahal <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-sm text-[#404945]/50">Rp</span>
                            <input v-model="form.harga_max" type="number" min="0" step="0.01" :class="[fieldClass, 'pl-10']" placeholder="0" />
                        </div>
                        <p v-if="form.errors.harga_max" class="text-xs text-rose-500 mt-1.5">{{ form.errors.harga_max }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-7 space-y-5">
                    <h3 class="font-heading font-bold text-lg text-[#003527]">Rating & Status</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Rating</label>
                            <input v-model="form.rating" type="number" min="0" max="5" step="0.1" :class="fieldClass" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Ulasan</label>
                            <input v-model="form.jumlah_ulasan" type="number" min="0" step="1" :class="fieldClass" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">Status Publikasi <span class="text-rose-500">*</span></label>
                        <select v-model="form.status" :class="[fieldClass, 'cursor-pointer']">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="bg-emerald-50/70 border border-emerald-100 rounded-2xl p-4 flex gap-2.5">
                    <AlertCircle class="w-4 h-4 text-emerald-700 shrink-0 mt-0.5" />
                    <p class="text-xs text-emerald-800/80 leading-relaxed">
                        Kolom bertanda <span class="text-rose-500 font-bold">*</span> wajib diisi.
                    </p>
                </div>
            </div>
        </div>
    </form>
</template>