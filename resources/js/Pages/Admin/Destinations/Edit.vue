<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DestinationForm from '@/Components/DestinationForm.vue'
import {
    ImageIcon, Trash2, UploadCloud, Loader2, X, AlertCircle, ArrowUp, ArrowDown, Camera
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

const images = ref(props.destination.images)

const uploadForm = useForm({
    destination_id: props.destination.id,
    photos: [],
    keterangan: '',
})

const isUploading = ref(false)

function onFilesChange(event) {
    uploadForm.photos = Array.from(event.target.files)
}

function uploadImages() {
    if (uploadForm.photos.length === 0) return
    isUploading.value = true
    uploadForm.post(route('admin.destination-images.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset()
            // Refresh images list from server
            router.reload({ only: ['destination'] })
        },
        onFinish: () => {
            isUploading.value = false
        },
    })
}

function updateOrder(image, newOrder) {
    if (newOrder < 1) return
    router.patch(route('admin.destination-images.order', image.id), {
        urutan_tampil: newOrder,
    }, {
        preserveScroll: true,
    })
}

function deleteImage(image) {
    if (!confirm(`Hapus foto "${image.keterangan || 'untitled'}"?`)) return
    router.delete(route('admin.destination-images.destroy', image.id), {
        preserveScroll: true,
        onSuccess: () => {
            images.value = images.value.filter((img) => img.id !== image.id)
        },
    })
}
</script>

<template>
    <AdminLayout>
        <DestinationForm :destination="destination" :categories="categories" />

        <!-- Gallery Management Section -->
        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <Camera class="w-4 h-4 text-emerald-600" />
                        Galeri Foto Destinasi
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Foto berurutan ke-1 akan menjadi thumbnail utama pada tabel katalog
                    </p>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="bg-gray-50 rounded-xl p-4 border border-dashed border-gray-300">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Upload Foto</label>
                        <input
                            type="file"
                            multiple
                            accept="image/jpeg,image/png,image/webp"
                            @change="onFilesChange"
                            class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 cursor-pointer"
                        />
                        <p class="text-[11px] text-gray-400 mt-1">Maks 2MB per file, format JPG/PNG/WEBP</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Keterangan (Opsional)</label>
                        <input
                            v-model="uploadForm.keterangan"
                            type="text"
                            placeholder="Contoh: Area Candi Utara"
                            class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>
                </div>
                <button
                    @click="uploadImages"
                    :disabled="isUploading || uploadForm.photos.length === 0"
                    class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm disabled:opacity-50 cursor-pointer"
                >
                    <Loader2 v-if="isUploading" class="w-4 h-4 animate-spin" />
                    <UploadCloud v-else class="w-4 h-4" />
                    {{ isUploading ? 'Mengunggah...' : `Unggah ${uploadForm.photos.length} Foto` }}
                </button>
                <p v-if="uploadForm.errors.photos" class="text-xs text-rose-500 mt-2">
                    {{ Array.isArray(uploadForm.errors.photos) ? uploadForm.errors.photos.join(', ') : uploadForm.errors.photos }}
                </p>
            </div>

            <!-- Images Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="(img, index) in images"
                    :key="img.id"
                    class="group relative bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden"
                >
                    <div class="aspect-video bg-gray-100 relative overflow-hidden">
                        <img
                            :src="img.url_gambar"
                            :alt="img.keterangan || `Foto ${index + 1}`"
                            class="w-full h-full object-cover"
                        />
                        <!-- Thumbnail badge -->
                        <span
                            v-if="img.urutan_tampil === 1"
                            class="absolute top-2 left-2 px-2 py-0.5 bg-emerald-600 text-white text-[10px] font-bold rounded-md uppercase"
                        >
                            Thumbnail Utama
                        </span>
                        <!-- Delete button -->
                        <button
                            @click="deleteImage(img)"
                            class="absolute top-2 right-2 p-1.5 bg-white/90 hover:bg-rose-50 text-gray-500 hover:text-rose-600 rounded-lg shadow-sm opacity-0 group-hover:opacity-100 transition-all cursor-pointer"
                            title="Hapus foto"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="p-3">
                        <p class="text-xs text-gray-700 font-medium truncate">{{ img.keterangan || `Foto ${index + 1}` }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[11px] text-gray-400">Urutan: {{ img.urutan_tampil }}</span>
                            <div class="flex items-center gap-1">
                                <button
                                    @click="updateOrder(img, img.urutan_tampil - 1)"
                                    class="p-1 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-colors cursor-pointer"
                                    title="Naikkan urutan"
                                >
                                    <ArrowUp class="w-3.5 h-3.5" />
                                </button>
                                <button
                                    @click="updateOrder(img, img.urutan_tampil + 1)"
                                    class="p-1 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-colors cursor-pointer"
                                    title="Turunkan urutan"
                                >
                                    <ArrowDown class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="images.length === 0" class="text-center py-12 bg-gray-50 rounded-xl">
                <ImageIcon class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                <p class="text-sm text-gray-500">Belum ada foto untuk destinasi ini.</p>
            </div>
        </div>
    </AdminLayout>
</template>
