<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import {
    Menu, X, ChevronDown, LogOut, User, Bell, Settings, Home, Layers, Image, MapPin, Package, Users, BarChart2, FileText, Plus, ChevronRight
} from 'lucide-vue-next'
import { usePage, router } from '@inertiajs/vue3'

const sidebarOpen = ref(false)
const userMenuOpen = ref(false)
const notifications = ref([])
const showNotifications = ref(false)

const toasts = ref([])

function showToast(message, type = 'success') {
    const id = Date.now()
    toasts.value.push({ id, message, type })
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id)
    }, 5000)
}

const page = usePage()
onMounted(() => {
    if (page.props.flash?.success) showToast(page.props.flash.success, 'success')
    if (page.props.flash?.error) showToast(page.props.flash.error, 'error')
    if (page.props.flash?.info) showToast(page.props.flash.info, 'info')
})

watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success) showToast(newFlash.success, 'success')
    if (newFlash?.error) showToast(newFlash.error, 'error')
    if (newFlash?.info) showToast(newFlash.info, 'info')
}, { deep: true })

function handleClickOutside(event) {
    if (!event.target.closest('.user-menu')) userMenuOpen.value = false
    if (!event.target.closest('.notification-menu')) showNotifications.value = false
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

function logout() {
    router.post(route('logout'))
}
</script>

<template>
    <Head>
        <title>{{ $page.props.title || 'Admin - NUSA WANDER' }}</title>
    </Head>

    <div class="min-h-screen bg-[#eefdf5] flex">
        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-[#003527] border-r border-[#003527] transform transition-transform duration-300 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
            aria-label="Sidebar"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-[#003527]/20">
                    <Link href="/admin/dashboard" class="flex items-center space-x-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-500 flex items-center justify-center">
                            <Layers class="w-5 h-5 text-white" />
                        </span>
                        <span class="text-xl font-heading font-semibold text-white tracking-tight">NUSA WANDER</span>
                    </Link>
                    <button 
                        @click="sidebarOpen = false"
                        class="lg:hidden p-2 rounded-md text-white/70 hover:text-white hover:bg-white/10"
                        aria-label="Close sidebar"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <Link 
                        href="/admin/dashboard"
                        class="flex items-center px-3 py-2.5 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-colors"
                    >
                        <BarChart2 class="w-5 h-5 mr-3" />
                        <span>Dashboard</span>
                    </Link>

                    <!-- Categories -->
                    <Link 
                        href="/admin/categories"
                        class="flex items-center px-3 py-2.5 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-colors"
                    >
                        <FileText class="w-5 h-5 mr-3" />
                        <span>Kategori</span>
                    </Link>

                    <!-- Destinations -->
                    <Link 
                        href="/admin/destinations"
                        class="flex items-center px-3 py-2.5 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-colors"
                    >
                        <MapPin class="w-5 h-5 mr-3" />
                        <span>Destinasi</span>
                    </Link>

                    <hr class="my-3 border-white/10" />
                    
                    <p class="px-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Pengaturan</p>
                    
                    <Link 
                        href="/admin/destinations"
                        class="flex items-center px-3 py-2.5 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-colors"
                    >
                        <Image class="w-5 h-5 mr-3" />
                        <span>Galeri Foto</span>
                    </Link>
                </nav>

                <!-- Footer -->
                <div class="p-4 border-t border-white/10">
                    <p class="text-xs text-white/40 text-center">NusaWander Admin v1.0</p>
                </div>
            </div>
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div 
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            aria-hidden="true"
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 lg:ml-64">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-emerald-100">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Mobile Menu Button -->
                    <button 
                        @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-md text-[#003527]/70 hover:text-[#003527] hover:bg-emerald-50"
                        aria-label="Open sidebar"
                    >
                        <Menu class="w-6 h-6" />
                    </button>

                    <!-- Search / Title -->
                    <div class="flex-1 lg:flex-none">
                        <h1 class="text-lg font-heading font-semibold text-[#003527] truncate">
                            {{ $page.props.title || 'Admin Panel' }}
                        </h1>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative notification-menu">
                            <button 
                                @click="showNotifications = !showNotifications"
                                class="p-2 rounded-full text-[#003527]/60 hover:text-[#003527] hover:bg-emerald-50 transition-colors relative"
                                aria-label="Notifications"
                            >
                                <Bell class="w-5 h-5" />
                                <span v-if="notifications.length > 0" class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-xs rounded-full flex items-center justify-center">
                                    {{ notifications.length }}
                                </span>
                            </button>
                            <div 
                                v-if="showNotifications"
                                class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-emerald-100 py-2 z-50"
                            >
                                <p class="px-4 py-2 text-sm font-medium text-[#003527] border-b border-emerald-100">Notifikasi</p>
                                <p v-if="notifications.length === 0" class="px-4 py-4 text-sm text-gray-500 text-center">Tidak ada notifikasi</p>
                                <div v-for="notif in notifications" :key="notif.id" class="px-4 py-3 hover:bg-emerald-50">
                                    <p class="text-sm text-[#003527]">{{ notif.message }}</p>
                                    <p class="text-xs text-gray-500">{{ notif.time }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative user-menu">
                            <button 
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center space-x-2 p-2 rounded-xl hover:bg-emerald-50 transition-colors"
                                aria-label="User menu"
                            >
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <User class="w-5 h-5 text-emerald-600" />
                                </div>
                                <span class="hidden md:block text-sm font-medium text-[#003527]">{{ $page.props.auth.user?.name }}</span>
                                <ChevronDown class="w-4 h-4 text-gray-500 hidden md:block" />
                            </button>

                            <div 
                                v-if="userMenuOpen"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-emerald-100 py-2 z-50"
                            >
                                <div class="px-4 py-3 border-b border-emerald-100">
                                    <p class="text-sm font-medium text-[#003527]">{{ $page.props.auth.user?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ $page.props.auth.user?.email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">
                                        {{ $page.props.auth.user?.role === 'admin' ? 'Administrator' : 'Wisatawan' }}
                                    </span>
                                </div>
                                <Link 
                                    href="/profile"
                                    class="flex items-center px-4 py-2 text-sm text-[#003527]/80 hover:bg-emerald-50"
                                >
                                    <Settings class="w-4 h-4 mr-3" />
                                    Pengaturan Profil
                                </Link>
                                <hr class="my-2 border-emerald-100" />
                                <button 
                                    @click="logout"
                                    class="flex items-center w-full px-4 py-2 text-sm text-rose-600 hover:bg-rose-50"
                                >
                                    <LogOut class="w-4 h-4 mr-3" />
                                    Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <!-- Toast Notifications -->
                <div class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none">
                    <div 
                        v-for="toast in toasts" 
                        :key="toast.id"
                        :class="[
                            'pointer-events-auto animate-slide-in flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg min-w-[300px] max-w-md',
                            toast.type === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : '',
                            toast.type === 'error' ? 'bg-rose-50 text-rose-800 border border-rose-200' : '',
                            toast.type === 'info' ? 'bg-blue-50 text-blue-800 border border-blue-200' : '',
                        ]"
                    >
                        <span class="flex-1 text-sm">{{ toast.message }}</span>
                        <button 
                            @click="toasts = toasts.filter(t => t.id !== toast.id)"
                            class="text-current opacity-50 hover:opacity-100"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <slot />
            </main>
        </div>
    </div>

    <style scoped>
    @keyframes slide-in {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    .animate-slide-in { animation: slide-in 0.3s ease-out; }
    </style>
</template>