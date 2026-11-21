<x-layouts.app title="Locabite — Peta Makanan Berlebih & Penyelamat Pangan">

<div x-data="homeMap()" x-init="init()" class="relative flex-1 flex flex-col lg:flex-row h-[calc(100vh-4rem)] overflow-hidden">

    <!-- Top Floating Filter & Search Bar on Map (Mobile & Desktop) -->
    <div class="absolute top-3 inset-x-3 md:inset-x-6 z-20 pointer-events-none flex flex-col gap-2 max-w-2xl mx-auto">
        <!-- Search bar with Autocomplete Dropdown -->
        <div class="pointer-events-auto relative">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-card border border-[#D5E0D9] p-2 flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="fetchSuggestions()"
                           @focus="if (suggestions.length > 0) showSuggestions = true"
                           @keydown.enter.prevent="selectFirstOrSearch()"
                           placeholder="Cari jalan, kelurahan, kecamatan (misal: Sukajadi, Tembalang)..."
                           class="w-full pl-9 pr-8 py-2 text-sm rounded-xl border border-[#74877B] text-[#16251D] focus:outline-none focus:ring-2 focus:ring-[#2A6F97] bg-white">
                    <svg class="w-4 h-4 text-[#4E6157] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <!-- Clear search button -->
                    <button type="button" x-show="searchQuery" @click="clearSearch()" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 text-sm font-bold">
                        &times;
                    </button>
                </div>

                <button @click="selectFirstOrSearch()" class="btn-primary text-xs px-3 py-2 shrink-0">
                    Cari
                </button>
                <button @click="useCurrentLocation()" :disabled="locating" title="Gunakan lokasi GPS saya saat ini" class="p-2 text-[#2A6F97] hover:bg-blue-50 rounded-xl border border-[#74877B]/40 shrink-0 transition">
                    <svg :class="locating ? 'animate-spin text-[#1E7A4C]' : ''" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>

            <!-- Autocomplete Results Dropdown (Google Maps style) -->
            <div x-show="showSuggestions && suggestions.length > 0"
                 x-transition
                 @click.away="showSuggestions = false"
                 class="absolute top-full left-0 right-0 mt-1.5 bg-white rounded-2xl shadow-2xl border border-[#D5E0D9] overflow-hidden z-50 divide-y divide-gray-100 max-h-72 overflow-y-auto">
                <div class="px-3 py-1.5 bg-gray-50 text-[11px] font-bold text-[#4E6157] flex items-center justify-between">
                    <span>Saran Lokasi</span>
                    <span x-text="`${suggestions.length} ditemukan`"></span>
                </div>
                <template x-for="(s, idx) in suggestions" :key="s.id || idx">
                    <div @click="selectSuggestion(s)"
                         class="p-3 hover:bg-[#F2F7F4] cursor-pointer flex items-start gap-3 transition text-left group">
                        <div class="w-7 h-7 rounded-xl bg-gray-100 group-hover:bg-[#1E7A4C]/10 text-[#1E7A4C] flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-sm">📍</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-xs text-[#16251D] group-hover:text-[#1E7A4C] truncate" x-text="s.title"></p>
                            <p class="text-[11px] text-[#4E6157] truncate" x-text="s.subtitle || s.display_name"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Filter Controls: Layer Toggle & Radius & Halal & Urgensi & Basemap -->
        <div class="pointer-events-auto flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <!-- Layer Toggle: Makanan | Galang -->
            <div class="inline-flex rounded-xl bg-white/95 backdrop-blur-md p-1 shadow-card border border-[#D5E0D9] shrink-0">
                <button @click="layer = 'food'; loadData()"
                        :class="layer === 'food' ? 'bg-[#1E7A4C] text-white shadow-xs font-semibold' : 'text-[#4E6157] hover:text-[#16251D]'"
                        class="px-3 py-1 text-xs rounded-lg transition">
                    Makanan
                </button>
                <button @click="layer = 'campaigns'; loadData()"
                        :class="layer === 'campaigns' ? 'bg-[#2A6F97] text-white shadow-xs font-semibold' : 'text-[#4E6157] hover:text-[#16251D]'"
                        class="px-3 py-1 text-xs rounded-lg transition">
                    Galang
                </button>
            </div>

            <!-- Radius chips (visible when layer is food) -->
            <template x-if="layer === 'food'">
                <div class="flex items-center gap-1 bg-white/95 backdrop-blur-md p-1 rounded-xl shadow-card border border-[#D5E0D9] shrink-0">
                    <template x-for="r in [1, 3, 5, 10]" :key="r">
                        <button @click="radius = r; loadData()"
                                :class="radius === r ? 'bg-[#1E7A4C] text-white font-semibold' : 'text-[#4E6157] hover:bg-gray-100'"
                                class="px-2.5 py-1 text-xs rounded-lg transition"
                                x-text="`${r} km`">
                        </button>
                    </template>
                </div>
            </template>

            <!-- Halal filter toggle -->
            <button @click="halalOnly = !halalOnly; loadData()"
                    :class="halalOnly ? 'bg-[#1E7A4C] text-white font-semibold border-transparent' : 'bg-white/95 text-[#4E6157] border-[#D5E0D9]'"
                    class="px-2.5 py-1.5 text-xs rounded-xl shadow-card border shrink-0 transition flex items-center gap-1">
                <span>Halal</span>
            </button>

            <!-- Urgent filter (< 2 jam) -->
            <button @click="urgentOnly = !urgentOnly; filterListings()"
                    :class="urgentOnly ? 'bg-[#C93A2E] text-white font-semibold border-transparent' : 'bg-white/95 text-[#4E6157] border-[#D5E0D9]'"
                    class="px-2.5 py-1.5 text-xs rounded-xl shadow-card border shrink-0 transition flex items-center gap-1">
                <span>🔥 &lt; 2 Jam</span>
            </button>

            <!-- Basemap toggle (OSM vs Satelit) -->
            <button @click="toggleBasemap()"
                    :class="basemap === 'satellite' ? 'bg-[#16251D] text-white font-semibold' : 'bg-white/95 text-[#4E6157] border-[#D5E0D9]'"
                    class="px-2.5 py-1.5 text-xs rounded-xl shadow-card border shrink-0 transition flex items-center gap-1.5">
                <span x-text="basemap === 'satellite' ? '🛰️ Satelit' : '🗺️ Peta'"></span>
            </button>
        </div>

        <!-- Category Horizontal Filter Chips (Food mode) -->
        <template x-if="layer === 'food'">
            <div class="pointer-events-auto flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                <button @click="selectedCategory = ''; filterListings()"
                        :class="selectedCategory === '' ? 'bg-[#1E7A4C] text-white font-semibold' : 'bg-white/95 text-[#4E6157] hover:bg-gray-50 border border-[#D5E0D9]'"
                        class="px-3 py-1 text-xs rounded-full shadow-xs shrink-0 transition">
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button @click="selectedCategory = '{{ $cat->name }}'; filterListings()"
                            :class="selectedCategory === '{{ $cat->name }}' ? 'bg-[#1E7A4C] text-white font-semibold' : 'bg-white/95 text-[#4E6157] hover:bg-gray-50 border border-[#D5E0D9]'"
                            class="px-3 py-1 text-xs rounded-full shadow-xs shrink-0 transition">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </template>
    </div>

    <!-- SIDEBAR LIST (Desktop) / BOTTOM SHEET (Mobile) -->
    <div :class="sheetExpanded ? 'h-[75vh]' : 'h-48 lg:h-full'"
         class="w-full lg:w-[420px] bg-white border-t lg:border-t-0 lg:border-r border-[#D5E0D9] flex flex-col order-2 lg:order-1 transition-all duration-300 z-10 shrink-0">

        <!-- Mobile Pull Handle / Header -->
        <div class="lg:hidden p-2 flex flex-col items-center border-b border-[#D5E0D9] cursor-pointer"
             @click="sheetExpanded = !sheetExpanded">
            <div class="w-12 h-1.5 rounded-full bg-[#74877B]/40 mb-1"></div>
            <div class="flex items-center justify-between w-full px-4 text-xs font-bold text-[#4E6157]">
                <span x-text="layer === 'food' ? `${filteredItems.length} Makanan di Sekitar` : `${items.length} Galang Makanan`"></span>
                <span class="text-[#1E7A4C]" x-text="sheetExpanded ? 'Ciutkan' : 'Perluas'"></span>
            </div>
        </div>

        <!-- Desktop Sidebar Header -->
        <div class="hidden lg:flex items-center justify-between px-5 py-4 border-b border-[#D5E0D9]">
            <div>
                <h1 class="font-display font-bold text-lg text-[#16251D]" x-text="layer === 'food' ? 'Makanan Tersedia' : 'Galang Makanan'"></h1>
                <p class="text-xs text-[#4E6157]" x-text="`Pusat: ${currentLocationLabel}`"></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-[#1E7A4C]/10 text-[#14573A]"
                      x-text="`${filteredItems.length} item`"></span>
            </div>
        </div>

        <!-- LIST CONTAINER -->
        <div class="flex-1 overflow-y-auto p-3 space-y-3">
            <!-- Loading Skeleton -->
            <template x-if="loading">
                <div class="space-y-3">
                    <div class="h-24 bg-gray-100 rounded-2xl animate-pulse"></div>
                    <div class="h-24 bg-gray-100 rounded-2xl animate-pulse"></div>
                    <div class="h-24 bg-gray-100 rounded-2xl animate-pulse"></div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && filteredItems.length === 0">
                <div class="py-10 text-center px-4">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-[#F2F6F3] flex items-center justify-center text-[#4E6157]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-base text-[#16251D] mb-1">
                        Belum ada makanan dalam <span x-text="`${radius} km`"></span>
                    </h3>
                    <p class="text-xs text-[#4E6157] mb-4">
                        Coba klik titik lain di peta, perluas radius ke 5 atau 10 km, atau bagikan makanan berlebih milik Anda.
                    </p>
                    <div class="flex flex-col gap-2 max-w-xs mx-auto">
                        <button @click="radius = 10; loadData()" class="btn-secondary text-xs">
                            Perluas ke 10 km
                        </button>
                        <a href="{{ route('listings.create') }}" class="btn-primary text-xs">
                            Bagikan makanan sekarang
                        </a>
                    </div>
                </div>
            </template>

            <!-- Food Listing Cards -->
            <template x-if="!loading && layer === 'food'">
                <template x-for="item in filteredItems" :key="item.id">
                    <div :id="`card-${item.id}`"
                         @click="focusMarker(item)"
                         :class="selectedId === item.id ? 'ring-2 ring-[#2A6F97] bg-blue-50/30' : 'bg-white hover:border-[#1E7A4C]'"
                         class="p-3.5 rounded-2xl border border-[#D5E0D9] shadow-xs cursor-pointer transition flex items-center justify-between gap-3">
                        <!-- Left Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-[#4E6157]" x-text="item.category_name"></span>
                                <template x-if="item.halal_claim">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Halal</span>
                                </template>
                                <template x-if="item.is_verified">
                                    <span class="badge-verified text-[10px]">Terverifikasi</span>
                                </template>
                                <template x-if="item.is_owner">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800">Milik Anda</span>
                                </template>
                            </div>

                            <h3 class="font-display font-bold text-sm text-[#16251D] truncate" x-text="item.title"></h3>
                            <p class="text-xs text-[#4E6157] truncate" x-text="item.user_name"></p>

                            <div class="flex items-center gap-3 text-xs text-[#4E6157] mt-2">
                                <span class="font-bold text-[#1E7A4C]" x-text="`${item.portions_left} porsi`"></span>
                                <span>•</span>
                                <span x-text="`${item.distance_km} km`"></span>
                                <span>•</span>
                                <a :href="'https://www.google.com/maps/dir/?api=1&destination=' + item.lat + ',' + item.lng"
                                   target="_blank"
                                   @click.stop
                                   class="text-[11px] font-bold text-[#2A6F97] hover:underline inline-flex items-center gap-0.5">
                                    🧭 Rute
                                </a>
                            </div>

                            <template x-if="item.campaign_title">
                                <div class="mt-1.5 text-[11px] text-[#2A6F97] font-medium truncate">
                                    <span>🎯 Bagian dari galang: </span>
                                    <strong x-text="item.campaign_title"></strong>
                                </div>
                            </template>
                        </div>

                        <!-- Right: Signature Time Ring Mini -->
                        <div class="shrink-0 flex flex-col items-center">
                            <div class="w-12 h-12">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-200" stroke-width="3" stroke="currentColor" fill="none"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    <path :stroke="getTimeColor(item.remaining_minutes)" stroke-width="3" stroke-linecap="round" fill="none"
                                          :stroke-dasharray="`${Math.min(100, Math.max(0, (item.remaining_minutes / (item.total_minutes || 240)) * 100))}, 100`"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold mt-1" :style="`color: ${getTimeColor(item.remaining_minutes)}`"
                                  x-text="getTimeShort(item.remaining_minutes)"></span>
                        </div>
                    </div>
                </template>
            </template>

            <!-- Campaign Cards -->
            <template x-if="!loading && layer === 'campaigns'">
                <template x-for="item in items" :key="item.id">
                    <div :id="`camp-${item.id}`"
                         @click="focusMarker(item)"
                         class="p-4 rounded-2xl border border-[#D5E0D9] shadow-xs bg-white hover:border-[#2A6F97] transition space-y-2 cursor-pointer">
                        <div class="flex items-center justify-between text-xs">
                            <span class="badge-role text-[10px]" x-text="item.beneficiary_type_label"></span>
                            <span class="text-[#4E6157]" x-text="`Kec. ${item.district_name}`"></span>
                        </div>
                        <h3 class="font-display font-bold text-sm text-[#16251D]" x-text="item.title"></h3>
                        <p class="text-xs text-[#4E6157] line-clamp-2" x-text="item.excerpt"></p>

                        <!-- Progress bar -->
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-1.5">
                            <div class="bg-[#1E7A4C] h-2 rounded-full transition-all duration-500"
                                 :style="`width: ${item.progress_percent}%`"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-[#4E6157]">
                            <span><strong class="text-[#16251D]" x-text="item.collected_portions"></strong> dari <span x-text="`${item.target_portions} porsi`"></span></span>
                            <a :href="item.url" class="font-bold text-[#2A6F97] hover:underline">Baca galang &rarr;</a>
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </div>

    <!-- MAP CONTAINER -->
    <div class="flex-1 order-1 lg:order-2 h-full w-full relative z-0">
        <div id="locabite-map" class="h-full w-full"></div>

        <!-- Floating Quick Recenter & Info Indicator on Map -->
        <div class="absolute bottom-4 left-4 z-10 pointer-events-auto bg-white/95 backdrop-blur-md px-3 py-2 rounded-2xl shadow-card border border-[#D5E0D9] flex items-center gap-2.5 text-xs text-[#16251D]">
            <span class="w-2.5 h-2.5 rounded-full bg-[#1E7A4C] animate-ping"></span>
            <div>
                <p class="font-bold text-[11px] leading-tight">Klik peta di mana saja untuk pindah titik</p>
                <p class="text-[10px] text-[#4E6157]" x-text="`Pusat: ${currentLocationLabel} (r=${radius}km)`"></p>
            </div>
            <button @click="resetToDefaultCenter()" title="Kembali ke titik awal" class="p-1 hover:bg-gray-100 rounded-lg text-gray-500">
                ↺
            </button>
        </div>

        <!-- Floating GPS Action Button -->
        <button @click="useCurrentLocation()"
                :disabled="locating"
                title="Pusatkan ke lokasi saya saat ini (GPS)"
                class="absolute bottom-24 right-3 z-10 pointer-events-auto w-11 h-11 bg-white hover:bg-gray-50 text-[#1E7A4C] rounded-2xl shadow-card border border-[#D5E0D9] flex items-center justify-center transition active:scale-95">
            <svg :class="locating ? 'animate-spin text-[#1E7A4C]' : ''" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('homeMap', () => ({
        map: null,
        osmLayer: null,
        satLayer: null,
        basemap: 'standard', // 'standard' or 'satellite'
        markersLayer: null,
        userMarker: null,
        userRadiusCircle: null,
        initialLat: {{ $initialLat }},
        initialLng: {{ $initialLng }},
        userLat: {{ $initialLat }},
        userLng: {{ $initialLng }},
        currentLocationLabel: 'Pusat Area',
        radius: 5,
        layer: 'food', // 'food' or 'campaigns'
        halalOnly: false,
        urgentOnly: false,
        selectedCategory: '',
        searchQuery: '',
        suggestions: [],
        showSuggestions: false,
        loading: false,
        locating: false,
        sheetExpanded: false,
        items: [],
        filteredItems: [],
        selectedId: null,
        pollTimer: null,

        init() {
            this.initMap();
            this.loadData();
            this.reverseGeocodeLabel(this.userLat, this.userLng);

            // 60-second periodic poll when tab is visible
            this.pollTimer = setInterval(() => {
                if (!document.hidden) this.loadData();
            }, 60000);

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.loadData();
            });
        },

        initMap() {
            this.map = L.map('locabite-map', {
                zoomControl: false,
            }).setView([this.userLat, this.userLng], 14);

            L.control.zoom({ position: 'bottomright' }).addTo(this.map);

            // OpenStreetMap Standard Tiles
            this.osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            // Esri Satellite Tiles
            this.satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: '&copy; Esri World Imagery'
            });

            this.markersLayer = L.layerGroup().addTo(this.map);

            // Render Center User Pin & Radius Circle
            this.updateUserMarker();

            // Click anywhere on map to set fixed search center (Google Maps style)
            this.map.on('click', (e) => {
                this.setCenterLocation(e.latlng.lat, e.latlng.lng);
            });
        },

        toggleBasemap() {
            if (this.basemap === 'standard') {
                this.map.removeLayer(this.osmLayer);
                this.satLayer.addTo(this.map);
                this.basemap = 'satellite';
            } else {
                this.map.removeLayer(this.satLayer);
                this.osmLayer.addTo(this.map);
                this.basemap = 'standard';
            }
        },

        setCenterLocation(lat, lng) {
            this.userLat = lat;
            this.userLng = lng;
            this.updateUserMarker();
            this.loadData();
            this.reverseGeocodeLabel(lat, lng);
        },

        async reverseGeocodeLabel(lat, lng) {
            try {
                const res = await fetch(`/api/geocoding/reverse?lat=${lat}&lng=${lng}`);
                if (res.ok) {
                    const data = await res.json();
                    this.currentLocationLabel = data.short_address || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                }
            } catch (e) {
                this.currentLocationLabel = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            }
        },

        resetToDefaultCenter() {
            this.setCenterLocation(this.initialLat, this.initialLng);
            this.map.flyTo([this.initialLat, this.initialLng], 14);
        },

        updateUserMarker() {
            if (this.userMarker) this.map.removeLayer(this.userMarker);
            if (this.userRadiusCircle) this.map.removeLayer(this.userRadiusCircle);

            // Draggable custom center marker
            const userIcon = L.divIcon({
                className: 'custom-user-marker',
                html: `
                    <div style="position:relative; width: 22px; height: 22px; display:flex; align-items:center; justify-content:center;">
                        <span style="position:absolute; width: 100%; height: 100%; border-radius: 50%; background: #2A6F97; opacity: 0.4; animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                        <div style="width: 14px; height: 14px; border-radius: 50%; background: #2A6F97; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>
                    </div>
                `,
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });

            this.userMarker = L.marker([this.userLat, this.userLng], {
                icon: userIcon,
                draggable: true
            }).addTo(this.map);

            this.userMarker.bindTooltip("Titik Pusat Pencarian (Geser atau klik peta untuk ubah)", {
                direction: 'top',
                offset: [0, -10]
            });

            this.userMarker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.setCenterLocation(pos.lat, pos.lng);
            });

            // Semi-transparent radius circle
            this.userRadiusCircle = L.circle([this.userLat, this.userLng], {
                radius: this.radius * 1000,
                color: '#2A6F97',
                fillColor: '#2A6F97',
                fillOpacity: 0.08,
                weight: 1.5,
                dashArray: '4, 4'
            }).addTo(this.map);
        },

        async loadData() {
            this.loading = true;
            this.updateUserMarker();

            try {
                if (this.layer === 'food') {
                    const url = new URL('/api/listings/nearby', window.location.origin);
                    url.searchParams.set('lat', this.userLat);
                    url.searchParams.set('lng', this.userLng);
                    url.searchParams.set('radius', this.radius);
                    if (this.halalOnly) url.searchParams.set('halal', '1');

                    const res = await fetch(url);
                    const data = await res.json();
                    this.items = data.listings || [];
                    this.filterListings();
                } else {
                    const res = await fetch('/api/campaigns/nearby');
                    const data = await res.json();
                    this.items = data.campaigns || [];
                    this.filteredItems = this.items;
                    this.renderCampaignMarkers(this.items);
                }
            } catch (err) {
                console.error("Gagal memuat data:", err);
            } finally {
                this.loading = false;
            }
        },

        filterListings() {
            let list = [...this.items];

            if (this.selectedCategory) {
                list = list.filter(item => item.category_name === this.selectedCategory);
            }

            if (this.urgentOnly) {
                list = list.filter(item => item.remaining_minutes <= 120);
            }

            this.filteredItems = list;
            this.renderFoodMarkers(this.filteredItems);
        },

        renderFoodMarkers(listings) {
            this.markersLayer.clearLayers();

            listings.forEach(l => {
                const color = this.getTimeColor(l.remaining_minutes);
                const shortTime = this.getTimeShort(l.remaining_minutes);

                const markerHtml = `
                    <div style="display:flex; flex-direction:column; align-items:center; cursor:pointer;">
                        <div style="width:28px; height:28px; border-radius:50% 50% 50% 0; transform:rotate(-45deg); background:${color}; border:2px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3); display:grid; place-items:center;">
                            <span style="transform:rotate(45deg); color:white; font-size:11px; font-weight:bold;">${l.portions_left}</span>
                        </div>
                        <div style="background:#16251D; color:white; font-size:10px; font-weight:bold; padding:1px 5px; border-radius:99px; margin-top:2px; box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                            ${shortTime}
                        </div>
                    </div>
                `;

                const icon = L.divIcon({
                    className: 'food-pin',
                    html: markerHtml,
                    iconSize: [36, 44],
                    iconAnchor: [18, 40]
                });

                const m = L.marker([l.lat, l.lng], { icon: icon });

                // Popup with directions button to Google Maps
                const popupContent = `
                    <div style="font-family:'Atkinson Hyperlegible',sans-serif; min-width:190px;">
                        <h4 style="font-weight:bold; font-size:13px; margin:0 0 4px 0; color:#16251D;">${l.title}</h4>
                        <div style="font-size:11px; color:#4E6157; margin-bottom:6px;">
                            ${l.portions_left} porsi • ${l.distance_km} km
                        </div>
                        <div style="font-size:11px; font-weight:bold; color:${color}; margin-bottom:8px;">
                            Sisa waktu: ${shortTime}
                        </div>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <a href="${l.detail_url}" style="display:inline-block; background:#1E7A4C; color:white; padding:4px 10px; border-radius:8px; font-size:11px; text-decoration:none; font-weight:bold;">
                                Detail
                            </a>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${l.lat},${l.lng}" target="_blank" style="display:inline-block; background:#2A6F97; color:white; padding:4px 10px; border-radius:8px; font-size:11px; text-decoration:none; font-weight:bold;">
                                🧭 Rute
                            </a>
                        </div>
                    </div>
                `;
                m.bindPopup(popupContent);

                m.on('click', () => {
                    this.selectedId = l.id;
                    const card = document.getElementById(`card-${l.id}`);
                    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });

                this.markersLayer.addLayer(m);
            });
        },

        renderCampaignMarkers(campaigns) {
            this.markersLayer.clearLayers();

            campaigns.forEach(c => {
                const markerHtml = `
                    <div style="display:flex; flex-direction:column; align-items:center; cursor:pointer;">
                        <div style="width:30px; height:30px; border-radius:8px; background:#2A6F97; border:2px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3); display:grid; place-items:center; color:white; font-size:14px;">
                            🏛️
                        </div>
                    </div>
                `;

                const icon = L.divIcon({
                    className: 'camp-pin',
                    html: markerHtml,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                const m = L.marker([c.lat, c.lng], { icon: icon });
                m.bindPopup(`
                    <div style="font-family:'Atkinson Hyperlegible',sans-serif; min-width:190px;">
                        <span style="font-size:10px; background:#2A6F97; color:white; padding:2px 6px; border-radius:4px;">Galang</span>
                        <h4 style="font-weight:bold; font-size:13px; margin:4px 0; color:#16251D;">${c.title}</h4>
                        <div style="font-size:11px; color:#4E6157; margin-bottom:6px;">
                            ${c.collected_portions} / ${c.target_portions} porsi (${c.progress_percent}%)
                        </div>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <a href="${c.url}" style="display:inline-block; background:#2A6F97; color:white; padding:4px 10px; border-radius:8px; font-size:11px; text-decoration:none; font-weight:bold;">
                                Baca galang
                            </a>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${c.lat},${c.lng}" target="_blank" style="display:inline-block; background:#16251D; color:white; padding:4px 10px; border-radius:8px; font-size:11px; text-decoration:none; font-weight:bold;">
                                🧭 Rute
                            </a>
                        </div>
                    </div>
                `);

                this.markersLayer.addLayer(m);
            });
        },

        focusMarker(item) {
            this.selectedId = item.id;
            this.map.panTo([item.lat, item.lng], { animate: true });
        },

        useCurrentLocation() {
            if (!navigator.geolocation) {
                alert("Peramban Anda tidak mendukung geolokasi GPS.");
                return;
            }

            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.locating = false;
                    this.setCenterLocation(pos.coords.latitude, pos.coords.longitude);
                    this.map.flyTo([this.userLat, this.userLng], 15);
                },
                (err) => {
                    this.locating = false;
                    alert("Akses GPS ditolak atau tidak tersedia. Gunakan pencarian alamat di atas.");
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        },

        async fetchSuggestions() {
            const q = this.searchQuery.trim();
            if (q.length < 2) {
                this.suggestions = [];
                this.showSuggestions = false;
                return;
            }

            try {
                const res = await fetch(`/api/geocoding/search?q=${encodeURIComponent(q)}`);
                if (res.ok) {
                    this.suggestions = await res.json();
                    this.showSuggestions = this.suggestions.length > 0;
                }
            } catch (e) {
                // Ignore network hiccups
            }
        },

        selectSuggestion(s) {
            this.searchQuery = s.title;
            this.showSuggestions = false;
            this.setCenterLocation(s.lat, s.lng);
            this.map.flyTo([s.lat, s.lng], 15);
        },

        selectFirstOrSearch() {
            if (this.suggestions.length > 0) {
                this.selectSuggestion(this.suggestions[0]);
            } else {
                this.searchLocationDirect();
            }
        },

        async searchLocationDirect() {
            if (!this.searchQuery.trim()) return;

            try {
                const res = await fetch(`/api/geocoding/search?q=${encodeURIComponent(this.searchQuery)}`);
                const results = await res.json();
                if (results && results.length > 0) {
                    this.selectSuggestion(results[0]);
                } else {
                    alert("Alamat tidak ditemukan. Coba gunakan nama jalan utama, kecamatan, atau kota.");
                }
            } catch (e) {
                alert("Pencarian lokasi gagal. Silakan coba sesaat lagi.");
            }
        },

        clearSearch() {
            this.searchQuery = '';
            this.suggestions = [];
            this.showSuggestions = false;
        },

        getTimeColor(remainingMins) {
            if (remainingMins <= 0) return 'var(--color-ink-soft)';
            if (remainingMins < 60) return '#C93A2E'; // Cabai (<1h)
            if (remainingMins <= 180) return '#B45309'; // Kunyit Tua (1-3h)
            return '#23865A'; // Hijau Segar (>3h)
        },

        getTimeShort(remainingMins) {
            if (remainingMins <= 0) return 'Habis';
            const h = Math.floor(remainingMins / 60);
            const m = remainingMins % 60;
            return h >= 1 ? (m ? `${h}j${m}` : `${h}j`) : `${m}m`;
        }
    }));
});
</script>
@endpush

</x-layouts.app>
