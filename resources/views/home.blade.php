<x-layouts.app title="Locabite — Peta Makanan Berlebih & Penyelamat Pangan">

<div x-data="homeMap()" x-init="init()" class="relative flex-1 flex flex-col lg:flex-row h-[calc(100vh-4rem)] w-full max-w-full overflow-hidden">

    <!-- Top Floating Filter & Search Bar on Map (Google Maps / Apple Maps Class) -->
    <div class="absolute top-3 inset-x-3 md:inset-x-6 z-20 pointer-events-none flex flex-col gap-2 max-w-2xl mx-auto">
        <!-- Search bar with Autocomplete Dropdown & Directions Mode (Google Maps Class) -->
        <div class="pointer-events-auto relative">
            <!-- Normal Search Mode Header -->
            <div x-show="!directionsMode && !routeInfo"
                 class="bg-white/95 backdrop-blur-2xl rounded-2xl shadow-[0_12px_36px_-6px_rgba(22,37,29,0.14),0_2px_8px_rgba(22,37,29,0.04)] border border-[#D5E0D9]/80 p-1.5 sm:p-2 flex items-center gap-1.5 sm:gap-2 transition-all focus-within:border-[#1E7A4C]/60 focus-within:shadow-[0_16px_40px_-6px_rgba(30,122,76,0.20)]">
                <div class="relative flex-1 min-w-0">
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="fetchSuggestions()"
                           @focus="if (suggestions.length > 0) showSuggestions = true"
                           @keydown.enter.prevent="selectFirstOrSearch()"
                           placeholder="Cari jalan, kelurahan, atau kecamatan (misal: Tembalang, Alun-alun)..."
                           class="w-full h-9 pl-9 pr-8 text-xs sm:text-sm rounded-xl border border-transparent text-[#16251D] placeholder:text-[#4E6157]/60 focus:outline-none bg-gray-50/80 focus:bg-white focus:border-[#74877B] transition-all">
                    <svg class="w-4 h-4 text-[#4E6157] absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <!-- Clear search button -->
                    <button type="button" x-show="searchQuery" @click="clearSearch()" class="absolute right-2 top-1.5 text-gray-400 hover:text-gray-700 w-6 h-6 rounded-full hover:bg-gray-100 flex items-center justify-center transition" title="Hapus pencarian">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Google Maps Directions Mode Trigger Button (Button 1: Rute) -->
                <button type="button"
                        @click="toggleDirectionsMode()"
                        class="h-9 px-3 rounded-xl bg-emerald-50/80 hover:bg-emerald-100/90 text-[#14573A] border border-emerald-200/90 hover:border-emerald-300 font-bold text-xs flex items-center gap-1.5 transition-all shadow-2xs active:scale-95 shrink-0 group cursor-pointer"
                        title="Buka Mode Petunjuk Arah / Rute">
                    <svg class="w-4 h-4 text-[#1E7A4C] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span class="hidden sm:inline">Rute</span>
                </button>

                <!-- Search Action Button (Button 2: Cari) -->
                <button type="button"
                        @click="selectFirstOrSearch()"
                        class="h-9 px-3.5 sm:px-4 rounded-xl bg-[#1E7A4C] hover:bg-[#14573A] text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs hover:shadow-sm active:scale-95 shrink-0 cursor-pointer"
                        title="Cari lokasi">
                    <span>Cari</span>
                </button>

                <!-- Current Location GPS Button (Google Maps "My Location" Crosshair) -->
                <button type="button"
                        @click="useCurrentLocation()"
                        :disabled="locating"
                        class="w-9 h-9 rounded-xl bg-gray-50/80 hover:bg-emerald-50 text-[#16251D] hover:text-[#1E7A4C] border border-[#D5E0D9] hover:border-emerald-300 flex items-center justify-center transition-all shadow-2xs active:scale-95 shrink-0 group cursor-pointer"
                        title="Gunakan lokasi GPS saya saat ini">
                    <svg :class="locating ? 'animate-spin' : 'group-hover:scale-110'" class="w-4.5 h-4.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="7" stroke-width="2" />
                        <circle cx="12" cy="12" r="3" fill="currentColor" stroke="none" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                    </svg>
                </button>
            </div>

            <!-- Google Maps Two-Point Directions Panel (Directions Mode) -->
            <div x-show="directionsMode"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
                 class="bg-white/98 backdrop-blur-2xl rounded-3xl shadow-[0_20px_50px_-10px_rgba(22,37,29,0.22)] border border-[#D5E0D9] p-3 space-y-2.5">
                
                <!-- Directions Header: Mode & Close -->
                <div class="flex items-center justify-between pb-1 border-b border-gray-100">
                    <div class="flex items-center gap-1 bg-gray-100/80 p-0.5 rounded-full border border-gray-200/60">
                        <button type="button"
                                @click="switchRouteMode('driving')"
                                :class="routeMode === 'driving' ? 'bg-[#1E7A4C] text-white shadow-2xs font-bold' : 'text-[#4E6157] hover:text-[#16251D] font-medium'"
                                class="px-3 py-1 rounded-full text-xs flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-9 4h10M5 16h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2zm2 0v2m10-2v2"/>
                            </svg>
                            <span>Kendaraan</span>
                        </button>
                        <button type="button"
                                @click="switchRouteMode('walking')"
                                :class="routeMode === 'walking' ? 'bg-[#2A6F97] text-white shadow-2xs font-bold' : 'text-[#4E6157] hover:text-[#16251D] font-medium'"
                                class="px-3 py-1 rounded-full text-xs flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a2 2 0 11-4 0 2 2 0 014 0zM9 14l2-4 3 2 1 4m-4-2v6m-2 0h4"/>
                            </svg>
                            <span>Jalan Kaki</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-[#14573A] uppercase tracking-wider hidden sm:inline">Navigasi Rute</span>
                        <button type="button" @click="toggleDirectionsMode()" class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 text-[#4E6157] flex items-center justify-center transition" title="Tutup Mode Rute">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Origin & Destination Inputs with Swap Button -->
                <div class="flex items-center gap-2">
                    <div class="flex flex-col gap-2 flex-1">
                        <!-- Origin Input (Point A) -->
                        <div class="relative flex items-center">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow-2xs absolute left-3"></span>
                            <input type="text"
                                   x-model="originLabel"
                                   readonly
                                   @click="useCurrentLocation()"
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50/80 font-medium text-[#16251D] cursor-pointer hover:bg-gray-100 transition"
                                   title="Klik untuk pusatkan ke GPS">
                        </div>

                        <!-- Destination Input (Point B) -->
                        <div class="relative flex items-center">
                            <span class="w-3 h-3 rounded-full bg-red-500 border-2 border-white shadow-2xs absolute left-3"></span>
                            <input type="text"
                                   x-model="searchQuery"
                                   @input.debounce.300ms="fetchSuggestions()"
                                   @focus="if (suggestions.length > 0) showSuggestions = true"
                                   @keydown.enter.prevent="selectFirstOrSearch()"
                                   placeholder="Pilih tujuan atau ketik tempat..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white font-medium text-[#16251D] focus:ring-2 focus:ring-[#1E7A4C] focus:outline-none transition">
                        </div>
                    </div>

                    <!-- Swap Direction Button -->
                    <button type="button"
                            @click="swapDirections()"
                            class="w-9 h-16 rounded-2xl bg-gray-100 hover:bg-gray-200 text-[#4E6157] hover:text-[#16251D] flex items-center justify-center transition active:scale-95 shrink-0"
                            title="Tukar Titik Awal dan Tujuan">
                        <svg class="w-4 h-4 transition-transform active:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Autocomplete Results Dropdown with Google Maps Style Route & View Actions -->
            <div x-show="showSuggestions && suggestions.length > 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-98"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-98"
                 @click.away="showSuggestions = false"
                 class="absolute top-full left-0 right-0 mt-2 bg-white/95 backdrop-blur-2xl rounded-3xl shadow-[0_24px_54px_-8px_rgba(22,37,29,0.22),0_8px_18px_rgba(22,37,29,0.08)] border border-[#D5E0D9]/70 overflow-hidden z-[1100] p-2 space-y-1 max-h-[400px] overflow-y-auto">
                
                <div class="px-3 py-2 flex items-center justify-between border-b border-gray-100/80 mb-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#1E7A4C] animate-pulse"></span>
                        <span class="text-[11px] font-bold text-[#16251D] tracking-wide uppercase">Hasil Pencarian Tujuan</span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#1E7A4C] border border-emerald-100" x-text="`${suggestions.length} opsi ditemukan`"></span>
                </div>

                <template x-for="(s, idx) in suggestions" :key="s.id || idx">
                    <div @click="selectSuggestion(s)"
                         class="group relative p-2.5 rounded-2xl hover:bg-linear-to-r hover:from-emerald-50/90 hover:via-teal-50/40 hover:to-transparent cursor-pointer transition-all duration-200 flex items-center justify-between gap-2.5 border border-transparent hover:border-emerald-200/60 hover:shadow-xs active:scale-[0.99]">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-linear-to-br from-emerald-100/80 to-teal-50 text-[#1E7A4C] group-hover:from-[#1E7A4C] group-hover:to-[#14573A] group-hover:text-white flex items-center justify-center shrink-0 transition-all duration-200 shadow-xs group-hover:scale-105">
                                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="font-display font-bold text-sm text-[#16251D] group-hover:text-[#1E7A4C] truncate transition-colors" x-text="s.title"></h4>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md bg-gray-100 text-[#4E6157] group-hover:bg-emerald-100 group-hover:text-[#14573A] transition-colors shrink-0" x-text="s.type_label || 'Wilayah'"></span>
                                    <template x-if="getDistanceKm(s.lat, s.lng)">
                                        <span class="text-[10px] font-semibold text-[#1E7A4C] bg-emerald-50 px-1.5 py-0.2 rounded shrink-0" x-text="`~${getDistanceKm(s.lat, s.lng)} km`"></span>
                                    </template>
                                </div>
                                <p class="text-xs text-[#4E6157] group-hover:text-[#16251D] truncate transition-colors mt-0.5" x-text="s.subtitle || s.display_name"></p>
                            </div>
                        </div>

                        <!-- Right Quick Actions: 1-Click "Rute" + "Pilih" -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Direct Route Button (Google Maps Directions) -->
                            <button type="button"
                                    @click.stop="routeToSuggestion(s)"
                                    class="px-2.5 py-1 rounded-xl bg-emerald-50 hover:bg-[#1E7A4C] text-[#1E7A4C] hover:text-white text-xs font-bold border border-emerald-200/80 transition flex items-center gap-1 active:scale-95 shadow-2xs"
                                    title="Tampilkan rute jalan ke tempat ini">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                                <span>Rute</span>
                            </button>

                            <!-- View Place Action -->
                            <div class="hidden sm:flex items-center text-[#4E6157] group-hover:text-[#1E7A4C] px-1 py-1 text-xs font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="px-3 py-2 bg-gray-50/70 rounded-2xl border-t border-gray-100/80 mt-1 flex items-center justify-between text-[11px] text-[#4E6157]">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ketuk <strong>Rute</strong> untuk panduan jalan, atau nama tempat untuk lihat area
                    </span>
                    <span class="hidden sm:inline font-mono text-[10px] bg-white px-2 py-0.5 rounded-md border border-gray-200">ESC tutup</span>
                </div>
            </div>
        </div>

        <!-- Sleek Single-Row Aesthetic Filter Strip (Google Maps & Apple Maps Class) -->
        <div x-show="!routeInfo"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="pointer-events-auto flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
            <!-- Layer Segmented Switcher: Makanan | Galang -->
            <div class="inline-flex rounded-full bg-white/95 backdrop-blur-md p-1 shadow-sm border border-[#D5E0D9] shrink-0">
                <button @click="layer = 'food'; loadData()"
                        :class="layer === 'food' ? 'bg-[#1E7A4C] text-white shadow-2xs font-bold' : 'text-[#4E6157] hover:text-[#16251D] font-medium'"
                        class="px-3 py-1 text-xs rounded-full transition">
                    Makanan
                </button>
                <button @click="layer = 'campaigns'; loadData()"
                        :class="layer === 'campaigns' ? 'bg-[#2A6F97] text-white shadow-2xs font-bold' : 'text-[#4E6157] hover:text-[#16251D] font-medium'"
                        class="px-3 py-1 text-xs rounded-full transition">
                    Galang
                </button>
            </div>

            <!-- Radius Quick Filter (Cycle Pill) -->
            <template x-if="layer === 'food'">
                <button @click="cycleRadius()"
                        class="px-3 py-1.5 text-xs font-semibold rounded-full bg-white/95 backdrop-blur-md text-[#16251D] border border-[#D5E0D9] shadow-sm hover:border-[#1E7A4C] shrink-0 transition flex items-center gap-1.5 active:scale-95"
                        title="Klik untuk ubah radius pencarian">
                    <svg class="w-3.5 h-3.5 text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" stroke-width="2"/>
                    </svg>
                    <span x-text="`Radius ${radius} km`"></span>
                </button>
            </template>

            <!-- Halal filter toggle -->
            <button @click="halalOnly = !halalOnly; loadData()"
                    :class="halalOnly ? 'bg-emerald-700 text-white font-bold border-transparent shadow-xs' : 'bg-white/95 text-[#4E6157] border-[#D5E0D9] shadow-sm'"
                    class="px-3 py-1.5 text-xs rounded-full border shrink-0 transition flex items-center gap-1.5 font-medium active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Halal</span>
            </button>

            <!-- Urgent filter (< 2 jam) -->
            <button @click="urgentOnly = !urgentOnly; filterListings()"
                    :class="urgentOnly ? 'bg-[#C93A2E] text-white font-bold border-transparent shadow-xs' : 'bg-white/95 text-[#4E6157] border-[#D5E0D9] shadow-sm'"
                    class="px-3 py-1.5 text-xs rounded-full border shrink-0 transition flex items-center gap-1.5 font-medium active:scale-95">
                <svg class="w-3.5 h-3.5 text-current shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>&lt; 2 Jam</span>
            </button>

            <!-- Divider -->
            <div class="h-5 w-px bg-gray-300/60 shrink-0 mx-0.5"></div>

            <!-- Category Chips in the same continuous sleek scrollable strip -->
            <template x-if="layer === 'food'">
                <div class="flex items-center gap-1.5 shrink-0">
                    <button @click="selectedCategory = ''; filterListings()"
                            :class="selectedCategory === '' ? 'bg-[#1E7A4C] text-white font-bold shadow-2xs' : 'bg-white/95 text-[#4E6157] hover:bg-gray-50 border border-[#D5E0D9] shadow-sm'"
                            class="px-3 py-1.5 text-xs rounded-full shrink-0 transition active:scale-95">
                        Semua Kategori
                    </button>
                    @foreach($categories as $cat)
                        <button @click="selectedCategory = '{{ $cat->name }}'; filterListings()"
                                :class="selectedCategory === '{{ $cat->name }}' ? 'bg-[#1E7A4C] text-white font-bold shadow-2xs' : 'bg-white/95 text-[#4E6157] hover:bg-gray-50 border border-[#D5E0D9] shadow-sm'"
                                class="px-3 py-1.5 text-xs rounded-full shrink-0 transition active:scale-95">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </template>
        </div>
    </div>

    <!-- SIDEBAR LIST (Desktop) / BOTTOM SHEET (Mobile) -->
    <div :class="[
            sheetExpanded ? 'h-[75vh]' : 'h-48 lg:h-full',
            desktopSidebarOpen ? 'lg:w-[420px] lg:min-w-[420px] lg:max-w-[420px]' : 'lg:w-0 lg:min-w-0 lg:max-w-0 lg:overflow-hidden lg:border-r-0'
         ]"
         class="relative w-full bg-white border-t lg:border-t-0 lg:border-r border-[#D5E0D9] flex flex-col order-2 lg:order-1 transition-all duration-300 z-10 shrink-0">

        <!-- Desktop Collapse / Expand Handle Button (World-Class Floating Circle) -->
        <button type="button"
                @click="toggleDesktopSidebar()"
                class="hidden lg:flex absolute -right-4 top-1/2 -translate-y-1/2 z-30 w-8 h-8 rounded-full bg-white border border-[#D5E0D9] shadow-[0_2px_10px_rgba(22,37,29,0.14)] items-center justify-center text-[#4E6157] hover:text-[#1E7A4C] hover:border-[#1E7A4C] hover:bg-emerald-50 transition-all duration-200 active:scale-90 hover:scale-110 cursor-pointer group"
                :title="desktopSidebarOpen ? 'Ciutkan panel daftar' : 'Buka panel daftar'">
            <svg :class="desktopSidebarOpen ? '' : 'rotate-180'" class="w-4 h-4 transition-transform duration-300 text-[#4E6157] group-hover:text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

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
        <div class="hidden lg:flex items-center justify-between px-5 py-3.5 border-b border-[#D5E0D9] bg-gray-50/60">
            <div>
                <h1 class="font-display font-bold text-base text-[#16251D]" x-text="layer === 'food' ? 'Penyelamat Makanan' : 'Galang Makanan'"></h1>
                <p class="text-xs text-[#4E6157] truncate max-w-[200px]" x-text="`Pusat: ${currentLocationLabel}`"></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#14573A] border border-emerald-200/80"
                      x-text="`${filteredItems.length} opsi`"></span>
                <button type="button"
                        @click="toggleDesktopSidebar(false)"
                        class="w-7 h-7 rounded-lg text-[#4E6157] hover:text-[#16251D] hover:bg-gray-200/70 flex items-center justify-center transition cursor-pointer"
                        title="Ciutkan panel daftar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
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
                         @click="openPlaceDetail(item)"
                         :class="[
                            selectedId === item.id ? 'ring-2 ring-[#2A6F97] bg-blue-50/40 shadow-card border-[#2A6F97]' : 'bg-white hover:border-[#1E7A4C]/70 hover:shadow-card hover:-translate-y-0.5 border-[#D5E0D9]',
                            item.remaining_minutes < 60 ? 'border-red-200 bg-linear-to-r from-red-50/20 via-white to-white' : ''
                         ]"
                         class="p-4 rounded-2xl border shadow-xs cursor-pointer transition-all duration-200 space-y-3 group">
                        
                        <!-- Top Header: Category Chips & Aesthetic Urgency Pill -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-gray-100 text-[#4E6157] group-hover:bg-emerald-50 group-hover:text-[#1E7A4C] transition-colors truncate" x-text="item.category_name"></span>
                                <template x-if="item.halal_claim">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/80">Halal</span>
                                </template>
                                <template x-if="item.is_verified">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-[#2A6F97] border border-blue-200/80 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Terverifikasi
                                    </span>
                                </template>
                                <template x-if="item.is_owner">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200">Milik Anda</span>
                                </template>
                            </div>

                            <!-- Aesthetic Urgency Pill Badge -->
                            <div class="shrink-0">
                                <template x-if="item.remaining_minutes < 60">
                                    <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-200 text-[11px] font-bold flex items-center gap-1.5 animate-pulse shadow-2xs">
                                        <svg class="w-3 h-3 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span x-text="`Sisa ${getTimeShort(item.remaining_minutes)}`"></span>
                                    </span>
                                </template>
                                <template x-if="item.remaining_minutes >= 60 && item.remaining_minutes <= 180">
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold flex items-center gap-1.5 shadow-2xs">
                                        <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span x-text="`Sisa ${getTimeShort(item.remaining_minutes)}`"></span>
                                    </span>
                                </template>
                                <template x-if="item.remaining_minutes > 180">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-[#14573A] border border-emerald-200 text-[11px] font-bold flex items-center gap-1.5 shadow-2xs">
                                        <svg class="w-3 h-3 text-[#1E7A4C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span x-text="`Sisa ${getTimeShort(item.remaining_minutes)}`"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Title & Provider (Clickable to open rich place sheet) -->
                        <div>
                            <h3 class="font-display font-bold text-sm text-[#16251D] group-hover:text-[#1E7A4C] transition-colors line-clamp-2 leading-snug cursor-pointer"
                                @click.stop="openPlaceDetail(item)"
                                x-text="item.title"></h3>
                            <div class="flex items-center gap-1.5 text-xs text-[#4E6157] mt-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span class="truncate" x-text="item.user_name"></span>
                            </div>
                        </div>

                        <!-- Meta Row: Portions, Distance, and Action Buttons -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs bg-emerald-100/70 text-[#14573A] px-2.5 py-0.5 rounded-lg border border-emerald-200/60" x-text="`${item.portions_left} porsi`"></span>
                                <span class="text-[#4E6157] font-medium flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span x-text="`${item.distance_km} km`"></span>
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <!-- Direct In-Map Road Route Button -->
                                <button type="button"
                                        @click.stop="drawRoute(item, 'driving')"
                                        title="Tampilkan rute jalan di peta"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold text-[#1E7A4C] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 transition active:scale-95 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span>Rute</span>
                                </button>

                                <!-- Rich Place Detail Button -->
                                <button type="button"
                                        @click.stop="openPlaceDetail(item)"
                                        title="Buka info tempat & alamat lengkap"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold text-[#2A6F97] bg-blue-50 hover:bg-blue-100 border border-blue-200/80 transition active:scale-95 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Info</span>
                                </button>
                            </div>
                        </div>

                        <!-- Associated Campaign Strip (if any) -->
                        <template x-if="item.campaign_title">
                            <div class="p-2 bg-blue-50/60 rounded-xl border border-blue-100 text-[11px] text-[#2A6F97] flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#2A6F97] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span class="truncate">Galang: <strong x-text="item.campaign_title"></strong></span>
                            </div>
                        </template>
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
    <div class="flex-1 min-w-0 order-1 lg:order-2 h-full relative z-0 overflow-hidden">
        <div id="locabite-map" class="h-full w-full relative z-0 isolate"></div>

        <!-- Floating Active Route HUD (Navigation like Google Maps) -->
        <div x-show="routeInfo"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             class="absolute top-3 sm:top-4 inset-x-3 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-[460px] max-w-[calc(100vw-1.5rem)] z-[1000] pointer-events-auto bg-white/98 backdrop-blur-2xl rounded-3xl shadow-[0_20px_50px_-10px_rgba(22,37,29,0.25),0_4px_12px_rgba(22,37,29,0.08)] border border-emerald-500/40 p-4 space-y-3">
            
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-800 uppercase tracking-wider mb-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Rute Navigasi Aktif (OSRM)</span>
                    </div>
                    <h3 class="font-display font-bold text-sm text-[#16251D] truncate" x-text="routeTarget?.title || 'Tujuan Terpilih'"></h3>
                </div>

                <button @click="clearRoute()"
                        class="w-7 h-7 rounded-xl bg-gray-100 hover:bg-gray-200 text-[#4E6157] hover:text-[#16251D] flex items-center justify-center shrink-0 transition"
                        title="Tutup Rute">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Route Metrics & Mode Selector -->
            <div class="flex items-center justify-between bg-emerald-50/60 rounded-2xl p-2.5 border border-emerald-100">
                <div class="flex items-baseline gap-1.5">
                    <span class="font-display font-black text-2xl text-[#14573A]" x-text="routeInfo?.durationMin"></span>
                    <span class="text-xs font-bold text-[#4E6157]">menit</span>
                    <span class="text-xs text-gray-400 mx-1">•</span>
                    <span class="font-bold text-xs text-[#1E7A4C]" x-text="`${routeInfo?.distanceKm} km`"></span>
                </div>

                <!-- Mode pills: Driving vs Walking -->
                <div class="inline-flex rounded-xl bg-white p-1 border border-emerald-200/70 shadow-2xs">
                    <button @click="switchRouteMode('driving')"
                            :class="routeMode === 'driving' ? 'bg-[#1E7A4C] text-white shadow-2xs' : 'text-[#4E6157] hover:text-[#16251D]'"
                            class="px-2.5 py-1 text-xs font-bold rounded-lg transition flex items-center gap-1"
                            title="Moda Kendaraan (Mobil/Motor)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-9 4h10M5 16h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2zm2 0v2m10-2v2"/>
                        </svg>
                        <span>Kendaraan</span>
                    </button>
                    <button @click="switchRouteMode('walking')"
                            :class="routeMode === 'walking' ? 'bg-[#2A6F97] text-white shadow-2xs' : 'text-[#4E6157] hover:text-[#16251D]'"
                            class="px-2.5 py-1 text-xs font-bold rounded-lg transition flex items-center gap-1"
                            title="Moda Jalan Kaki">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a2 2 0 11-4 0 2 2 0 014 0zM9 14l2-4 3 2 1 4m-4-2v6m-2 0h4"/>
                        </svg>
                        <span>Jalan Kaki</span>
                    </button>
                </div>
            </div>

            <!-- Route Actions Bar -->
            <div class="flex items-center gap-2 pt-1">
                <a :href="'https://www.google.com/maps/dir/?api=1&origin=' + userLat + ',' + userLng + '&destination=' + (routeTarget?.lat || '') + ',' + (routeTarget?.lng || '') + '&travelmode=' + (routeMode === 'walking' ? 'walking' : 'driving')"
                   target="_blank"
                   class="flex-1 btn-primary py-2 text-xs flex items-center justify-center gap-1.5 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Buka Suara di Google Maps</span>
                </a>
                <button @click="openPlaceDetail(routeTarget)"
                        class="px-3 py-2 rounded-xl text-xs font-bold text-[#16251D] bg-gray-100 hover:bg-gray-200 transition">
                    Lihat Alamat
                </button>
            </div>
        </div>

        <!-- Rich Place Detail Modal / Sheet (Google Maps & Apple Maps Class UX) -->
        <div x-show="activePlace"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             class="fixed sm:absolute inset-x-0 bottom-0 sm:bottom-4 sm:left-4 sm:inset-x-auto sm:w-[410px] max-h-[78vh] sm:max-h-[calc(100%-7.5rem)] z-[1000] bg-white/98 backdrop-blur-2xl rounded-t-3xl sm:rounded-3xl shadow-[0_25px_60px_-12px_rgba(22,37,29,0.35),0_6px_20px_rgba(22,37,29,0.12)] border border-[#D5E0D9] overflow-hidden flex flex-col pointer-events-auto">
            
            <!-- Mobile pull handle -->
            <div class="sm:hidden pt-2 pb-1 flex justify-center">
                <div class="w-10 h-1 rounded-full bg-gray-300"></div>
            </div>

            <!-- Header with Badges & Close -->
            <div class="p-4 sm:p-5 border-b border-gray-100 flex items-start justify-between gap-3 bg-linear-to-b from-gray-50/80 to-transparent">
                <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                    <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-emerald-50 text-[#14573A] border border-emerald-200/80" x-text="activePlace?.category_name || 'Makanan'"></span>
                    <template x-if="activePlace?.halal_claim">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/80">Halal</span>
                    </template>
                    <template x-if="activePlace?.is_verified">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-[#2A6F97] border border-blue-200/80 flex items-center gap-1">
                            <svg class="w-3 h-3 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Terverifikasi
                        </span>
                    </template>
                    <template x-if="activePlace?.remaining_minutes">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span x-text="`Sisa ${getTimeShort(activePlace?.remaining_minutes)}`"></span>
                        </span>
                    </template>
                </div>

                <button @click="closePlaceDetail()"
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-[#4E6157] hover:text-[#16251D] flex items-center justify-center shrink-0 transition"
                        title="Tutup Panel Tempat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content Body -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                <!-- Title & Provider -->
                <div>
                    <h2 class="font-display font-black text-lg text-[#16251D] leading-snug" x-text="activePlace?.title"></h2>
                    <div class="flex items-center gap-2 mt-1.5 text-xs text-[#4E6157]">
                        <div class="w-5 h-5 rounded-full bg-[#1E7A4C]/10 text-[#1E7A4C] flex items-center justify-center shrink-0 font-bold text-[10px]" x-text="(activePlace?.user_name || 'U').substring(0, 1).toUpperCase()"></div>
                        <span class="font-medium text-[#16251D]" x-text="activePlace?.user_name"></span>
                        <span class="text-gray-300">•</span>
                        <span x-text="`${activePlace?.portions_left} porsi tersedia`"></span>
                    </div>
                </div>

                <!-- Full Address Card with Copy Action -->
                <div class="bg-gray-50/90 rounded-2xl p-3.5 border border-gray-100 space-y-2">
                    <div class="flex items-center justify-between text-xs text-[#4E6157]">
                        <span class="font-bold flex items-center gap-1.5 text-[#16251D]">
                            <svg class="w-4 h-4 text-[#1E7A4C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Alamat Pengambilan
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-white border border-gray-200 text-[#4E6157]"
                              x-text="activePlace?.show_exact ? 'Titik Presisi' : 'Area Kecamatan'"></span>
                    </div>
                    <p class="text-xs text-[#16251D] font-medium leading-relaxed select-all" x-text="activePlace?.pickup_address"></p>
                    
                    <button type="button"
                            @click="copyAddress(activePlace?.pickup_address)"
                            class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1E7A4C] hover:text-[#14573A] transition pt-1">
                        <template x-if="!copiedAddress">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </template>
                        <template x-if="copiedAddress">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span x-text="copiedAddress ? 'Alamat Berhasil Disalin!' : 'Salin Alamat Lengkap'"></span>
                    </button>
                </div>

                <!-- Description (if any) -->
                <template x-if="activePlace?.description">
                    <div class="text-xs text-[#4E6157] bg-white rounded-xl p-3 border border-gray-100">
                        <span class="font-bold text-[#16251D] block mb-1">Catatan Makanan:</span>
                        <p class="leading-relaxed" x-text="activePlace?.description"></p>
                    </div>
                </template>

                <!-- Grid Feature Action Buttons (Google Maps Class) -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <!-- 1. Road Routing Polyline in Leaflet OSM -->
                    <button type="button"
                            @click="drawRoute(activePlace, 'driving')"
                            class="p-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        <span>Rute di Peta</span>
                    </button>

                    <!-- 2. Share Feature (Web Share API & Copy) -->
                    <button type="button"
                            @click="sharePlace(activePlace)"
                            class="p-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-[#16251D] font-bold text-xs flex items-center justify-center gap-1.5 transition active:scale-95">
                        <svg class="w-4 h-4 text-[#1E7A4C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        <span>Bagikan</span>
                    </button>

                    <!-- 3. External Google Maps Navigation -->
                    <a :href="'https://www.google.com/maps/dir/?api=1&origin=' + userLat + ',' + userLng + '&destination=' + activePlace?.lat + ',' + activePlace?.lng"
                       target="_blank"
                       class="p-2.5 rounded-2xl bg-blue-50 hover:bg-blue-100 text-[#2A6F97] font-bold text-xs flex items-center justify-center gap-1.5 border border-blue-200/80 transition active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>Google Maps</span>
                    </a>

                    <!-- 4. Direct Claim / Detail Link -->
                    <a :href="activePlace?.detail_url"
                       class="p-2.5 rounded-2xl bg-[#16251D] hover:bg-black text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition active:scale-95">
                        <span>Ambil Makanan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Searched Place Result Sheet (Google Maps Destination Card) -->
        <div x-show="searchedPlace"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             class="fixed sm:absolute inset-x-0 bottom-0 sm:bottom-4 sm:left-4 sm:inset-x-auto sm:w-[410px] max-h-[78vh] sm:max-h-[calc(100%-7.5rem)] z-[1000] bg-white/98 backdrop-blur-2xl rounded-t-3xl sm:rounded-3xl shadow-[0_25px_60px_-12px_rgba(22,37,29,0.35),0_6px_20px_rgba(22,37,29,0.12)] border border-[#D5E0D9] overflow-hidden flex flex-col pointer-events-auto">
            
            <!-- Mobile pull handle -->
            <div class="sm:hidden pt-2 pb-1 flex justify-center">
                <div class="w-10 h-1 rounded-full bg-gray-300"></div>
            </div>

            <!-- Header with Badges & Close -->
            <div class="p-4 sm:p-5 border-b border-gray-100 flex items-start justify-between gap-3 bg-linear-to-b from-gray-50/80 to-transparent">
                <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                    <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-emerald-100 text-[#14573A] border border-emerald-200" x-text="searchedPlace?.type_label || 'Titik Tujuan'"></span>
                    <template x-if="searchedPlace?.distance_km">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-[#2A6F97] border border-blue-200 flex items-center gap-1">
                            <svg class="w-3 h-3 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path d="M12 7v5l3 3"/></svg>
                            <span x-text="`~${searchedPlace?.distance_km} km dari Anda`"></span>
                        </span>
                    </template>
                </div>

                <button @click="closeSearchedPlace()"
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-[#4E6157] hover:text-[#16251D] flex items-center justify-center shrink-0 transition"
                        title="Tutup Panel Tempat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content Body -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                <div>
                    <h2 class="font-display font-black text-lg text-[#16251D] leading-snug" x-text="searchedPlace?.title"></h2>
                    <p class="text-xs text-[#4E6157] mt-1 leading-relaxed" x-text="searchedPlace?.subtitle"></p>
                </div>

                <!-- Google Maps Actions Grid -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <!-- 1. Road Route to this destination -->
                    <button type="button"
                            @click="drawRoute(searchedPlace, 'driving'); closeSearchedPlace()"
                            class="p-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        <span>Rute Jalan (OSRM)</span>
                    </button>

                    <!-- 2. Explore Food Rescue in this neighborhood -->
                    <button type="button"
                            @click="setCenterLocation(searchedPlace.lat, searchedPlace.lng); closeSearchedPlace()"
                            class="p-2.5 rounded-2xl bg-[#16251D] hover:bg-black text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Cari Makanan di Sini</span>
                    </button>

                    <!-- 3. External Google Maps turn-by-turn -->
                    <a :href="'https://www.google.com/maps/dir/?api=1&origin=' + userLat + ',' + userLng + '&destination=' + searchedPlace?.lat + ',' + searchedPlace?.lng"
                       target="_blank"
                       class="p-2.5 rounded-2xl bg-blue-50 hover:bg-blue-100 text-[#2A6F97] font-bold text-xs flex items-center justify-center gap-1.5 border border-blue-200/80 transition active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>Google Maps</span>
                    </a>

                    <!-- 4. Share Destination -->
                    <button type="button"
                            @click="sharePlace({ title: searchedPlace?.title, id: 'search', lat: searchedPlace?.lat, lng: searchedPlace?.lng, pickup_address: searchedPlace?.subtitle, portions_left: 'Titik Lokasi' })"
                            class="p-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-[#16251D] font-bold text-xs flex items-center justify-center gap-1.5 transition active:scale-95">
                        <svg class="w-4 h-4 text-[#1E7A4C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        <span>Bagikan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating Re-open Sidebar Button (When sidebar is collapsed on desktop) -->
        <button x-show="!desktopSidebarOpen && !activePlace && !searchedPlace"
                @click="toggleDesktopSidebar(true)"
                class="hidden lg:flex items-center gap-2 absolute top-4 left-4 z-20 bg-white/95 backdrop-blur-md px-3.5 h-9 rounded-full shadow-[0_4px_16px_rgba(22,37,29,0.12)] border border-[#D5E0D9] text-xs font-bold text-[#16251D] hover:border-[#1E7A4C] hover:text-[#1E7A4C] hover:bg-emerald-50/50 transition active:scale-95 cursor-pointer">
            <svg class="w-4 h-4 text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            <span>Buka Panel (<span x-text="filteredItems.length"></span>)</span>
        </button>

        <!-- Floating Focus Banner (from Catalog hand-off - SDD FR-46) -->
        <template x-if="focusId && items.some(i => i.id == focusId)">
            <div class="absolute top-4 left-4 right-4 sm:left-auto sm:right-4 sm:max-w-md z-20 pointer-events-auto bg-white/95 backdrop-blur-md p-3.5 rounded-2xl shadow-card border border-[#1E7A4C]/50 flex items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-[#1E7A4C] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-[#16251D] flex items-center gap-1.5">
                            <span>Titik Penjemputan Makanan Aktif</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-[#14573A]">Hand-off Peta</span>
                        </p>
                        <p class="text-[10px] text-[#4E6157] truncate font-medium" x-text="items.find(i => i.id == focusId)?.title"></p>
                    </div>
                </div>
                <button @click="focusId = null" class="text-[#4E6157] hover:text-[#16251D] p-1 rounded-lg shrink-0" title="Tutup pemberitahuan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>

        <!-- Floating Location Tag (Bottom-Left) -->
        <div x-show="!activePlace && !searchedPlace"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute bottom-4 left-4 z-10 pointer-events-auto bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-card border border-[#D5E0D9] flex items-center gap-2 text-xs text-[#16251D]">
            <span class="w-2.5 h-2.5 rounded-full bg-[#1E7A4C] animate-ping"></span>
            <span class="font-bold text-[11px]" x-text="currentLocationLabel"></span>
            <span class="text-[10px] text-[#4E6157] font-medium" x-text="`• Radius ${radius}km`"></span>
        </div>

        <!-- Floating Map Control Dock (Bottom-Right - Google Maps Style) -->
        <div class="absolute bottom-20 right-4 z-10 pointer-events-auto flex flex-col items-center gap-2">
            <!-- Google Maps Vertical Grouped Zoom Dock (+ / -) -->
            <div class="flex flex-col rounded-2xl bg-white/95 backdrop-blur-md border border-[#D5E0D9] shadow-card overflow-hidden">
                <button type="button"
                        @click="map.zoomIn()"
                        class="w-10 h-10 flex items-center justify-center text-[#16251D] hover:bg-gray-50 active:bg-gray-100 transition border-b border-gray-100"
                        title="Perbesar peta">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <button type="button"
                        @click="map.zoomOut()"
                        class="w-10 h-10 flex items-center justify-center text-[#16251D] hover:bg-gray-50 active:bg-gray-100 transition"
                        title="Perkecil peta">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                    </svg>
                </button>
            </div>

            <!-- Basemap Switcher (Satellite vs Street) -->
            <button type="button"
                    @click="toggleBasemap()"
                    class="w-10 h-10 rounded-2xl bg-white/95 backdrop-blur-md hover:bg-white text-[#16251D] border border-[#D5E0D9] shadow-card flex items-center justify-center transition active:scale-95 group"
                    :title="basemap === 'satellite' ? 'Ganti ke Peta Jalan' : 'Ganti ke Citra Satelit'">
                <svg class="w-5 h-5 text-[#4E6157] group-hover:text-[#1E7A4C] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </button>

            <!-- GPS Recenter Button (Google Maps "My Location" Crosshair) -->
            <button type="button"
                    @click="useCurrentLocation()"
                    :disabled="locating"
                    class="w-10 h-10 rounded-2xl bg-white/95 backdrop-blur-md hover:bg-white text-[#16251D] hover:text-[#1E7A4C] border border-[#D5E0D9] shadow-card flex items-center justify-center transition active:scale-95 group cursor-pointer"
                    title="Pusatkan ke lokasi saya (GPS)">
                <svg :class="locating ? 'animate-spin' : 'group-hover:scale-110'" class="w-5 h-5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="7" stroke-width="2" />
                    <circle cx="12" cy="12" r="3" fill="currentColor" stroke="none" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                </svg>
            </button>

            <!-- Reset to Default Center Button -->
            <button type="button"
                    @click="resetToDefaultCenter()"
                    class="w-10 h-10 rounded-2xl bg-white/95 backdrop-blur-md hover:bg-white text-[#4E6157] hover:text-[#16251D] border border-[#D5E0D9] shadow-card flex items-center justify-center transition active:scale-95"
                    title="Kembali ke titik awal pencarian">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        <!-- Aesthetic Toast Notification -->
        <div x-show="toastMessage"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[1200] pointer-events-none bg-[#16251D]/95 text-white px-4 py-2.5 rounded-2xl shadow-2xl flex items-center gap-2.5 text-xs font-bold border border-white/10 backdrop-blur-md">
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="toastMessage"></span>
        </div>
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
        routeLayer: null,
        userMarker: null,
        userRadiusCircle: null,
        initialLat: {{ $initialLat }},
        initialLng: {{ $initialLng }},
        userLat: {{ $initialLat }},
        userLng: {{ $initialLng }},
        focusId: @json($focusId ? (int)$focusId : null),
        markersById: {},
        currentLocationLabel: 'Pusat Area',
        radius: 5,
        layer: 'food', // 'food' or 'campaigns'
        halalOnly: false,
        urgentOnly: false,
        selectedCategory: '',
        searchQuery: '',
        suggestions: [],
        showSuggestions: false,
        directionsMode: false,
        originLabel: 'Lokasi Anda saat ini (GPS)',
        searchedPlace: null,
        searchMarker: null,
        loading: false,
        locating: false,
        routingLoading: false,
        sheetExpanded: false,
        desktopSidebarOpen: true,
        copiedAddress: false,
        items: [],
        filteredItems: [],
        selectedId: null,
        activePlace: null,
        routeInfo: null,
        routeTarget: null,
        routeMode: 'driving', // 'driving' or 'walking'
        toastMessage: null,
        toastTimer: null,
        pollTimer: null,
        resizeObserver: null,

        init() {
            this.initMap();
            this.loadData();
            this.reverseGeocodeLabel(this.userLat, this.userLng);

            // Invalidate Leaflet size as flex layout settles to avoid grey tile boxes
            this.$nextTick(() => {
                if (this.map) this.map.invalidateSize();
                setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 250);
                setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 600);
            });

            // Window resize handler
            window.addEventListener('resize', () => {
                if (this.map) this.map.invalidateSize();
            });

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
            this.routeLayer = L.layerGroup().addTo(this.map);

            // Render Center User Pin & Radius Circle
            this.updateUserMarker();

            // Observe map container dimensions dynamically so tiles always fit 100% of the screen
            if (window.ResizeObserver) {
                this.resizeObserver = new ResizeObserver(() => {
                    if (this.map) {
                        this.map.invalidateSize();
                    }
                });
                const mapEl = document.getElementById('locabite-map');
                if (mapEl) this.resizeObserver.observe(mapEl);
            }

            // Click anywhere on map to set fixed search center (Google Maps style)
            this.map.on('click', (e) => {
                this.setCenterLocation(e.latlng.lat, e.latlng.lng);
            });
        },

        toggleDesktopSidebar(forcedState) {
            if (typeof forcedState === 'boolean') {
                this.desktopSidebarOpen = forcedState;
            } else {
                this.desktopSidebarOpen = !this.desktopSidebarOpen;
            }
            setTimeout(() => {
                if (this.map) this.map.invalidateSize();
            }, 320);
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
            if (this.map) this.map.invalidateSize();
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
            this.markersById = {};

            listings.forEach(l => {
                const color = this.getTimeColor(l.remaining_minutes);
                const shortTime = this.getTimeShort(l.remaining_minutes);

                const markerHtml = `
                    <div style="display:flex; flex-direction:column; align-items:center; cursor:pointer; filter:drop-shadow(0 3px 6px rgba(0,0,0,0.22)); transition:transform 0.15s ease;">
                        <div style="width:30px; height:30px; border-radius:50% 50% 50% 0; transform:rotate(-45deg); background:${color}; border:2.5px solid white; display:grid; place-items:center;">
                            <span style="transform:rotate(45deg); color:white; font-size:11px; font-weight:800; font-family:'Atkinson Hyperlegible',sans-serif;">${l.portions_left}</span>
                        </div>
                        <div style="background:#16251D; color:white; font-size:9.5px; font-weight:700; padding:1px 6px; border-radius:999px; margin-top:2px; letter-spacing:0.02em; border:1px solid rgba(255,255,255,0.25);">
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

                // Popup with rich direct actions
                const popupContent = `
                    <div style="font-family:'Atkinson Hyperlegible',sans-serif; min-width:210px; padding:2px;">
                        <div style="font-size:10px; font-weight:bold; color:#1E7A4C; text-transform:uppercase; margin-bottom:2px;">${l.category_name || 'Makanan'}</div>
                        <h4 style="font-weight:bold; font-size:13px; margin:0 0 4px 0; color:#16251D; line-height:1.3;">${l.title}</h4>
                        <div style="font-size:11px; color:#4E6157; margin-bottom:6px;">
                            ${l.portions_left} porsi • ${l.distance_km} km
                        </div>
                        <div style="font-size:11px; font-weight:bold; color:${color}; margin-bottom:8px;">
                            Sisa waktu: ${shortTime}
                        </div>
                        <div style="display:flex; gap:4px; align-items:center; flex-wrap:wrap;">
                            <button type="button" onclick="window.__locabiteDrawRoute(${l.id})" style="background:#1E7A4C; color:white; border:none; padding:4px 8px; border-radius:8px; font-size:10px; font-weight:bold; cursor:pointer;">
                                Rute Peta
                            </button>
                            <button type="button" onclick="window.__locabiteOpenDetail(${l.id})" style="background:#2A6F97; color:white; border:none; padding:4px 8px; border-radius:8px; font-size:10px; font-weight:bold; cursor:pointer;">
                                Alamat & Info
                            </button>
                            <a href="${l.detail_url}" style="background:#16251D; color:white; padding:4px 8px; border-radius:8px; font-size:10px; text-decoration:none; font-weight:bold;">
                                Ambil
                            </a>
                        </div>
                    </div>
                `;
                m.bindPopup(popupContent);

                m.on('click', () => {
                    this.selectedId = l.id;
                    this.openPlaceDetail(l);
                });

                this.markersById[l.id] = m;
                this.markersLayer.addLayer(m);
            });

            // Global bridge for popup buttons
            window.__locabiteDrawRoute = (id) => {
                const item = this.items.find(x => x.id === id);
                if (item) this.drawRoute(item, 'driving');
            };
            window.__locabiteOpenDetail = (id) => {
                const item = this.items.find(x => x.id === id);
                if (item) this.openPlaceDetail(item);
            };

            // Hand-off focus trigger from catalog
            if (this.focusId) {
                const target = listings.find(l => l.id == this.focusId);
                if (target) {
                    setTimeout(() => {
                        this.openPlaceDetail(target);
                    }, 350);
                }
            }
        },

        renderCampaignMarkers(campaigns) {
            this.markersLayer.clearLayers();

            campaigns.forEach(c => {
                const markerHtml = `
                    <div style="display:flex; flex-direction:column; align-items:center; cursor:pointer;">
                        <div style="width:30px; height:30px; border-radius:8px; background:#2A6F97; border:2px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3); display:grid; place-items:center; color:white;">
                            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
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
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${c.lat},${c.lng}" target="_blank" style="display:inline-flex; align-items:center; gap:4px; background:#16251D; color:white; padding:4px 10px; border-radius:8px; font-size:11px; text-decoration:none; font-weight:bold;">
                                <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                Rute
                            </a>
                        </div>
                    </div>
                `);

                this.markersLayer.addLayer(m);
            });
        },

        focusMarker(item) {
            this.selectedId = item.id;
            this.map.flyTo([item.lat, item.lng], 16, { animate: true, duration: 1.2 });
            if (this.markersById && this.markersById[item.id]) {
                this.markersById[item.id].openPopup();
            }
            const card = document.getElementById(`card-${item.id}`);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        },

        openPlaceDetail(item) {
            if (!item) return;
            this.routeInfo = null;
            this.searchedPlace = null;
            this.activePlace = item;
            this.selectedId = item.id;
            this.focusMarker(item);
        },

        closePlaceDetail() {
            this.activePlace = null;
        },

        async drawRoute(item, mode = 'driving') {
            if (!item || !item.lat || !item.lng) return;

            this.activePlace = null;
            this.searchedPlace = null;
            this.routeMode = mode;
            this.routeTarget = item;
            this.routingLoading = true;

            const startLng = Number(this.userLng);
            const startLat = Number(this.userLat);
            const endLng = Number(item.lng);
            const endLat = Number(item.lat);

            if (this.routeLayer) {
                this.routeLayer.clearLayers();
            }

            try {
                // Query OpenStreetMap OSRM routing engine API
                const osrmProfile = mode === 'walking' ? 'walking' : 'driving';
                const osrmUrl = `https://router.project-osrm.org/route/v1/${osrmProfile}/${startLng},${startLat};${endLng},${endLat}?overview=full&geometries=geojson`;

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 6000);

                const res = await fetch(osrmUrl, { signal: controller.signal });
                clearTimeout(timeoutId);

                if (res.ok) {
                    const data = await res.json();
                    if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                        const r = data.routes[0];
                        // OSRM coordinates are [lng, lat], convert to Leaflet [lat, lng]
                        const coords = r.geometry.coordinates.map(pt => [pt[1], pt[0]]);

                        const distanceKm = (r.distance / 1000).toFixed(1);
                        const durationMin = Math.max(1, Math.round(r.duration / 60));

                        this.routeInfo = {
                            distanceKm: distanceKm,
                            durationMin: durationMin,
                            mode: mode
                        };

                        // Draw outer casing shadow line for high contrast
                        const casing = L.polyline(coords, {
                            color: '#0F3B24',
                            weight: 8,
                            opacity: 0.35,
                            lineCap: 'round',
                            lineJoin: 'round'
                        });

                        // Draw inner road line
                        const roadLine = L.polyline(coords, {
                            color: mode === 'walking' ? '#2A6F97' : '#1E7A4C',
                            weight: 5,
                            opacity: 0.95,
                            lineCap: 'round',
                            lineJoin: 'round',
                            dashArray: mode === 'walking' ? '8, 8' : null
                        });

                        this.routeLayer.addLayer(casing);
                        this.routeLayer.addLayer(roadLine);

                        // Fit bounds to show entire route with padding
                        this.map.fitBounds(roadLine.getBounds(), {
                            padding: [80, 80],
                            maxZoom: 16
                        });

                        this.showToast(`Rute jalan ${mode === 'walking' ? 'jalan kaki' : 'berkendara'} (${distanceKm} km, ~${durationMin} mnt) siap!`);
                        return;
                    }
                }
                throw new Error("OSRM server returned empty routes");
            } catch (err) {
                // Graceful fallback: draw direct connection line with calculated estimation
                console.warn("OSRM routing offline or timed out, using geodetic interpolation fallback:", err);
                const coords = [[startLat, startLng], [endLat, endLng]];

                // Haversine estimation
                const distKm = Number(item.distance_km) || 2.5;
                const speed = mode === 'walking' ? 4.5 : 30; // km/h
                const durationMin = Math.max(1, Math.round((distKm / speed) * 60));

                this.routeInfo = {
                    distanceKm: distKm.toFixed(1),
                    durationMin: durationMin,
                    mode: mode
                };

                const fallbackLine = L.polyline(coords, {
                    color: mode === 'walking' ? '#2A6F97' : '#1E7A4C',
                    weight: 4,
                    dashArray: '6, 8',
                    opacity: 0.85
                });
                this.routeLayer.addLayer(fallbackLine);
                this.map.fitBounds(fallbackLine.getBounds(), { padding: [80, 80] });
                this.showToast(`Rute garis langsung (~${distKm.toFixed(1)} km) ditampilkan.`);
            } finally {
                this.routingLoading = false;
            }
        },

        switchRouteMode(newMode) {
            if (this.routeTarget) {
                this.drawRoute(this.routeTarget, newMode);
            }
        },

        clearRoute() {
            if (this.routeLayer) {
                this.routeLayer.clearLayers();
            }
            if (this.searchMarker && !this.searchedPlace) {
                this.map.removeLayer(this.searchMarker);
                this.searchMarker = null;
            }
            this.routeInfo = null;
            this.routeTarget = null;
            this.showToast("Rute jalan ditutup.");
        },

        toggleDirectionsMode() {
            this.directionsMode = !this.directionsMode;
            if (this.directionsMode) {
                this.originLabel = 'Lokasi Anda saat ini (GPS)';
                this.showSuggestions = false;
            } else {
                this.clearRoute();
            }
        },

        swapDirections() {
            if (this.searchedPlace || this.routeTarget) {
                const target = this.searchedPlace || this.routeTarget;
                const tempLat = this.userLat;
                const tempLng = this.userLng;
                const tempLabel = this.originLabel;

                this.userLat = target.lat;
                this.userLng = target.lng;
                this.originLabel = target.title;

                target.lat = tempLat;
                target.lng = tempLng;
                target.title = tempLabel;

                this.drawRoute(target, this.routeMode);
                this.showToast("Titik awal dan tujuan ditukar.");
            }
        },

        getDistanceKm(lat, lng) {
            if (!lat || !lng || !this.userLat || !this.userLng) return null;
            const R = 6371; // km
            const dLat = (lat - this.userLat) * Math.PI / 180;
            const dLon = (lng - this.userLng) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(this.userLat * Math.PI / 180) * Math.cos(lat * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const dist = R * c;
            return dist < 10 ? dist.toFixed(1) : Math.round(dist);
        },

        setSearchMarker(lat, lng, title) {
            if (this.searchMarker) {
                this.map.removeLayer(this.searchMarker);
            }
            const searchIcon = L.divIcon({
                className: 'search-destination-pin',
                html: `
                    <div style="display:flex; flex-direction:column; align-items:center; filter:drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                        <div style="width:32px; height:32px; border-radius:50% 50% 50% 0; transform:rotate(-45deg); background:#C93A2E; border:3px solid white; display:grid; place-items:center;">
                            <div style="width:10px; height:10px; border-radius:50%; background:white; transform:rotate(45deg);"></div>
                        </div>
                        <div style="width:8px; height:8px; border-radius:50%; background:rgba(0,0,0,0.3); margin-top:2px;"></div>
                    </div>
                `,
                iconSize: [32, 44],
                iconAnchor: [16, 40]
            });

            this.searchMarker = L.marker([lat, lng], { icon: searchIcon }).addTo(this.map);
            if (title) {
                this.searchMarker.bindTooltip(title, { direction: 'top', offset: [0, -35] });
            }
        },

        routeToSuggestion(s) {
            this.searchQuery = s.title;
            this.showSuggestions = false;
            this.closePlaceDetail();
            this.closeSearchedPlace();
            this.setSearchMarker(s.lat, s.lng, s.title);
            this.drawRoute({
                title: s.title,
                lat: s.lat,
                lng: s.lng,
                pickup_address: s.subtitle || s.display_name,
                distance_km: this.getDistanceKm(s.lat, s.lng) || 1
            }, this.routeMode || 'driving');
        },

        openSearchedPlace(s) {
            this.closePlaceDetail();
            this.routeInfo = null;
            this.searchedPlace = {
                title: s.title,
                subtitle: s.subtitle || s.display_name,
                lat: s.lat,
                lng: s.lng,
                type_label: s.type_label || 'Tujuan',
                distance_km: this.getDistanceKm(s.lat, s.lng)
            };
        },

        closeSearchedPlace() {
            this.searchedPlace = null;
            if (this.searchMarker && !this.routeInfo) {
                this.map.removeLayer(this.searchMarker);
                this.searchMarker = null;
            }
        },

        async sharePlace(item) {
            if (!item) return;
            const shareUrl = `${window.location.origin}/?focus=${item.id}&lat=${item.lat}&lng=${item.lng}`;
            const shareData = {
                title: `${item.title} — Locabite`,
                text: `Ambil makanan berlebih "${item.title}" (${item.portions_left} porsi) di ${item.pickup_address}. Mari selamatkan pangan!`,
                url: shareUrl
            };

            if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
                try {
                    await navigator.share(shareData);
                    this.showToast("Berhasil membagikan makanan!");
                    return;
                } catch (e) {
                    if (e.name === 'AbortError') return; // User closed share dialog
                }
            }

            // Clipboard fallback
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(shareUrl);
                this.showToast("Tautan lokasi berhasil disalin ke papan klip!");
            } else {
                prompt("Salin tautan makanan berikut:", shareUrl);
            }
        },

        cycleRadius() {
            const steps = [1, 3, 5, 10];
            const nextIdx = (steps.indexOf(this.radius) + 1) % steps.length;
            this.radius = steps[nextIdx];
            this.loadData();
        },

        async copyAddress(address) {
            if (!address) return;
            this.copiedAddress = true;
            setTimeout(() => { this.copiedAddress = false; }, 2500);

            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(address);
                this.showToast("Alamat lengkap berhasil disalin ke papan klip!");
            } else {
                prompt("Salin alamat berikut:", address);
            }
        },

        showToast(msg) {
            this.toastMessage = msg;
            if (this.toastTimer) clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => {
                this.toastMessage = null;
            }, 3200);
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
            this.setSearchMarker(s.lat, s.lng, s.title);
            this.setCenterLocation(s.lat, s.lng);
            this.map.flyTo([s.lat, s.lng], 15);
            this.openSearchedPlace(s);
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
            const mins = Math.max(0, Math.round(Number(remainingMins) || 0));
            if (mins <= 0) return '#4E6157';
            if (mins < 60) return '#C93A2E'; // Cabai / Kritis (<1h)
            if (mins <= 180) return '#B45309'; // Kunyit Tua / Mendesak (1-3h)
            return '#1E7A4C'; // Hijau Segar (>3h)
        },

        getTimeShort(remainingMins) {
            const mins = Math.max(0, Math.round(Number(remainingMins) || 0));
            if (mins <= 0) return 'Habis';
            const h = Math.floor(mins / 60);
            const m = mins % 60;
            if (h >= 1) {
                return m > 0 ? `${h}j ${m}m` : `${h}j`;
            }
            return `${m}m`;
        }
    }));
});
</script>
@endpush

</x-layouts.app>
