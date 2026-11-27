<script setup>
import { computed, ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import MapPicker from '@/Components/MapPicker.vue'
import { Save, ArrowLeft, AlertCircle, X } from 'lucide-vue-next'

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
    estimasi_harga: props.destination?.estimasi_harga ?? '',
    jenis_harga: props.destination?.jenis_harga ?? 'per_orang',
    status: props.destination?.status ?? 'aktif',
    photos: [],
})

const photoPreviews = ref([])

function onFilesChange(event) {
    const files = Array.from(event.target.files)
    form.photos = files
    photoPreviews.value = []
    files.forEach((file) => {
        photoPreviews.value.push({ name: file.name, url: URL.createObjectURL(file) })
    })
}

function removePreview(index) {
    URL.revokeObjectURL(photoPreviews.value[index].url)
    photoPreviews.value.splice(index, 1)
    form.photos.splice(index, 1)
    const input = document.querySelector('input[type="file"]')
    if (input) input.value = ''
}

function submit() {
    const normalizePrice = (val) => (val === '' || val === null ? null : val)

    if (isEditing.value) {
        form.transform(({ photos, ...data }) => ({
            ...data,
            estimasi_harga: normalizePrice(data.estimasi_harga),
        })).put(`/admin/destinations/${props.destination.id}`, {
            preserveScroll: true,
        })
    } else {
        form.transform((data) => ({
            ...data,
            estimasi_harga: normalizePrice(data.estimasi_harga),
        })).post('/admin/destinations', {
            forceFormData: true,
            preserveScroll: true,
        })
    }
}

const fieldClass =
    'w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark placeholder-brand-muted/60 focus:bg-white focus:border-brand-primary transition-colors min-h-[44px]'

