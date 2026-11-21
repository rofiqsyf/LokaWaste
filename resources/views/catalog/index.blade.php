<x-layouts.app :title="'Katalog Makanan Penyelamat — Locabite'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Top Breadcrumb & E-Commerce Hero Header -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#D5E0D9] shadow-card relative overflow-hidden">
            <div class="relative z-10 space-y-4">
                <nav class="flex items-center gap-2 text-xs text-[#4E6157]">
                    <a href="{{ route('home') }}" class="hover:text-[#1E7A4C] transition">Peta</a>
                    <span>/</span>
                    <span class="text-[#16251D] font-bold">Katalog Makanan</span>
                </nav>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D] tracking-tight">
                            Katalog Surplus Pangan
                        </h1>
                        <p class="text-xs sm:text-sm text-[#4E6157] mt-1 max-w-2xl leading-relaxed">
                            Jelajahi makanan layak konsumsi dari toko roti, resto, katering, dan warga sekitar. Pengambilan dilakukan langsung di titik lokasi via <strong>Peta Interaktif</strong>.
                        </p>
                    </div>

                    <!-- Impact & Stock Live Counter Badge -->
                    <div class="flex items-center gap-3 shrink-0 bg-[#F2F6F3] p-3 rounded-2xl border border-[#D5E0D9]">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#1E7A4C] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-display font-bold text-lg text-[#16251D]">{{ $totalPortionsAvailable }}</span>
                                <span class="text-xs text-[#4E6157]">porsi aktif</span>
                            </div>
                            <span class="text-[11px] text-[#1E7A4C] font-semibold block">dari {{ $totalActiveListings }} listing siap ambil</span>
                        </div>
                    </div>
                </div>

                <!-- Instant Search Bar & Location Quick Detect -->
                <form method="GET" action="{{ route('catalog.index') }}" class="pt-2">
                    <div class="flex flex-col sm:flex-row items-center gap-2.5">
                        <div class="relative flex-1 w-full">
                            <input type="text"
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Cari nama makanan, toko roti, resto, atau kecamatan (misal: Roti Sisir, Tembalang)..."
                                   class="w-full pl-10 pr-4 py-3 rounded-2xl border border-[#D5E0D9] text-xs sm:text-sm bg-white focus:ring-2 focus:ring-[#1E7A4C] focus:border-transparent transition shadow-xs">
                            <span class="absolute left-3.5 top-3.5 text-[#4E6157]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                        </div>

                        <!-- Retain active filters in hidden inputs -->
                        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                        @if(request('halal')) <input type="hidden" name="halal" value="1"> @endif
                        @if(request('vegetarian')) <input type="hidden" name="vegetarian" value="1"> @endif
                        @if(request('food_type')) <input type="hidden" name="food_type" value="{{ request('food_type') }}"> @endif
                        @if(request('urgency')) <input type="hidden" name="urgency" value="{{ request('urgency') }}"> @endif
                        @if(request('district_id')) <input type="hidden" name="district_id" value="{{ request('district_id') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit" class="btn-primary w-full sm:w-auto py-3 px-5 text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari Makanan
                            </button>

                            <a href="{{ route('home') }}" class="btn-secondary w-full sm:w-auto py-3 px-4 text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 shrink-0" title="Buka Mode Peta Interaktif">
                                <svg class="w-4 h-4 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Mode Peta
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Category Horizontal Scroller (E-Commerce Category Chips) -->
                <div class="pt-2 border-t border-[#D5E0D9]/80 flex items-center gap-2 overflow-x-auto scrollbar-none pb-1">
                    <a href="{{ route('catalog.index', array_merge(request()->except('category', 'page'))) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold shrink-0 transition flex items-center gap-1.5 {{ !request('category') ? 'bg-[#1E7A4C] text-white shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-[#4E6157]' }}">
                        <span>Semua Kategori</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ !request('category') ? 'bg-white/20 text-white' : 'bg-gray-200 text-[#16251D]' }}">
                            {{ $totalActiveListings }}
                        </span>
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('catalog.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold shrink-0 transition flex items-center gap-1.5 {{ request('category') === $cat->slug ? 'bg-[#1E7A4C] text-white shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-[#4E6157]' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ request('category') === $cat->slug ? 'bg-white/20 text-white' : 'bg-gray-200 text-[#16251D]' }}">
                                {{ $cat->listings_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Catalog Body Layout (Sidebar Filters + Products Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start" x-data="{ mobileFilterOpen: false }">

            <!-- Mobile Filter Toggle Button -->
            <div class="lg:hidden flex items-center justify-between bg-white p-4 rounded-2xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs font-bold text-[#16251D]">Filter & Pengurutan</span>
                <button type="button" @click="mobileFilterOpen = !mobileFilterOpen" class="btn-secondary py-1.5 px-3 text-xs flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span x-text="mobileFilterOpen ? 'Tutup Filter' : 'Buka Filter'"></span>
                </button>
            </div>

            <!-- Left Filter Sidebar -->
            <aside :class="mobileFilterOpen ? 'block' : 'hidden lg:block'"
                   class="bg-white rounded-3xl p-5 border border-[#D5E0D9] shadow-card space-y-6 sticky top-20">
                <div class="flex items-center justify-between pb-3 border-b border-[#D5E0D9]">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <h2 class="font-display font-bold text-sm text-[#16251D]">Filter Pilihan</h2>
                    </div>
                    @if(request()->hasAny(['q', 'category', 'halal', 'vegetarian', 'food_type', 'urgency', 'district_id']))
                        <a href="{{ route('catalog.index') }}" class="text-[11px] font-bold text-[#C93A2E] hover:underline">
                            Reset Semua
                        </a>
                    @endif
                </div>

                <form method="GET" action="{{ route('catalog.index') }}" class="space-y-5">
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    <!-- Syariah & Diet Filter -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#16251D] block">Kesesuaian Diet & Syariah</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2.5 text-xs text-[#4E6157] hover:text-[#16251D] cursor-pointer">
                                <input type="checkbox" name="halal" value="1" {{ request('halal') ? 'checked' : '' }}
                                       class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                                <span>Pernyataan Halal</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-xs text-[#4E6157] hover:text-[#16251D] cursor-pointer">
                                <input type="checkbox" name="vegetarian" value="1" {{ request('vegetarian') ? 'checked' : '' }}
                                       class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                                <span>Vegetarian</span>
                            </label>
                        </div>
                    </div>

                    <!-- Urgency Level Filter -->
                    <div class="space-y-2 pt-2 border-t border-[#D5E0D9]">
                        <label class="text-xs font-bold text-[#16251D] block">Tingkat Urgensi Waktu</label>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-xs text-[#4E6157] hover:text-[#16251D] cursor-pointer">
                                <input type="radio" name="urgency" value="" {{ !request('urgency') ? 'checked' : '' }}
                                       class="text-[#1E7A4C] focus:ring-[#1E7A4C]">
                                <span>Semua Batas Waktu</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-red-700 font-semibold cursor-pointer">
                                <input type="radio" name="urgency" value="critical" {{ request('urgency') === 'critical' ? 'checked' : '' }}
                                       class="text-red-600 focus:ring-red-600">
                                <span>Kritis (&lt; 1 Jam Tersisa)</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-amber-700 font-semibold cursor-pointer">
                                <input type="radio" name="urgency" value="urgent" {{ request('urgency') === 'urgent' ? 'checked' : '' }}
                                       class="text-amber-600 focus:ring-amber-600">
                                <span>Mendesak (&lt; 2 Jam Tersisa)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Food Type Filter -->
                    <div class="space-y-2 pt-2 border-t border-[#D5E0D9]">
                        <label class="text-xs font-bold text-[#16251D] block">Jenis Olahan Pangan</label>
                        <select name="food_type" class="w-full text-xs rounded-xl border border-[#D5E0D9] p-2 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#1E7A4C]">
                            <option value="">Semua Jenis Olahan</option>
                            <option value="cooked" {{ request('food_type') === 'cooked' ? 'selected' : '' }}>Siap Santap / Matang</option>
                            <option value="packaged" {{ request('food_type') === 'packaged' ? 'selected' : '' }}>Kemasan Pabrik / Toko</option>
                            <option value="raw" {{ request('food_type') === 'raw' ? 'selected' : '' }}>Bahan Mentah / Sayur</option>
                        </select>
                    </div>

                    <!-- District Filter -->
                    <div class="space-y-2 pt-2 border-t border-[#D5E0D9]">
                        <label class="text-xs font-bold text-[#16251D] block">Wilayah / Kecamatan</label>
                        <select name="district_id" class="w-full text-xs rounded-xl border border-[#D5E0D9] p-2 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#1E7A4C]">
                            <option value="">Semua Kecamatan</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ request('district_id') == $d->id ? 'selected' : '' }}>
                                    Kec. {{ $d->name }} ({{ $d->regency->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full btn-primary py-2.5 text-xs font-bold shadow-xs">
                        Terapkan Filter
                    </button>
                </form>

                <!-- Navigation Guide to Interactive Map -->
                <div class="p-3.5 bg-blue-50/70 rounded-2xl border border-blue-200/80 text-[11px] text-[#2A6F97] space-y-1.5">
                    <div class="flex items-center gap-1.5 font-bold">
                        <svg class="w-4 h-4 text-[#2A6F97] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Aturan Pengambilan:</span>
                    </div>
                    <p class="leading-relaxed">
                        Makanan tidak dikirim lewat kurir komersial. Ketuk tombol <strong>Ambil di Peta</strong> pada kartu makanan untuk membuka navigasi rute dan menjemput makanan langsung sebelum batas waktu berakhir.
                    </p>
                </div>
            </aside>

            <!-- Main Product Grid Area -->
            <div class="lg:col-span-3 space-y-5">

                <!-- Toolbar: Result Counter & Sort Dropdown -->
                <div class="bg-white rounded-2xl p-4 border border-[#D5E0D9] shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="text-xs text-[#4E6157]">
                        Menampilkan <strong class="text-[#16251D]">{{ $listings->total() }}</strong> makanan penyelamat
                        @if(request('q')) untuk pencarian "<em>{{ request('q') }}</em>" @endif
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <span class="text-xs text-[#4E6157] font-medium hidden sm:inline">Urutkan:</span>
                        <form method="GET" action="{{ route('catalog.index') }}" id="sort-form">
                            @foreach(request()->except('sort', 'page') as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <select name="sort" onchange="document.getElementById('sort-form').submit()"
                                    class="text-xs font-semibold rounded-xl border border-[#D5E0D9] py-1.5 pl-3 pr-8 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#1E7A4C] cursor-pointer">
                                <option value="expiring_soon" {{ $sort === 'expiring_soon' ? 'selected' : '' }}>Batas Waktu Terdekat (Prioritas Food Rescue)</option>
                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Terbaru Diposting</option>
                                <option value="portions_high" {{ $sort === 'portions_high' ? 'selected' : '' }}>Porsi Terbanyak</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Product Grid (1, 2, 3 cols) -->
                @if($listings->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-[#D5E0D9] shadow-card space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 text-[#4E6157] flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-base text-[#16251D]">Tidak Ada Makanan yang Cocok</h3>
                        <p class="text-xs text-[#4E6157] max-w-sm mx-auto">
                            Tidak ditemukan surplus makanan yang sesuai dengan kriteria filter Anda saat ini. Coba ubah kata kunci atau reset filter.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('catalog.index') }}" class="btn-primary text-xs py-2 px-4 inline-flex">
                                Reset Semua Filter
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($listings as $item)
                            @php
                                $remainingMins = max(0, now()->diffInMinutes($item->expires_at, false));
                                $hours = floor($remainingMins / 60);
                                $mins = $remainingMins % 60;
                                $timeShort = $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";
                                $isUrgent = $remainingMins < 60;
                            @endphp

                            <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card overflow-hidden hover:border-[#1E7A4C]/60 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                                <div>
                                    <!-- Card Media Box with Floating Badges -->
                                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                                        @if($item->photo_path)
                                            <img src="{{ asset('storage/' . $item->photo_path) }}"
                                                 alt="{{ $item->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <!-- SVG Fallback Illustration -->
                                            <div class="w-full h-full bg-linear-to-br from-emerald-50 to-[#F2F6F3] flex items-center justify-center text-[#1E7A4C]">
                                                <svg class="w-12 h-12 text-[#1E7A4C]/40 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Top Left Badges: Category & Stock -->
                                        <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
                                            <span class="px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-md text-[10px] font-bold text-[#16251D] shadow-xs">
                                                {{ $item->category->name }}
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full bg-[#1E7A4C] text-white text-[10px] font-bold shadow-xs flex items-center gap-1">
                                                <span>{{ $item->portions_left }} porsi tersisa</span>
                                            </span>
                                        </div>

                                        <!-- Top Right Badge: Time Urgency Pill -->
                                        <div class="absolute top-3 right-3">
                                            @if($isUrgent)
                                                <div class="px-2.5 py-1 rounded-full bg-red-600 text-white text-[10px] font-bold shadow-xs animate-pulse flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Sisa {{ $timeShort }}</span>
                                                </div>
                                            @else
                                                <div class="px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-md text-[#16251D] text-[10px] font-bold shadow-xs flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-[#1E7A4C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Sisa {{ $timeShort }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Content Body -->
                                    <div class="p-5 space-y-3">
                                        <!-- Provider Identity -->
                                        <div class="flex items-center justify-between text-xs text-[#4E6157]">
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <span class="truncate font-semibold text-[#16251D]">
                                                    {{ $item->user->organization_name ?? $item->user->name }}
                                                </span>
                                                @if($item->user->is_verified)
                                                    <span title="Penyedia Terverifikasi" class="text-emerald-700 shrink-0">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>

                                            <span class="text-[11px] shrink-0 font-medium">
                                                {{ $item->district->name }}
                                            </span>
                                        </div>

                                        <!-- Title -->
                                        <h3 class="font-display font-bold text-base text-[#16251D] group-hover:text-[#1E7A4C] transition-colors line-clamp-1">
                                            {{ $item->title }}
                                        </h3>

                                        <!-- Description snippet -->
                                        <p class="text-xs text-[#4E6157] line-clamp-2 leading-relaxed">
                                            {{ $item->description ?? 'Surplus pangan berkualitas, siap diambil langsung di lokasi penyedia.' }}
                                        </p>

                                        <!-- Diet & Syariah Chips -->
                                        <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                            @if($item->halal_claim)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    Halal
                                                </span>
                                            @endif
                                            @if($item->is_vegetarian)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-green-50 text-green-800 border border-green-200">
                                                    Vegetarian
                                                </span>
                                            @endif
                                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-gray-100 text-[#4E6157]">
                                                {{ match($item->food_type) { 'cooked' => 'Siap Santap', 'packaged' => 'Kemasan', 'raw' => 'Bahan Mentah', default => 'Makanan' } }}
                                            </span>
                                            @if(isset($item->distance_km))
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-[#2A6F97]">
                                                    {{ $item->distance_km }} km
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Action Footer: E-Commerce to Map Hand-Off (SDD FR-46) -->
                                <div class="p-5 pt-0 border-t border-[#D5E0D9]/60 flex items-center gap-2 mt-2">
                                    <!-- Primary Action: Hand-off to Interactive Map -->
                                    <a href="{{ route('home', ['focus' => $item->id, 'lat' => $item->public_latitude, 'lng' => $item->public_longitude]) }}"
                                       class="flex-1 btn-primary py-2.5 px-3 text-xs font-bold flex items-center justify-center gap-1.5 active:scale-[0.98] transition shadow-xs"
                                       title="Buka titik penjemputan makanan ini di Peta Interaktif">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>Ambil di Peta</span>
                                    </a>

                                    <!-- Secondary Action: Detail Specs -->
                                    <a href="{{ route('listings.show', $item->id) }}"
                                       class="btn-secondary py-2.5 px-3 text-xs font-semibold shrink-0"
                                       title="Lihat Keterangan Lengkap & Keamanan Pangan">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pt-4">
                        {{ $listings->links() }}
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-layouts.app>
