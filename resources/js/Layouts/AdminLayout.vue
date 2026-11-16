<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { Menu, X, ChevronDown, LogOut, User, Bell, Settings, Home, Layers, Image, MapPin, Package, Users, BarChart2, FileText, Plus } from 'lucide-vue-next'
import { usePage, router } from '@inertiajs/vue3'
import { ref as vueRef } from 'vue'

const sidebarOpen = ref(false)
const userMenuOpen = ref(false)
const notifications = ref([])
const showNotifications = ref(false)

// Toast/Flash messages
const toasts = ref([])

function showToast(message, type = 'success') {
    const id = Date.now()
    toasts.value.push({ id, message, type })
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id)
    }, 5000)
}

// Listen for flash messages from Laravel
const page = usePage()
onMounted(() => {
    // Check for flash messages on initial load
    if (page.props.flash?.success) showToast(page.props.flash.success, 'success')
    if (page.props.flash?.error) showToast(page.props.flash.error, 'error')
    if (page.props.flash?.info) showToast(page.props.flash.info, 'info')
})

// Watch for flash messages on subsequent Inertia visits
import { watch } from 'vue'
watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success) showToast(newFlash.success, 'success')
    if (newFlash?.error) showToast(newFlash.error, 'error')
    if (newFlash?.info) showToast(newFlash.info, 'info')
}, { deep: true })

// Close dropdowns when clicking outside
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
        <title>{{ $page.props.title || 'Admin - NusaWander' }}</title>
    </Head>

    <div class="min-h-screen bg-gray-50 flex">
        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
            aria-label="Sidebar"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                    <Link href="/admin/dashboard" class="flex items-center space-x-2">
                        <Layers class="w-8 h-8 text-green-600" />
                        <span class="text-xl font-bold text-gray-900">NusaWander</span>
                    </Link>
                    <button 
                        @click="sidebarOpen = false"
                        class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
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
                        class="flex items-center px-3 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors"
                    >
                        <BarChart2 class="w-5 h-5 mr-3" />
                        <span>Dashboard</span>
                    </Link>

                    <!-- Categories -->
                    <Link 
                        href="/admin/categories"
                        class="flex items-center px-3 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors"
                    >
                        <FileText class="w-5 h-5 mr-3" />
                        <span>Kategori</span>
                    </Link>

                    <!-- Destinations -->
                    <Link 
                        href="/admin/destinations"
                        class="flex items-center px-3 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors"
                    >
                        <MapPin class="w-5 h-5 mr-3" />
                        <span>Destinasi</span>
                    </Link>

                    <hr class="my-3 border-gray-200" />

                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengaturan</p>
                    
                    <Link 
                        href="/admin/destination-images"
                        class="flex items-center px-3 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors"
                    >
                        <Image class="w-5 h-5 mr-3" />
                        <span>Galeri Foto</span>
                    </Link>
                </nav>

                <!-- Footer -->
                <div class="p-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center">NusaWander Admin v1.0</p>
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
        <div class="flex-1 flex flex-col min-w-0 lg:ml-0">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Mobile Menu Button -->
                    <button 
                        @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                        aria-label="Open sidebar"
                    >
                        <Menu class="w-6 h-6" />
                    </button>

                    <!-- Search / Title -->
                    <div class="flex-1 lg:flex-none">
                        <h1 class="text-lg font-semibold text-gray-900 truncate">
                            {{ $page.props.title || 'Admin Panel' }}
                        </h1>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative notification-menu">
                            <button 
                                @click="showNotifications = !showNotifications"
                                class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors relative"
                                aria-label="Notifications"
                            >
                                <Bell class="w-5 h-5" />
                                <span v-if="notifications.length > 0" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                    {{ notifications.length }}
                                </span>
                            </button>
                            <div 
                                v-if="showNotifications"
                                class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                            >
                                <p class="px-4 py-2 text-sm font-medium text-gray-900 border-b border-gray-200">Notifikasi</p>
                                <p v-if="notifications.length === 0" class="px-4 py-4 text-sm text-gray-500 text-center">Tidak ada notifikasi</p>
                                <div v-for="notif in notifications" :key="notif.id" class="px-4 py-3 hover:bg-gray-50">
                                    <p class="text-sm text-gray-900">{{ notif.message }}</p>
                                    <p class="text-xs text-gray-500">{{ notif.time }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative user-menu">
                            <button 
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                aria-label="User menu"
                            >
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <User class="w-5 h-5 text-green-600" />
                                </div>
                                <span class="hidden md:block text-sm font-medium text-gray-700">{{ $page.props.auth.user?.name }}</span>
                                <ChevronDown class="w-4 h-4 text-gray-500 hidden md:block" />
                            </button>

                            <div 
                                v-if="userMenuOpen"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                            >
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ $page.props.auth.user?.email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        {{ $page.props.auth.user?.role === 'admin' ? 'Administrator' : 'Wisatawan' }}
                                    </span>
                                </div>
                                <Link 
                                    href="/profile"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    <Settings class="w-4 h-4 mr-3" />
                                    Pengaturan Profil
                                </Link>
                                <hr class="my-2 border-gray-200" />
                                <button 
                                    @click="logout"
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
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
                            'pointer-events-auto animate-slide-in flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg min-w-[300px] max-w-md',
                            toast.type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : '',
                            toast.type === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : '',
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