function fieldClassFor(key) {
    return form.errors[key]
        ? fieldClass.replace('border-brand-dark/15', 'border-coral-text')
        : fieldClass
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <Link
                    href="/admin/destinations"
                    aria-label="Kembali ke daftar destinasi"
                    class="p-2.5 rounded-lg border border-brand-dark/15 bg-white text-brand-dark hover:bg-brand-bg min-h-[44px] min-w-[44px] inline-flex items-center justify-center"
                >
                    <ArrowLeft class="w-4 h-4" aria-hidden="true" />
                </Link>
                <div>
                    <h1 class="font-heading font-bold text-2xl sm:text-3xl text-brand-dark">
                        {{ isEditing ? 'Edit destinasi' : 'Tambah destinasi baru' }}
                    </h1>
                    <p class="text-sm text-brand-muted mt-0.5">Lengkapi detail destinasi, koordinat peta, dan estimasi harga.</p>
                </div>
            </div>
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors disabled:opacity-50 min-h-[44px]"
            >
                <Save class="w-4 h-4" aria-hidden="true" />
                {{ form.processing ? 'Menyimpan...' : 'Simpan Destinasi' }}
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <section class="bg-white rounded-xl border border-brand-dark/10 p-6 space-y-4">
                    <h2 class="font-heading font-bold text-base text-brand-dark">Informasi dasar</h2>

                    <div>
                        <label for="dest-nama" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Nama destinasi <span class="text-coral-text">*</span>
                        </label>
                        <input id="dest-nama" v-model="form.nama" type="text" :class="fieldClassFor('nama')" placeholder="Misal: Candi Borobudur" />
                        <p v-if="form.errors.nama" class="text-xs text-coral-text mt-1">{{ form.errors.nama }}</p>
                    </div>

                    <div>
                        <label for="dest-cat" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Kategori <span class="text-coral-text">*</span>
                        </label>
                        <select id="dest-cat" v-model="form.category_id" :class="fieldClassFor('category_id')">
                            <option value="" disabled>Pilih kategori</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nama_kategori }}</option>
                        </select>
                        <p v-if="form.errors.category_id" class="text-xs text-coral-text mt-1">{{ form.errors.category_id }}</p>
                    </div>

                    <div>
                        <label for="dest-desc" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Deskripsi <span class="text-coral-text">*</span>
                        </label>
                        <textarea id="dest-desc" v-model="form.deskripsi" rows="5" :class="[fieldClassFor('deskripsi'), 'resize-none']" placeholder="Jelaskan daya tarik dan informasi utama destinasi..."></textarea>
                        <p v-if="form.errors.deskripsi" class="text-xs text-coral-text mt-1">{{ form.errors.deskripsi }}</p>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-brand-dark/10 p-6 space-y-4">
                    <h2 class="font-heading font-bold text-base text-brand-dark">Alamat &amp; koordinat</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="dest-kota" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Kota / kabupaten <span class="text-coral-text">*</span>
                            </label>
                            <input id="dest-kota" v-model="form.kota" type="text" :class="fieldClassFor('kota')" placeholder="Misal: Yogyakarta" />
                            <p v-if="form.errors.kota" class="text-xs text-coral-text mt-1">{{ form.errors.kota }}</p>
                        </div>
                        <div>
                            <label for="dest-hours" class="block text-xs font-semibold text-brand-dark mb-1.5">Jam operasional</label>
                            <input id="dest-hours" v-model="form.jam_operasional" type="text" :class="fieldClass" placeholder="08:00 - 17:00 WIB" />
                        </div>
                    </div>

                    <div>
                        <label for="dest-alamat" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Alamat lengkap <span class="text-coral-text">*</span>
                        </label>
                        <textarea id="dest-alamat" v-model="form.alamat" rows="2" :class="[fieldClassFor('alamat'), 'resize-none']" placeholder="Alamat lengkap lokasi..."></textarea>
                        <p v-if="form.errors.alamat" class="text-xs text-coral-text mt-1">{{ form.errors.alamat }}</p>
                    </div>

                    <div>
                        <p class="block text-xs font-semibold text-brand-dark mb-1.5">Titik koordinat peta</p>
                        <MapPicker v-model:latitude="form.latitude" v-model:longitude="form.longitude" />
                        <p v-if="form.errors.latitude || form.errors.longitude" class="text-xs text-coral-text mt-1">
                            {{ form.errors.latitude || form.errors.longitude }}
                        </p>
                    </div>
                </section>

                <section v-if="!isEditing" class="bg-white rounded-xl border border-brand-dark/10 p-6 space-y-3">
                    <h2 class="font-heading font-bold text-base text-brand-dark">Foto awal (opsional)</h2>
                    <p class="text-xs text-brand-muted">Format JPG, PNG, WEBP. Maksimal 2MB per file.</p>
                    <input
                        type="file"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        @change="onFilesChange"
                        class="block w-full text-sm text-brand-muted file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-input file:text-brand-dark hover:file:bg-brand-dark hover:file:text-white"
                    />
                    <p v-if="form.errors.photos" class="text-xs text-coral-text">{{ form.errors.photos }}</p>

                    <div v-if="photoPreviews.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-3 pt-2">
                        <div v-for="(photo, i) in photoPreviews" :key="i" class="aspect-square rounded-lg overflow-hidden border border-brand-dark/10 relative">
                            <img :src="photo.url" :alt="photo.name" class="w-full h-full object-cover" />
                            <button type="button" @click="removePreview(i)" class="absolute top-1.5 right-1.5 bg-white p-1.5 rounded-md shadow-sm" aria-label="Hapus foto pilihan">
                                <X class="w-3.5 h-3.5 text-coral-text" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-5">
                <section class="bg-white rounded-xl border border-brand-dark/10 p-6 space-y-4">
                    <h2 class="font-heading font-bold text-base text-brand-dark">Estimasi harga &amp; status</h2>

                    <div>
                        <label for="dest-price" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Estimasi harga (opsional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-sm text-brand-muted" aria-hidden="true">Rp</span>
                            <input
                                id="dest-price"
                                v-model="form.estimasi_harga"
                                type="number"
                                min="0"
                                step="1"
                                :class="[fieldClassFor('estimasi_harga'), 'pl-10 tabular-nums']"
                                placeholder="Kosongkan jika belum ada data"
                            />
                        </div>
                        <p class="text-xs text-brand-muted mt-1.5">
                            Isi <strong>0</strong> jika gratis. Kosongkan jika belum tersedia agar ditandai <em>Belum dihitung</em>.
                        </p>
                        <p v-if="form.errors.estimasi_harga" class="text-xs text-coral-text mt-1">{{ form.errors.estimasi_harga }}</p>
                    </div>

                    <div>
                        <label for="dest-price-type" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Jenis harga <span class="text-coral-text">*</span>
                        </label>
                        <select id="dest-price-type" v-model="form.jenis_harga" :class="fieldClass">
                            <option value="per_orang">Per Orang</option>
                            <option value="per_kelompok">Per Kelompok</option>
                        </select>
                    </div>

                    <div>
                        <label for="dest-status" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Status publikasi <span class="text-coral-text">*</span>
                        </label>
                        <select id="dest-status" v-model="form.status" :class="fieldClass">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </section>

                <div class="bg-brand-input/60 border border-brand-dark/10 rounded-xl p-4 flex gap-2.5">
                    <AlertCircle class="w-4 h-4 text-brand-dark shrink-0 mt-0.5" aria-hidden="true" />
                    <p class="text-xs text-brand-text leading-relaxed">
                        Harga pada sistem merupakan estimasi yang dikelola Admin (bukan harga real-time) untuk perhitungan rencana perjalanan.
                    </p>
                </div>
            </div>
        </div>
    </form>
</template>
