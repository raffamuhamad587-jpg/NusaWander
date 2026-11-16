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
                    <h2 class="font-heading font-bold text-3xl text-[#003527] tracking-tight">Kategori Destinasi</h2>
                    <p class="text-sm text-[#404945]/70 mt-1">Kelola kelompok wisata untuk mempermudah pencarian wisatawan</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center justify-center px-6 py-3 bg-[#174c3c] hover:bg-[#003527] text-white font-semibold text-sm rounded-2xl transition-all shadow-lg shadow-emerald-200 gap-2 cursor-pointer"
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
                    class="bg-white rounded-3xl p-6 border border-emerald-100 shadow-sm hover:shadow-lg transition-all relative group"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#174c3c] flex items-center justify-center font-bold">
                                <Folder class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="font-heading font-bold text-[#003527] text-lg">{{ cat.nama_kategori }}</h3>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-100">
                                    <Layers class="w-3.5 h-3.5" />
                                    {{ cat.destinations_count }} Destinasi
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <button
                                @click="openEditModal(cat)"
                                class="p-2.5 text-[#404945]/60 hover:text-[#174c3c] hover:bg-emerald-50 rounded-xl transition-colors cursor-pointer"
                                title="Edit Kategori"
                            >
                                <Edit2 class="w-4 h-4" />
                            </button>
                            <button
                                @click="confirmDelete(cat)"
                                class="p-2.5 text-[#404945]/60 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer"
                                title="Hapus Kategori"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <p class="text-sm text-[#404945]/70 mt-5 leading-relaxed line-clamp-2">
                        {{ cat.deskripsi || 'Tidak ada deskripsi untuk kategori ini.' }}
                    </p>
                </div>
            </div>

            <div v-if="categories.length === 0" class="text-center py-20 bg-white rounded-3xl border border-emerald-100">
                <Folder class="w-16 h-16 text-emerald-200 mx-auto mb-4" />
                <h4 class="font-heading font-bold text-xl text-[#003527]">Belum Ada Kategori</h4>
                <p class="text-sm text-[#404945]/60 mt-2">Mulai buat kategori pertama untuk mengelompokkan destinasi wisata.</p>
                <button
                    @click="openCreateModal"
                    class="mt-6 inline-flex items-center px-6 py-3 text-sm bg-[#174c3c] hover:bg-[#003527] text-white rounded-2xl transition-all shadow-lg shadow-emerald-200"
                >
                    <Plus class="w-4 h-4 mr-2" /> Tambah Kategori
                </button>
            </div>

            <!-- Create/Edit Modal -->
            <div
                v-if="isModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            >
                <div class="bg-white rounded-3xl max-w-md w-full p-7 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
                    <div class="flex items-center justify-between pb-4 border-b border-emerald-100">
                        <h3 class="font-heading font-bold text-lg text-[#003527]">
                            {{ isEditing ? 'Edit Kategori Wisata' : 'Tambah Kategori Baru' }}
                        </h3>
                        <button @click="closeModal" class="text-[#404945]/50 hover:text-[#003527] p-1.5 rounded-xl hover:bg-emerald-50 transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.nama_kategori"
                                type="text"
                                placeholder="Contoh: Alam, Kuliner, Sejarah..."
                                class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                                :class="{ 'border-rose-300 bg-rose-50/30': form.errors.nama_kategori }"
                            />
                            <p v-if="form.errors.nama_kategori" class="text-xs text-rose-500 mt-1.5">
                                {{ form.errors.nama_kategori }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#404945]/70 mb-2">
                                Deskripsi (Opsional)
                            </label>
                            <textarea
                                v-model="form.deskripsi"
                                rows="3"
                                placeholder="Tuliskan keterangan singkat tentang kategori ini..."
                                class="w-full px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all resize-none"
                                :class="{ 'border-rose-300 bg-rose-50/30': form.errors.deskripsi }"
                            ></textarea>
                            <p v-if="form.errors.deskripsi" class="text-xs text-rose-500 mt-1.5">
                                {{ form.errors.deskripsi }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-5 border-t border-emerald-100">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-5 py-2.5 rounded-xl border border-emerald-100 text-sm font-semibold text-[#404945] hover:bg-emerald-50 cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2.5 rounded-xl bg-[#174c3c] hover:bg-[#003527] text-white text-sm font-semibold shadow-lg shadow-emerald-200 transition-all disabled:opacity-50 cursor-pointer"
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
                <div class="bg-white rounded-3xl max-w-sm w-full p-7 shadow-2xl text-center">
                    <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                        <AlertCircle class="w-6 h-6" />
                    </div>
                    <h3 class="font-heading font-bold text-lg text-[#003527]">Hapus Kategori?</h3>
                    <p class="text-sm text-[#404945]/70 mt-2">
                        Apakah Anda yakin ingin menghapus kategori <strong>"{{ categoryToDelete?.nama_kategori }}"</strong>?
                    </p>

                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button
                            @click="isDeleteModalOpen = false"
                            class="px-5 py-2.5 rounded-xl border border-emerald-100 text-sm font-semibold text-[#404945] hover:bg-emerald-50 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            @click="deleteCategory"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-sm shadow-rose-200 cursor-pointer"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
