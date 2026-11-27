<script setup>
import { ref, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DestinationForm from '@/Components/DestinationForm.vue'
import {
    ImageIcon, Trash2, UploadCloud, Loader2, ArrowUp, ArrowDown, Camera, Check
} from 'lucide-vue-next'

const props = defineProps({
    destination: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
})

const images = ref([...(props.destination.images || [])])
const captionEdits = ref({})

watch(
    () => props.destination.images,
    (newImages) => {
        images.value = [...(newImages || [])]
        const map = {}
        images.value.forEach((img) => {
            map[img.id] = img.keterangan || ''
        })
        captionEdits.value = map
    },
    { deep: true, immediate: true }
)

const uploadForm = useForm({
    destination_id: props.destination.id,
    photos: [],
    keterangan: '',
})

const isUploading = ref(false)
const fileInput = ref(null)

function onFilesChange(event) {
    uploadForm.photos = Array.from(event.target.files)
}

function uploadImages() {
    if (uploadForm.photos.length === 0) return
    isUploading.value = true
    uploadForm.post('/admin/destination-images', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset('photos', 'keterangan')
            if (fileInput.value) fileInput.value.value = ''
            router.reload({ only: ['destination'] })
        },
        onFinish: () => {
            isUploading.value = false
        },
    })
}

function updateOrder(image, newOrder) {
    if (newOrder < 1) return
    router.patch(`/admin/destination-images/${image.id}/order`, {
        urutan_tampil: newOrder,
    }, {
        preserveScroll: true,
    })
}

function updateCaption(image) {
    router.patch(`/admin/destination-images/${image.id}`, {
        keterangan: captionEdits.value[image.id] ?? '',
    }, {
        preserveScroll: true,
    })
}

function deleteImage(image) {
    if (!confirm(`Hapus foto "${image.keterangan || 'tanpa keterangan'}"?`)) return
    router.delete(`/admin/destination-images/${image.id}`, {
        preserveScroll: true,
    })
}
</script>

