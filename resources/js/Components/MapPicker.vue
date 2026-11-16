<script setup>
import { ref, onMounted, watch } from 'vue'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import { Search, MapPin, Loader2 } from 'lucide-vue-next'

// Fix default leaflet marker icon path in bundler
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
import markerIcon from 'leaflet/dist/images/marker-icon.png'
import markerShadow from 'leaflet/dist/images/marker-shadow.png'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
})

const props = defineProps({
    latitude: {
        type: [Number, String],
        default: null,
    },
    longitude: {
        type: [Number, String],
        default: null,
    },
})

const emit = defineEmits(['update:latitude', 'update:longitude'])

const mapContainer = ref(null)
let map = null
let marker = null

const searchQuery = ref('')
const isSearching = ref(false)
const searchError = ref('')

// Default coordinate: Monas Jakarta, or Indonesia center
const defaultLat = -6.175392
const defaultLng = 106.827153

onMounted(() => {
    initMap()
})

function initMap() {
    const lat = props.latitude ? parseFloat(props.latitude) : defaultLat
    const lng = props.longitude ? parseFloat(props.longitude) : defaultLng

    map = L.map(mapContainer.value).setView([lat, lng], props.latitude ? 14 : 5)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map)

    if (props.latitude && props.longitude) {
        addMarker(lat, lng)
    }

    map.on('click', (e) => {
        const { lat, lng } = e.latlng
        addMarker(lat, lng)
        emit('update:latitude', Number(lat.toFixed(8)))
        emit('update:longitude', Number(lng.toFixed(8)))
    })
}

function addMarker(lat, lng) {
    if (marker) {
        marker.setLatLng([lat, lng])
    } else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map)
        marker.on('dragend', (e) => {
            const position = e.target.getLatLng()
            emit('update:latitude', Number(position.lat.toFixed(8)))
            emit('update:longitude', Number(position.lng.toFixed(8)))
        })
    }
}

watch(
    () => [props.latitude, props.longitude],
    ([newLat, newLng]) => {
        if (map && newLat && newLng) {
            const lat = parseFloat(newLat)
            const lng = parseFloat(newLng)
            addMarker(lat, lng)
            map.panTo([lat, lng])
        }
    }
)

async function searchLocation() {
    if (!searchQuery.value.trim()) return

    isSearching.value = true
    searchError.value = ''

    try {
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
            searchQuery.value + ', Indonesia'
        )}&limit=1`
        const response = await fetch(url, {
            headers: {
                'Accept-Language': 'id',
            },
        })

        if (!response.ok) throw new Error('Gagal menghubungi layanan geocoding')

        const data = await response.json()
        if (data && data.length > 0) {
            const result = data[0]
            const lat = parseFloat(result.lat)
            const lon = parseFloat(result.lon)

            map.setView([lat, lon], 14)
            addMarker(lat, lon)
            emit('update:latitude', Number(lat.toFixed(8)))
            emit('update:longitude', Number(lon.toFixed(8)))
        } else {
            searchError.value = 'Lokasi tidak ditemukan. Coba kata kunci yang lebih spesifik.'
        }
    } catch (err) {
        searchError.value = 'Layanan pencarian peta sedang sibuk. Anda tetap dapat mengklik atau menggeser pin di peta secara manual.'
    } finally {
        isSearching.value = false
    }
}
</script>

<template>
    <div class="space-y-3">
        <!-- Search bar -->
        <div class="flex gap-2">
            <div class="relative flex-1">
                <input
                    v-model="searchQuery"
                    @keydown.enter.prevent="searchLocation"
                    type="text"
                    placeholder="Cari lokasi di peta (misal: Malioboro, Candi Borobudur)..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                />
                <Search class="w-4 h-4 text-gray-400 absolute left-3 top-2.5 sm:top-3" />
            </div>
            <button
                type="button"
                @click="searchLocation"
                :disabled="isSearching"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-medium flex items-center gap-1.5 transition-colors cursor-pointer shrink-0 disabled:opacity-50"
            >
                <Loader2 v-if="isSearching" class="w-4 h-4 animate-spin" />
                <span v-else>Cari</span>
            </button>
        </div>

        <p v-if="searchError" class="text-xs text-amber-600 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
            {{ searchError }}
        </p>

        <!-- Leaflet Map Container -->
        <div class="relative rounded-2xl overflow-hidden border border-gray-200 shadow-inner h-72 sm:h-80 z-0">
            <div ref="mapContainer" class="w-full h-full"></div>
            <div class="absolute bottom-2 left-2 z-1000 bg-white/90 backdrop-blur-xs px-2.5 py-1 rounded-md text-[11px] text-gray-600 shadow-sm border border-gray-200 pointer-events-none">
                Klik peta atau geser pin untuk menentukan koordinat
            </div>
        </div>

        <!-- Coordinates Display -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Latitude</label>
                <input
                    :value="latitude"
                    @input="$emit('update:latitude', $event.target.value)"
                    type="text"
                    placeholder="-7.797068"
                    class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-mono text-gray-800 focus:bg-white focus:outline-hidden"
                />
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Longitude</label>
                <input
                    :value="longitude"
                    @input="$emit('update:longitude', $event.target.value)"
                    type="text"
                    placeholder="110.370529"
                    class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-mono text-gray-800 focus:bg-white focus:outline-hidden"
                />
            </div>
        </div>
    </div>
</template>
