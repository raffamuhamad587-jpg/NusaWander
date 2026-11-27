<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    Plus,
    Compass,
    FolderKanban,
    Users,
    Route,
    Camera,
    BadgeDollarSign,
    MapPin,
    ChevronRight,
} from 'lucide-vue-next'

const props = defineProps({
    stats: { type: Object, required: true },
    categoryBreakdown: { type: Array, default: () => [] },
    topDestinations: { type: Array, default: () => [] },
    recentActivities: { type: Array, default: () => [] },
})

const numberFmt = (value) => Number(value ?? 0).toLocaleString('id-ID')

const maxCategoryCount = computed(() => {
    const counts = props.categoryBreakdown.map((c) => Number(c.destinations_count || 0))
    return Math.max(1, ...counts)
})

const quickActions = computed(() => [
    {
        title: 'Kelola Kategori',
        sub: `${numberFmt(props.stats.totalCategories)} kategori aktif`,
        icon: FolderKanban,
        href: '/admin/categories',
    },
    {
        title: 'Kelola Foto Destinasi',
        sub: `${numberFmt(props.stats.destinationsWithoutPhoto)} destinasi belum punya foto`,
        icon: Camera,
        href: '/admin/destinations?photo_status=missing',
    },
    {
        title: 'Kelola Estimasi Harga',
        sub: `${numberFmt(props.stats.destinationsWithoutPrice)} destinasi belum diisi harga`,
        icon: BadgeDollarSign,
        href: '/admin/destinations?price_status=missing',
    },
])

function relativeTime(datetime) {
    if (!datetime) return ''
    const then = new Date(datetime).getTime()
    if (Number.isNaN(then)) return datetime
    const diff = Math.max(0, Date.now() - then)
    const minute = 60000
    if (diff < minute) return 'baru saja'
    if (diff < 60 * minute) return `${Math.floor(diff / minute)} menit lalu`
    if (diff < 24 * 60 * minute) return `${Math.floor(diff / (60 * minute))} jam lalu`
    return `${Math.floor(diff / (24 * 60 * minute))} hari lalu`
}
</script>

