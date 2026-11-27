<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Plus, Edit2, Trash2, Folder, X, AlertCircle } from 'lucide-vue-next'

defineProps({
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
        form.put(`/admin/categories/${selectedCategory.value.id}`, {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post('/admin/categories', {
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
        router.delete(`/admin/categories/${categoryToDelete.value.id}`, {
            onSuccess: () => {
                isDeleteModalOpen.value = false
                categoryToDelete.value = null
            },
        })
    }
}

function onKeydown(event) {
    if (event.key === 'Escape') {
        isModalOpen.value = false
        isDeleteModalOpen.value = false
    }
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))
</script>

<template>
    <AdminLayout title="Kelola Kategori">
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="font-heading font-bold text-2xl sm:text-3xl text-brand-dark">Kelola Kategori</h1>
                    <p class="text-sm text-brand-muted mt-1">Atur kategori destinasi untuk filter pencarian dan preferensi itinerary.</p>
                </div>
                <button
                    type="button"
                    @click="openCreateModal"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white font-semibold text-sm rounded-lg transition-colors min-h-[44px]"
                >
                    <Plus class="w-4 h-4" aria-hidden="true" />
                    Tambah Kategori
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <article
                    v-for="cat in categories"
                    :key="cat.id"
                    class="bg-white rounded-xl p-5 border border-brand-dark/10 flex flex-col justify-between gap-4"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <span class="w-10 h-10 rounded-lg bg-brand-bg text-brand-primary flex items-center justify-center shrink-0" aria-hidden="true">
                                <Folder class="w-5 h-5" />
                            </span>
                            <div>
                                <h2 class="font-heading font-bold text-base text-brand-dark">{{ cat.nama_kategori }}</h2>
                                <span class="inline-block px-2 py-0.5 mt-1 rounded-md text-xs font-medium bg-brand-input text-brand-dark tabular-nums">
                                    {{ cat.destinations_count }} destinasi
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                @click="openEditModal(cat)"
                                :aria-label="`Edit kategori ${cat.nama_kategori}`"
                                class="p-2 text-brand-muted hover:text-brand-dark hover:bg-brand-bg rounded-lg min-h-[40px] min-w-[40px] flex items-center justify-center"
                                title="Edit kategori"
                            >
                                <Edit2 class="w-4 h-4" aria-hidden="true" />
                            </button>
                            <button
                                type="button"
                                @click="confirmDelete(cat)"
                                :aria-label="`Hapus kategori ${cat.nama_kategori}`"
                                class="p-2 text-brand-muted hover:text-coral-text hover:bg-coral-light rounded-lg min-h-[40px] min-w-[40px] flex items-center justify-center"
                                title="Hapus kategori"
                            >
                                <Trash2 class="w-4 h-4" aria-hidden="true" />
                            </button>
                        </div>
                    </div>

                    <p class="text-xs text-brand-muted leading-relaxed line-clamp-2">
                        {{ cat.deskripsi || 'Belum ada deskripsi untuk kategori ini.' }}
                    </p>
                </article>
            </div>

            <div v-if="categories.length === 0" class="text-center py-14 bg-white rounded-xl border border-brand-dark/10">
                <Folder class="w-10 h-10 text-brand-muted mx-auto mb-3" aria-hidden="true" />
                <h2 class="font-heading font-bold text-lg text-brand-dark">Belum ada kategori</h2>
                <p class="text-sm text-brand-muted mt-1">Buat kategori pertama untuk mengelompokkan destinasi wisata.</p>
                <button
                    type="button"
                    @click="openCreateModal"
                    class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold bg-brand-primary hover:bg-brand-primaryHover text-white rounded-lg min-h-[44px]"
                >
                    <Plus class="w-4 h-4" aria-hidden="true" /> Tambah Kategori
                </button>
            </div>

            <!-- Create/Edit Modal -->
            <div
                v-if="isModalOpen"
                role="dialog"
                aria-modal="true"
                aria-labelledby="cat-modal-title"
                class="fixed inset-0 z-50 overflow-y-auto bg-brand-dark/50 flex items-center justify-center p-4"
                @click.self="closeModal"
            >
                <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-brand-dark/10">
                    <div class="flex items-center justify-between pb-3 border-b border-brand-dark/10">
                        <h3 id="cat-modal-title" class="font-heading font-bold text-lg text-brand-dark">
                            {{ isEditing ? 'Edit kategori wisata' : 'Tambah kategori baru' }}
                        </h3>
                        <button type="button" @click="closeModal" aria-label="Tutup dialog" class="p-2 text-brand-muted hover:text-brand-dark rounded-lg min-h-[40px] min-w-[40px] flex items-center justify-center">
                            <X class="w-5 h-5" aria-hidden="true" />
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-4 space-y-4">
                        <div>
                            <label for="cat-name" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Nama kategori <span class="text-coral-text">*</span>
                            </label>
                            <input
                                id="cat-name"
                                v-model="form.nama_kategori"
                                type="text"
                                placeholder="Misal: Alam, Kuliner, Sejarah"
                                class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm focus:bg-white focus:border-brand-primary min-h-[44px]"
                            />
                            <p v-if="form.errors.nama_kategori" class="text-xs text-coral-text mt-1">{{ form.errors.nama_kategori }}</p>
                        </div>

                        <div>
                            <label for="cat-desc" class="block text-xs font-semibold text-brand-dark mb-1.5">
                                Deskripsi (opsional)
                            </label>
                            <textarea
                                id="cat-desc"
                                v-model="form.deskripsi"
                                rows="3"
                                placeholder="Keterangan singkat kategori ini..."
                                class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm focus:bg-white focus:border-brand-primary resize-none"
                            ></textarea>
                            <p v-if="form.errors.deskripsi" class="text-xs text-coral-text mt-1">{{ form.errors.deskripsi }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-brand-dark/10">
                            <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-brand-dark/15 text-sm font-semibold text-brand-dark hover:bg-brand-bg min-h-[44px]">
                                Batal
                            </button>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-brand-primary hover:bg-brand-primaryHover text-white text-sm font-semibold disabled:opacity-50 min-h-[44px]">
                                {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui' : 'Simpan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div
                v-if="isDeleteModalOpen"
                role="dialog"
                aria-modal="true"
                aria-labelledby="cat-delete-title"
                class="fixed inset-0 z-50 bg-brand-dark/50 flex items-center justify-center p-4"
                @click.self="isDeleteModalOpen = false"
            >
                <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-xl border border-brand-dark/10 text-center">
                    <div class="w-11 h-11 rounded-lg bg-coral-light text-coral-text flex items-center justify-center mx-auto mb-3" aria-hidden="true">
                        <AlertCircle class="w-5 h-5" />
                    </div>
                    <h3 id="cat-delete-title" class="font-heading font-bold text-lg text-brand-dark">Hapus kategori?</h3>
                    <p class="text-sm text-brand-muted mt-1.5">
                        Yakin ingin menghapus kategori <strong>"{{ categoryToDelete?.nama_kategori }}"</strong>?
                    </p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button type="button" @click="isDeleteModalOpen = false" class="px-4 py-2.5 rounded-lg border border-brand-dark/15 text-sm font-semibold text-brand-dark hover:bg-brand-bg min-h-[44px]">
                            Batal
                        </button>
                        <button type="button" @click="deleteCategory" class="px-4 py-2.5 rounded-lg bg-coral-text hover:bg-brand-dark text-white text-sm font-semibold min-h-[44px]">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
