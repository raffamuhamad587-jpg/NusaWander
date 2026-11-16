<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Plus, Edit2, Trash2, Folder, Layers, X, AlertCircle } from 'lucide-vue-next'

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
})

const isModalOpen = ref(false)
const isEditing = ref(false)
const selectedCategory = ref(null)
const isDeleteModalOpen = ref(false)
const categoryToDelete = ref(null)

const form = useForm({
    nama_kategori: '',
    deskripsi: '',
})

function openCreateModal() {
    isEditing.value = false
    selectedCategory.value = null
    form.reset()
    form.clearErrors()
    isModalOpen.value = true
}

function openEditModal(category) {
    isEditing.value = true
    selectedCategory.value = category
    form.nama_kategori = category.nama_kategori
    form.deskripsi = category.deskripsi || ''
    form.clearErrors()
    isModalOpen.value = true
}

function closeModal() {
    isModalOpen.value = false
    form.reset()
    form.clearErrors()
}

function submitForm() {
    if (isEditing.value && selectedCategory.value) {
        form.put(route('admin.categories.update', selectedCategory.value.id), {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => closeModal(),
        })
    }
}

function confirmDelete(category) {
    categoryToDelete.value = category
    isDeleteModalOpen.value = true
}

function deleteCategory() {
    if (categoryToDelete.value) {
        router.delete(route('admin.categories.destroy', categoryToDelete.value.id), {
            onSuccess: () => {
                isDeleteModalOpen.value = false
                categoryToDelete.value = null
            },
        })
    }
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Kategori Destinasi</h2>
                    <p class="text-sm text-gray-500">Kelola kelompok wisata untuk mempermudah pencarian wisatawan</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl transition-all shadow-sm shadow-emerald-200 gap-2 cursor-pointer"
                >
                    <Plus class="w-4 h-4" />
                    Tambah Kategori
                </button>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-for="cat in categories"
                    :key="cat.id"
                    class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative group"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                <Folder class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">{{ cat.nama_kategori }}</h3>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 mt-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    <Layers class="w-3 h-3" />
                                    {{ cat.destinations_count }} Destinasi
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 opacity-90 group-hover:opacity-100 transition-opacity">
                            <button
                                @click="openEditModal(cat)"
                                class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors cursor-pointer"
                                title="Edit Kategori"
                            >
                                <Edit2 class="w-4 h-4" />
                            </button>
                            <button
                                @click="confirmDelete(cat)"
                                class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                title="Hapus Kategori"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-4 line-clamp-2 leading-relaxed">
                        {{ cat.deskripsi || 'Tidak ada deskripsi untuk kategori ini.' }}
                    </p>
                </div>
            </div>

            <div v-if="categories.length === 0" class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                <Folder class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <h4 class="text-base font-semibold text-gray-700">Belum Ada Kategori</h4>
                <p class="text-sm text-gray-400 mt-1">Mulai buat kategori pertama untuk mengelompokkan destinasi wisata.</p>
                <button
                    @click="openCreateModal"
                    class="mt-4 inline-flex items-center px-4 py-2 text-sm bg-emerald-600 text-white rounded-xl hover:bg-emerald-700"
                >
                    <Plus class="w-4 h-4 mr-1.5" /> Tambah Kategori
                </button>
            </div>

            <!-- Create/Edit Modal -->
            <div
                v-if="isModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            >
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ isEditing ? 'Edit Kategori Wisata' : 'Tambah Kategori Baru' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.nama_kategori"
                                type="text"
                                placeholder="Contoh: Alam, Kuliner, Sejarah..."
                                class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                                :class="{ 'border-rose-300 bg-rose-50/30': form.errors.nama_kategori }"
                            />
                            <p v-if="form.errors.nama_kategori" class="text-xs text-rose-500 mt-1.5">
                                {{ form.errors.nama_kategori }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                                Deskripsi (Opsional)
                            </label>
                            <textarea
                                v-model="form.deskripsi"
                                rows="3"
                                placeholder="Tuliskan keterangan singkat tentang kategori ini..."
                                class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all resize-none"
                                :class="{ 'border-rose-300 bg-rose-50/30': form.errors.deskripsi }"
                            ></textarea>
                            <p v-if="form.errors.deskripsi" class="text-xs text-rose-500 mt-1.5">
                                {{ form.errors.deskripsi }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-sm shadow-emerald-200 transition-all disabled:opacity-50 cursor-pointer"
                            >
                                {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui' : 'Simpan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div
                v-if="isDeleteModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            >
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center">
                    <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                        <AlertCircle class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Hapus Kategori?</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Apakah Anda yakin ingin menghapus kategori <strong>"{{ categoryToDelete?.nama_kategori }}"</strong>?
                    </p>

                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button
                            @click="isDeleteModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            @click="deleteCategory"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium shadow-sm shadow-rose-200 cursor-pointer"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