<template>
    <AdminLayout title="Dashboard Admin">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="font-heading text-2xl sm:text-3xl font-bold tracking-tight text-brand-dark">
                        Ringkasan Katalog NUSA WANDER
                    </h1>
                    <p class="mt-1 text-sm text-brand-muted">
                        Pantau kelengkapan destinasi, foto, estimasi harga, dan pemakaian pada itinerary wisatawan.
                    </p>
                </div>

                <Link
                    href="/admin/destinations/create"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-primary hover:bg-brand-primaryHover px-5 py-2.5 text-sm font-semibold text-white transition-colors shrink-0 min-h-[44px]"
                >
                    <Plus class="h-4 w-4" aria-hidden="true" />
                    Tambah Destinasi
                </Link>
            </div>

            <!-- Compact Metrics Strip (real DB numbers only) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <Link href="/admin/destinations" class="rounded-xl bg-white border border-brand-dark/10 p-5 hover:border-brand-primary/40 transition-colors">
                    <div class="flex items-center justify-between text-brand-muted">
                        <span class="text-xs font-semibold">Total Destinasi</span>
                        <Compass class="h-4 w-4 text-brand-primary" aria-hidden="true" />
                    </div>
                    <p class="mt-2 font-heading text-2xl sm:text-3xl font-bold text-brand-dark tabular-nums">
                        {{ numberFmt(stats.totalDestinations) }}
                    </p>
                    <p class="mt-1 text-xs text-brand-muted tabular-nums">
                        {{ numberFmt(stats.activeDestinations) }} aktif, {{ numberFmt(stats.inactiveDestinations) }} nonaktif
                    </p>
                </Link>

                <Link href="/admin/categories" class="rounded-xl bg-white border border-brand-dark/10 p-5 hover:border-brand-primary/40 transition-colors">
                    <div class="flex items-center justify-between text-brand-muted">
                        <span class="text-xs font-semibold">Kategori &amp; Foto</span>
                        <FolderKanban class="h-4 w-4 text-brand-primary" aria-hidden="true" />
                    </div>
                    <p class="mt-2 font-heading text-2xl sm:text-3xl font-bold text-brand-dark tabular-nums">
                        {{ numberFmt(stats.totalCategories) }}
                    </p>
                    <p class="mt-1 text-xs text-brand-muted tabular-nums">
                        {{ numberFmt(stats.totalPhotos) }} foto tersimpan
                    </p>
                </Link>

                <div class="rounded-xl bg-white border border-brand-dark/10 p-5">
                    <div class="flex items-center justify-between text-brand-muted">
                        <span class="text-xs font-semibold">Wisatawan</span>
                        <Users class="h-4 w-4 text-brand-primary" aria-hidden="true" />
                    </div>
                    <p class="mt-2 font-heading text-2xl sm:text-3xl font-bold text-brand-dark tabular-nums">
                        {{ numberFmt(stats.totalTourists) }}
                    </p>
                    <p class="mt-1 text-xs text-brand-muted">Akun wisatawan terdaftar</p>
                </div>

                <Link href="/admin/destinations?price_status=missing" class="rounded-xl bg-white border border-brand-dark/10 p-5 hover:border-coral transition-colors">
                    <div class="flex items-center justify-between text-brand-muted">
                        <span class="text-xs font-semibold">Itinerary Tersimpan</span>
                        <Route class="h-4 w-4 text-coral-text" aria-hidden="true" />
                    </div>
                    <p class="mt-2 font-heading text-2xl sm:text-3xl font-bold text-brand-dark tabular-nums">
                        {{ numberFmt(stats.totalTrips) }}
                    </p>
                    <p class="mt-1 text-xs text-coral-text font-medium tabular-nums">
                        {{ numberFmt(stats.destinationsWithoutPrice) }} destinasi tanpa harga
                    </p>
                </Link>
            </div>

            <!-- Main grid -->
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                <!-- Category breakdown -->
                <section class="rounded-xl bg-white border border-brand-dark/10 p-6 xl:col-span-7">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h2 class="font-heading text-lg font-bold text-brand-dark">Sebaran destinasi per kategori</h2>
                            <p class="mt-0.5 text-xs text-brand-muted">Jumlah destinasi aktif dan nonaktif di setiap kategori</p>
                        </div>
                        <Link href="/admin/categories" class="text-xs font-semibold text-brand-primary hover:underline">
                            Kelola Kategori
                        </Link>
                    </div>

                    <div v-if="categoryBreakdown.length > 0" class="space-y-4">
                        <div v-for="cat in categoryBreakdown" :key="cat.id" class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="text-brand-dark">{{ cat.nama_kategori }}</span>
                                <span class="text-brand-muted tabular-nums">{{ cat.destinations_count }} destinasi</span>
                            </div>
                            <div class="h-2.5 w-full rounded-full bg-brand-bg overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-brand-primary"
                                    :style="{ width: `${Math.max(4, Math.round((cat.destinations_count / maxCategoryCount) * 100))}%` }"
                                />
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-brand-muted py-8 text-center">Belum ada kategori terdaftar.</p>
                </section>

                <!-- Right column -->
                <div class="flex flex-col gap-6 xl:col-span-5">
                    <section class="rounded-xl bg-white border border-brand-dark/10 p-5">
                        <h2 class="font-heading text-base font-bold text-brand-dark">Prioritas pemeliharaan katalog</h2>
                        <p class="mt-0.5 text-xs text-brand-muted">Pintasan melengkapi data foto dan estimasi harga</p>

                        <div class="mt-4 space-y-2.5">
                            <Link
                                v-for="action in quickActions"
                                :key="action.title"
                                :href="action.href"
                                class="flex items-center gap-3 rounded-lg bg-brand-bg px-4 py-3 hover:bg-brand-input/80 transition-colors min-h-[44px]"
                            >
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-brand-primary border border-brand-dark/10" aria-hidden="true">
                                    <component :is="action.icon" class="h-4 w-4" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-brand-dark">{{ action.title }}</span>
                                    <span class="block truncate text-xs text-brand-muted">{{ action.sub }}</span>
                                </span>
                                <ChevronRight class="h-4 w-4 shrink-0 text-brand-muted" aria-hidden="true" />
                            </Link>
                        </div>
                    </section>

                    <section class="rounded-xl bg-white border border-brand-dark/10 p-5">
                        <h2 class="font-heading text-base font-bold text-brand-dark">Pembaruan katalog terbaru</h2>
                        <ul v-if="recentActivities.length" class="mt-4 divide-y divide-brand-dark/10">
                            <li v-for="(activity, i) in recentActivities" :key="i" class="py-2.5 first:pt-0 last:pb-0">
                                <p class="text-sm font-medium text-brand-dark">{{ activity.title }}</p>
                                <p class="mt-0.5 text-xs text-brand-muted">
                                    {{ relativeTime(activity.time) }} &bull; <span class="font-semibold text-brand-primary">{{ activity.tag }}</span>
                                </p>
                            </li>
                        </ul>
                        <p v-else class="mt-3 text-sm text-brand-muted">Belum ada pembaruan katalog.</p>
                    </section>
                </div>
            </div>

            <!-- Top destinations table -->
            <section class="rounded-xl bg-white border border-brand-dark/10 p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-brand-dark">Destinasi paling sering masuk itinerary</h2>
                        <p class="mt-0.5 text-xs text-brand-muted">Dihitung langsung dari tabel itinerary_items pada basis data</p>
                    </div>
                    <Link href="/admin/destinations" class="text-xs font-semibold text-brand-primary hover:underline shrink-0">
                        Lihat semua ({{ stats.totalDestinations }})
                    </Link>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left">
                        <thead>
                            <tr class="bg-brand-bg text-xs font-semibold text-brand-muted">
                                <th class="rounded-l-lg px-4 py-3">Peringkat</th>
                                <th class="px-4 py-3">Destinasi &amp; kota</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Dipakai di itinerary</th>
                                <th class="rounded-r-lg px-4 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-dark/10 text-sm">
                            <tr v-for="(d, i) in topDestinations" :key="d.id">
                                <td class="px-4 py-3.5 font-bold text-brand-muted tabular-nums">#{{ i + 1 }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="relative block h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-brand-input">
                                            <img v-if="d.images?.[0]?.url_gambar" :src="d.images[0].url_gambar" :alt="d.nama" class="h-full w-full object-cover">
                                            <MapPin v-else class="absolute inset-0 m-auto h-4 w-4 text-brand-dark/30" aria-hidden="true" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-brand-dark">{{ d.nama }}</p>
                                            <p class="text-xs text-brand-muted">{{ d.kota }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-md bg-brand-input px-2.5 py-0.5 text-xs font-medium text-brand-dark">
                                        {{ d.category?.nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-brand-dark tabular-nums">
                                    {{ numberFmt(d.itinerary_items_count) }} kali
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <span
                                        :class="[
                                            'inline-flex rounded-md px-2.5 py-0.5 text-xs font-semibold',
                                            d.status === 'aktif' ? 'bg-teal-light text-brand-primary' : 'bg-brand-input text-brand-muted',
                                        ]"
                                    >
                                        {{ d.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!topDestinations.length">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-brand-muted">
                                    Belum ada data destinasi.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