<template>
    <AdminLayout title="Edit Destinasi &amp; Foto">
        <DestinationForm :destination="destination" :categories="categories" />

        <!-- Gallery Management -->
        <section class="mt-6 bg-white rounded-xl border border-brand-dark/10 p-6 space-y-5">
            <div>
                <h2 class="font-heading font-bold text-base text-brand-dark flex items-center gap-2">
                    <Camera class="w-4 h-4 text-brand-primary" aria-hidden="true" />
                    Kelola foto destinasi
                </h2>
                <p class="text-xs text-brand-muted mt-1">
                    Foto dengan urutan terkecil tampil pertama sebagai gambar utama di katalog.
                </p>
            </div>

            <div class="bg-brand-bg rounded-xl p-4 border border-dashed border-brand-dark/20">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div class="sm:col-span-2">
                        <label for="upload-photos" class="block text-xs font-semibold text-brand-dark mb-1.5">Unggah foto baru</label>
                        <input
                            id="upload-photos"
                            ref="fileInput"
                            type="file"
                            multiple
                            accept="image/jpeg,image/png,image/webp"
                            @change="onFilesChange"
                            class="block w-full text-sm text-brand-muted file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-input file:text-brand-dark hover:file:bg-brand-dark hover:file:text-white"
                        />
                        <p class="text-xs text-brand-muted mt-1">Maksimal 2MB per file, format JPG/PNG/WEBP.</p>
                    </div>
                    <div>
                        <label for="upload-caption" class="block text-xs font-semibold text-brand-dark mb-1.5">Keterangan (opsional)</label>
                        <input
                            id="upload-caption"
                            v-model="uploadForm.keterangan"
                            type="text"
                            placeholder="Misal: Area Candi Utara"
                            class="w-full px-3 py-2 bg-white border border-brand-dark/15 rounded-lg text-xs min-h-[44px]"
                        />
                    </div>
                </div>
                <button
                    type="button"
                    @click="uploadImages"
                    :disabled="isUploading || uploadForm.photos.length === 0"
                    class="mt-3 inline-flex items-center gap-2 px-4 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white text-sm font-semibold rounded-lg disabled:opacity-50 min-h-[44px]"
                >
                    <Loader2 v-if="isUploading" class="w-4 h-4 animate-spin" aria-hidden="true" />
                    <UploadCloud v-else class="w-4 h-4" aria-hidden="true" />
                    {{ isUploading ? 'Mengunggah...' : `Unggah ${uploadForm.photos.length} Foto` }}
                </button>
                <p v-if="uploadForm.errors['photos.0'] || uploadForm.errors.photos" class="text-xs text-coral-text mt-2">
                    {{ uploadForm.errors['photos.0'] || uploadForm.errors.photos }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="(img, index) in images"
                    :key="img.id"
                    class="bg-white rounded-xl border border-brand-dark/10 overflow-hidden"
                >
                    <div class="aspect-video bg-brand-input relative overflow-hidden">
                        <img :src="img.url_gambar" :alt="img.keterangan || `Foto ${index + 1}`" class="w-full h-full object-cover" />
                        <span v-if="index === 0" class="absolute top-2 left-2 px-2 py-0.5 bg-brand-dark text-white text-[11px] font-semibold rounded-md">
                            Foto Utama
                        </span>
                        <button
                            type="button"
                            @click="deleteImage(img)"
                            :aria-label="`Hapus foto ${index + 1}`"
                            class="absolute top-2 right-2 p-2 bg-white/95 hover:bg-coral-light text-brand-dark hover:text-coral-text rounded-lg min-h-[36px] min-w-[36px] flex items-center justify-center"
                            title="Hapus foto"
                        >
                            <Trash2 class="w-4 h-4" aria-hidden="true" />
                        </button>
                    </div>
                    <div class="p-3 space-y-2">
                        <div class="flex items-center gap-1.5">
                            <input
                                v-model="captionEdits[img.id]"
                                :aria-label="`Keterangan foto ${index + 1}`"
                                type="text"
                                placeholder="Keterangan foto..."
                                class="flex-1 px-2.5 py-1.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-xs min-h-[38px]"
                            />
                            <button
                                type="button"
                                @click="updateCaption(img)"
                                :aria-label="`Simpan keterangan foto ${index + 1}`"
                                class="p-2 bg-brand-input hover:bg-brand-primary hover:text-white text-brand-dark rounded-lg transition-colors min-h-[38px] min-w-[38px] flex items-center justify-center"
                                title="Simpan keterangan"
                            >
                                <Check class="w-3.5 h-3.5" aria-hidden="true" />
                            </button>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-brand-muted tabular-nums">Urutan tampil: <strong class="text-brand-dark">{{ img.urutan_tampil }}</strong></span>
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    @click="updateOrder(img, img.urutan_tampil - 1)"
                                    :disabled="img.urutan_tampil <= 1"
                                    aria-label="Naikkan urutan foto"
                                    class="p-1.5 text-brand-muted hover:text-brand-dark hover:bg-brand-bg rounded-md disabled:opacity-30 min-h-[36px] min-w-[36px] flex items-center justify-center"
                                >
                                    <ArrowUp class="w-3.5 h-3.5" aria-hidden="true" />
                                </button>
                                <button
                                    type="button"
                                    @click="updateOrder(img, img.urutan_tampil + 1)"
                                    aria-label="Turunkan urutan foto"
                                    class="p-1.5 text-brand-muted hover:text-brand-dark hover:bg-brand-bg rounded-md min-h-[36px] min-w-[36px] flex items-center justify-center"
                                >
                                    <ArrowDown class="w-3.5 h-3.5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="images.length === 0" class="text-center py-10 bg-brand-bg rounded-xl border border-dashed border-brand-dark/15">
                <ImageIcon class="w-9 h-9 text-brand-muted mx-auto mb-2" aria-hidden="true" />
                <p class="text-sm text-brand-muted">Belum ada foto untuk destinasi ini.</p>
            </div>
        </section>
    </AdminLayout>
</template>
