<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import axios from 'axios'
import { Globe, X, ExternalLink, Loader2, AlertTriangle, Info } from 'lucide-vue-next'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    initialArea: {
        type: String,
        default: '',
    },
    initialDuration: {
        type: Number,
        default: 2,
    },
    initialBudget: {
        type: [Number, String],
        default: null,
    },
    initialPreference: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['close'])

const wilayahTujuan = ref(props.initialArea)
const preferensi = ref(props.initialPreference)
const catatan = ref('')
const loading = ref(false)
const errorMessage = ref('')
const result = ref(null)

watch(
    () => props.show,
    (open) => {
        if (open) {
            wilayahTujuan.value = props.initialArea || wilayahTujuan.value
            preferensi.value = props.initialPreference || preferensi.value
            errorMessage.value = ''
        }
    }
)

function onKeydown(event) {
    if (event.key === 'Escape' && props.show) {
        emit('close')
    }
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))

async function searchWithAi() {
    if (!wilayahTujuan.value.trim()) {
        errorMessage.value = 'Masukkan wilayah tujuan terlebih dahulu.'
        return
    }

    loading.value = true
    errorMessage.value = ''

    try {
        const { data } = await axios.post('/ai/alternatives', {
            wilayah_tujuan: wilayahTujuan.value.trim(),
            durasi_hari: props.initialDuration || 2,
            anggaran: props.initialBudget ? Number(props.initialBudget) : null,
            preferensi: preferensi.value.trim() || null,
            catatan: catatan.value.trim() || null,
        })
        result.value = data
    } catch (err) {
        if (err.response?.status === 429) {
            errorMessage.value = 'Terlalu banyak permintaan dalam 1 menit. Tunggu sebentar lalu coba lagi.'
        } else {
            errorMessage.value = err.response?.data?.message || 'Gagal mengambil alternatif eksternal saat ini.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div
        v-if="show"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ai-modal-title"
        class="fixed inset-0 z-50 bg-brand-dark/50 flex items-center justify-center p-4 overflow-y-auto"
        @click.self="emit('close')"
    >
        <div class="bg-white rounded-xl max-w-2xl w-full p-6 sm:p-7 shadow-xl border border-brand-dark/10 my-8 space-y-5">
            <!-- Header -->
            <div class="flex items-start justify-between gap-4 border-b border-brand-dark/10 pb-4">
                <div>
                    <p class="text-xs font-semibold text-brand-primary flex items-center gap-1.5">
                        <Globe class="w-4 h-4" aria-hidden="true" /> Pencarian Alternatif Eksternal (AI)
                    </p>
                    <h3 id="ai-modal-title" class="font-heading font-bold text-xl text-brand-dark mt-1">
                        Cari Alternatif dengan AI
                    </h3>
                    <p class="text-xs text-brand-muted mt-1">
                        Hasil bersifat sementara sebagai referensi tambahan dan tidak masuk ke katalog resmi secara otomatis.
                    </p>
                </div>
                <button
                    type="button"
                    @click="emit('close')"
                    class="p-2 text-brand-muted hover:text-brand-dark hover:bg-brand-bg rounded-lg transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center"
                    aria-label="Tutup dialog"
                >
                    <X class="w-5 h-5" aria-hidden="true" />
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="searchWithAi" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="ai-area" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Wilayah tujuan <span class="text-coral-text">*</span>
                        </label>
                        <input
                            id="ai-area"
                            v-model="wilayahTujuan"
                            type="text"
                            placeholder="Misal: Bandung, Lombok, Malang"
                            class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                        />
                    </div>
                    <div>
                        <label for="ai-pref" class="block text-xs font-semibold text-brand-dark mb-1.5">
                            Minat atau preferensi
                        </label>
                        <input
                            id="ai-pref"
                            v-model="preferensi"
                            type="text"
                            placeholder="Misal: Alam, Kuliner hemat"
                            class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                        />
                    </div>
                </div>

                <div>
                    <label for="ai-note" class="block text-xs font-semibold text-brand-dark mb-1.5">
                        Catatan tambahan (opsional)
                    </label>
                    <input
                        id="ai-note"
                        v-model="catatan"
                        type="text"
                        placeholder="Misal: Ramah keluarga, dekat pusat kota"
                        class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-dark/15 rounded-lg text-sm text-brand-dark focus:bg-white focus:border-brand-primary min-h-[44px]"
                    />
                </div>

                <p v-if="errorMessage" role="alert" class="text-xs font-medium text-coral-text bg-coral-light px-3.5 py-2.5 rounded-lg flex items-center gap-2">
                    <AlertTriangle class="w-4 h-4 shrink-0" aria-hidden="true" /> {{ errorMessage }}
                </p>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="emit('close')"
                        class="px-4 py-2.5 rounded-lg border border-brand-dark/15 text-sm font-medium text-brand-dark hover:bg-brand-bg min-h-[44px]"
                    >
                        Tutup
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-primary hover:bg-brand-primaryHover text-white text-sm font-semibold rounded-lg disabled:opacity-50 min-h-[44px]"
                    >
                        <Loader2 v-if="loading" class="w-4 h-4 animate-spin" aria-hidden="true" />
                        <Globe v-else class="w-4 h-4" aria-hidden="true" />
                        {{ loading ? 'Mencari alternatif...' : 'Cari Alternatif Eksternal' }}
                    </button>
                </div>
            </form>

            <!-- Results -->
            <div v-if="result" class="space-y-3 pt-3 border-t border-brand-dark/10">
                <div class="flex items-start gap-2.5 bg-coral-light border border-coral/40 rounded-lg p-3.5 text-xs text-coral-text">
                    <Info class="w-4 h-4 shrink-0 mt-0.5" aria-hidden="true" />
                    <p>{{ result.disclaimer }}</p>
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    <article
                        v-for="(alt, idx) in result.alternatives"
                        :key="idx"
                        class="p-4 rounded-xl border border-brand-dark/10 bg-brand-bg/60 space-y-1.5"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-semibold text-sm text-brand-dark">{{ alt.nama }}</h4>
                                <span class="inline-block text-[11px] font-medium text-brand-dark bg-brand-input px-2 py-0.5 rounded-md mt-1">
                                    {{ alt.kategori }}
                                </span>
                            </div>
                            <span class="text-xs font-semibold text-brand-primary shrink-0 tabular-nums">
                                {{ alt.estimasi_kasar }}
                            </span>
                        </div>
                        <p class="text-xs text-brand-muted leading-relaxed">{{ alt.ringkasan }}</p>
                        <div v-if="alt.sumber_url" class="pt-1">
                            <a
                                :href="alt.sumber_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-brand-primary hover:underline"
                            >
                                Sumber: {{ alt.sumber_nama }}
                                <ExternalLink class="w-3.5 h-3.5" aria-hidden="true" />
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</template>
