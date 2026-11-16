<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import MapPicker from '@/Components/MapPicker.vue'
import { Save, ArrowLeft, AlertCircle } from 'lucide-vue-next'

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    destination: {
        type: Object,
        default: null,
    },
})

const isEditing = computed(() => !!props.destination)

const form = useForm({
    category_id: props.destination?.category_id ?? '',
    nama: props.destination?.nama ?? '',
    deskripsi: props.destination?.deskripsi ?? '',
    alamat: props.destination?.alamat ?? '',
    kota: props.destination?.kota ?? '',
    latitude: props.destination?.latitude ?? '',
    longitude: props.destination?.longitude ?? '',
    jam_operasional: props.destination?.jam_operasional ?? '',
    estimasi_harga: props.destination?.estimasi_harga ?? 0,
    jenis_harga: props.destination?.jenis_harga ?? 'per_orang',
    status: props.destination?.status ?? 'aktif',
    photos: [],
})

const photoPreviews = []

function onFilesChange(event) {
    const files = Array.from(event.target.files)
    form.photos = files
    photoPreviews.length = 0
    files.forEach((file) => {
        photoPreviews.push({ name: file.name, url: URL.createObjectURL(file) })
    })
}

function submit() {
    if (isEditing.value) {
        form.transform((data) => ({ ...data })).put(route('admin.destinations.update', props.destination.id), {
            forceFormData: true,
            preserveScroll: true,
        })
    } else {
        form.post(route('admin.destinations.store'), {
            forceFormData: true,
            preserveScroll: true,
        })
    }
}

const fieldClass =
    'w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all'
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <Link
                    :href="route('admin.destinations.index')"
                    class="p-2 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors"
                >
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                        {{ isEditing ? 'Edit Destinasi' : 'Tambah Destinasi Baru' }}
                    </h2>
                    <p class="text-sm text-gray-500">Lengkapi detail destinasi, koordinat, dan estimasi harga</p>
                </div>
            </div>
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl transition-all shadow-sm shadow-emerald-200 gap-2 disabled:opacity-50 cursor-pointer"
            >
                <Save class="w-4 h-4" />
                {{ form.processing ? 'Menyimpan...' : 'Simpan Destinasi' }}
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-900">Informasi Dasar</h3>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Nama Destinasi <span class="text-rose-500">*</span>
                        </label>
                        <input v-model="form.nama" type="text" :class="fieldClass" placeholder="Contoh: Candi Borobudur" />
                        <p v-if="form.errors.nama" class="text-xs text-rose-500 mt-1.5">{{ form.errors.nama }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select v-model="form.category_id" :class="[fieldClass, 'cursor-pointer']">
                            <option value="" disabled>Pilih kategori</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nama_kategori }}</option>
                        </select>
                        <p v-if="form.errors.category_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.category_id }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Deskripsi <span class="text-rose-500">*</span>
                        </label>
                        <textarea v-model="form.deskripsi" rows="5" :class="[fieldClass, 'resize-none']" placeholder="Deskripsikan keunikan, daya tarik, dan fasilitas destinasi..."></textarea>
                        <p v-if="form.errors.deskripsi" class="text-xs text-rose-500 mt-1.5">{{ form.errors.deskripsi }}</p>
                    </div>
                </div>

                <!-- Location -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-900">Alamat & Koordinat</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                                Kota / Kabupaten <span class="text-rose-500">*</span>
                            </label>
                            <input v-model="form.kota" type="text" :class="fieldClass" placeholder="Contoh: Yogyakarta" />
                            <p v-if="form.errors.kota" class="text-xs text-rose-500 mt-1.5">{{ form.errors.kota }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Jam Operasional</label>
                            <input v-model="form.jam_operasional" type="text" :class="fieldClass" placeholder="08:00 - 17:00 WIB" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Alamat Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea v-model="form.alamat" rows="2" :class="[fieldClass, 'resize-none']" placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan..."></textarea>
                        <p v-if="form.errors.alamat" class="text-xs text-rose-500 mt-1.5">{{ form.errors.alamat }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Titik Koordinat Peta</label>
                        <MapPicker v-model:latitude="form.latitude" v-model:longitude="form.longitude" />
                        <p v-if="form.errors.latitude || form.errors.longitude" class="text-xs text-rose-500 mt-1.5">
                            {{ form.errors.latitude || form.errors.longitude }}
                        </p>
                    </div>
                </div>

                <!-- Photos -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-900">Galeri Foto {{ isEditing ? '(Tambahan)' : '' }}</h3>
                    <p class="text-xs text-gray-500">Format JPG, PNG, WEBP. Maks 2MB per file.</p>
                    <input
                        type="file"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        @change="onFilesChange"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                    />
                    <p v-if="form.errors.photos" class="text-xs text-rose-500">{{ form.errors.photos }}</p>

                    <div v-if="photoPreviews.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        <div v-for="(photo, i) in photoPreviews" :key="i" class="aspect-square rounded-xl overflow-hidden border border-gray-100">
                            <img :src="photo.url" :alt="photo.name" class="w-full h-full object-cover" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-5">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-900">Harga & Status</h3>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Estimasi Harga <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm text-gray-400">Rp</span>
                            <input v-model="form.estimasi_harga" type="number" min="0" step="0.01" :class="[fieldClass, 'pl-9']" placeholder="0" />
                        </div>
                        <p v-if="form.errors.estimasi_harga" class="text-xs text-rose-500 mt-1.5">{{ form.errors.estimasi_harga }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Jenis Harga <span class="text-rose-500">*</span>
                        </label>
                        <select v-model="form.jenis_harga" :class="[fieldClass, 'cursor-pointer']">
                            <option value="per_orang">Per Orang</option>
                            <option value="per_kelompok">Per Kelompok</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Status Publikasi <span class="text-rose-500">*</span>
                        </label>
                        <select v-model="form.status" :class="[fieldClass, 'cursor-pointer']">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-2.5">
                    <AlertCircle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Kolom bertanda <span class="text-rose-500 font-bold">*</span> wajib diisi. Koordinat direkomendasikan untuk fitur peta wisatawan.
                    </p>
                </div>
            </div>
        </div>
    </form>
</template>
