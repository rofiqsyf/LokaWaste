<x-layouts.app title="Bagikan Makanan — Locabite">

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6" x-data="createListingForm()">

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('home') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] inline-flex items-center gap-1 mb-2">
            &larr; Batal & Kembali
        </a>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Bagikan Makanan Berlebih</h1>
        <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Cegah makanan terbuang dengan menyalurkannya ke warga terdekat dalam hitungan menit.</p>
    </div>

    <!-- Main Form -->
    <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-6 sm:p-8 space-y-6">
        @csrf

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-[#C93A2E] rounded-2xl text-xs text-[#C93A2E] space-y-1">
                <strong class="font-bold block">Mohon perbaiki isian berikut:</strong>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. Foto Makanan (opsional, disarankan) -->
        <div>
            <label class="font-bold text-xs text-[#16251D] block mb-1.5">Foto Makanan (Opsional)</label>
            <div class="flex items-center gap-4">
                <div class="relative w-24 h-24 rounded-2xl bg-[#F2F6F3] border-2 border-dashed border-[#74877B] flex items-center justify-center overflow-hidden shrink-0">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!photoPreview">
                        <span class="text-2xl text-[#74877B]">📷</span>
                    </template>
                </div>
                <div class="space-y-1">
                    <input type="file" name="photo" id="photo-input" accept="image/*" @change="previewImage($event)" class="text-xs file:btn-secondary file:text-xs file:py-1 file:px-3 file:mr-2 file:border-0">
                    <p class="text-[11px] text-[#4E6157]">Maks. 2 MB. Metadata GPS & EXIF otomatis dihapus untuk keamanan privasi Anda.</p>
                </div>
            </div>
        </div>

        <!-- 2. Nama Makanan & Kategori -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="font-bold text-xs text-[#16251D] block mb-1">Nama Makanan <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $duplicate->title ?? '') }}" placeholder="Contoh: 15 Kotak Nasi Ayam Bakar Komplit" class="input-field text-sm" required>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" class="input-field text-sm" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $duplicate->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Jenis Pangan <span class="text-red-500">*</span></label>
                <select name="food_type" x-model="foodType" class="input-field text-sm" required>
                    <option value="cooked">Makanan Dimasak (Siap Santap)</option>
                    <option value="packaged">Makanan Kemasan</option>
                    <option value="raw">Bahan Mentah</option>
                </select>
            </div>
        </div>

        <!-- 3. Porsi & Batas Waktu Ambil -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-[#D5E0D9]">
            <!-- Portions Stepper -->
            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Jumlah Porsi Tersedia <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <button type="button" @click="portions = Math.max(1, portions - 1)" class="w-11 h-11 rounded-xl bg-gray-100 hover:bg-gray-200 text-lg font-bold flex items-center justify-center shrink-0">-</button>
                    <input type="number" name="portions_total" x-model="portions" min="1" max="500" class="input-field text-center font-bold text-base w-24" required>
                    <button type="button" @click="portions = portions + 1" class="w-11 h-11 rounded-xl bg-gray-100 hover:bg-gray-200 text-lg font-bold flex items-center justify-center shrink-0">+</button>
                    <span class="text-xs text-[#4E6157]">porsi</span>
                </div>
            </div>

            <!-- Batas Waktu Quick Chips (SDD 3.5) -->
            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Batas Waktu Ambil <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <template x-for="h in [1, 2, 4, 8]" :key="h">
                        <button type="button" @click="hours = h"
                                :class="hours === h ? 'bg-[#1E7A4C] text-white font-bold' : 'bg-gray-100 text-[#4E6157] hover:bg-gray-200'"
                                class="px-3 py-2 rounded-xl text-xs transition"
                                x-text="`+${h} jam`">
                        </button>
                    </template>
                </div>
                <input type="hidden" name="hours_valid" :value="hours">
                <span class="text-[11px] text-[#4E6157] block mt-1.5">
                    Makanan akan kedaluwarsa dalam <strong x-text="`${hours} jam ke depan`"></strong>.
                </span>
            </div>
        </div>

        <!-- 4. Titik Lokasi Pengambilan (Interactive Map & Autocomplete Search) -->
        <div class="space-y-3 pt-2 border-t border-[#D5E0D9]">
            <div class="flex items-center justify-between">
                <div>
                    <label class="font-bold text-xs text-[#16251D] block">Titik & Alamat Pengambilan <span class="text-red-500">*</span></label>
                    <p class="text-[11px] text-[#4E6157]">Klik peta atau cari alamat di bawah untuk menentukan titik temu presisi.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="toggleBasemap()" class="text-xs text-[#4E6157] hover:text-[#16251D] font-medium flex items-center gap-1">
                        <span x-text="basemap === 'satellite' ? '🛰️ Satelit' : '🗺️ Peta'"></span>
                    </button>
                    <button type="button" @click="detectLocation()" class="text-xs font-bold text-[#2A6F97] hover:underline flex items-center gap-1">
                        📍 Lokasi saya
                    </button>
                </div>
            </div>

            <!-- Location Search with Autocomplete Dropdown (Google Maps style) -->
            <div class="relative">
                <div class="relative">
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="fetchSuggestions()"
                           @focus="if (suggestions.length > 0) showSuggestions = true"
                           placeholder="Cari jalan, patokan toko, atau nama kelurahan... (misal: Tembalang, Jl. Sudirman)"
                           class="w-full pl-8 pr-8 py-2 text-xs rounded-xl border border-[#74877B] text-[#16251D] focus:ring-2 focus:ring-[#2A6F97] bg-white">
                    <span class="absolute left-2.5 top-2.5 text-xs text-gray-400">🔍</span>
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''; suggestions = []; showSuggestions = false;" class="absolute right-2.5 top-2 text-xs font-bold text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <!-- Suggestions dropdown -->
                <div x-show="showSuggestions && suggestions.length > 0"
                     @click.away="showSuggestions = false"
                     class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-[#D5E0D9] overflow-hidden z-30 divide-y divide-gray-100 max-h-48 overflow-y-auto">
                    <template x-for="(s, idx) in suggestions" :key="s.id || idx">
                        <div @click="selectSuggestion(s)" class="p-2.5 hover:bg-[#F2F7F4] cursor-pointer flex items-center gap-2 text-left">
                            <span class="text-xs text-[#1E7A4C]">📍</span>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-xs text-[#16251D] truncate" x-text="s.title"></p>
                                <p class="text-[10px] text-[#4E6157] truncate" x-text="s.subtitle || s.display_name"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Mini Map for Picking Coordinates -->
            <div id="picker-map" class="h-56 w-full rounded-2xl border border-[#D5E0D9] overflow-hidden relative"></div>

            <!-- Reverse Geocoding Address Suggestion Pill -->
            <template x-if="detectedAddress">
                <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between gap-2 text-xs">
                    <div class="truncate text-emerald-900">
                        📍 <strong>Titik dipilih:</strong> <span x-text="detectedAddress"></span>
                    </div>
                    <button type="button" @click="useDetectedAddress()" class="btn-primary text-[10px] py-1 px-2.5 shrink-0">
                        Pakai Alamat Ini
                    </button>
                </div>
            </template>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <input type="text" name="pickup_address" x-model="address" placeholder="Detail alamat/patokan (misal: Toko Roti Sari Rasa, Jl. Melati No. 4)" class="input-field text-xs" required>
                </div>
                <div>
                    <select name="district_id" x-model="selectedDistrict" class="input-field text-xs" required>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ old('district_id', $user->district_id ?? '') == $d->id ? 'selected' : '' }}>
                                Kec. {{ $d->name }} ({{ $d->regency->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Safe Meeting Zone Recommendations (design.md 1.C) -->
            <div class="space-y-1.5">
                <span class="text-[11px] font-bold text-[#4E6157] block">Rekomendasi Titik Temu Aman (Untuk Menjaga Privasi Rumah Anda):</span>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" @click="appendSafeZone('Pos Satpam / Gerbang Masuk')" class="px-2.5 py-1 bg-gray-100 hover:bg-emerald-50 hover:text-[#1E7A4C] text-[11px] rounded-lg border border-[#D5E0D9] transition">
                        🏢 Pos Satpam / Gerbang
                    </button>
                    <button type="button" @click="appendSafeZone('Minimarket Terdekat (Area Parkir)')" class="px-2.5 py-1 bg-gray-100 hover:bg-emerald-50 hover:text-[#1E7A4C] text-[11px] rounded-lg border border-[#D5E0D9] transition">
                        🏪 Minimarket 24 Jam
                    </button>
                    <button type="button" @click="appendSafeZone('Halaman Masjid')" class="px-2.5 py-1 bg-gray-100 hover:bg-emerald-50 hover:text-[#1E7A4C] text-[11px] rounded-lg border border-[#D5E0D9] transition">
                        🕌 Halaman Masjid
                    </button>
                    <button type="button" @click="appendSafeZone('SPBU Terdekat')" class="px-2.5 py-1 bg-gray-100 hover:bg-emerald-50 hover:text-[#1E7A4C] text-[11px] rounded-lg border border-[#D5E0D9] transition">
                        ⛽ SPBU Terdekat
                    </button>
                </div>
            </div>

            <!-- Coordinates hidden inputs -->
            <input type="hidden" name="latitude" :value="lat">
            <input type="hidden" name="longitude" :value="lng">

            <!-- Privacy Guarantee notice -->
            <div class="p-3 bg-blue-50/60 rounded-xl border border-[#2A6F97]/30 text-[11px] text-[#2A6F97]">
                🔒 <strong>Privasi Anda Terjaga:</strong> Pengguna publik hanya melihat titik perkiraan (~200m). Alamat lengkap baru diberikan setelah Anda menyetujui klaim.
            </div>
        </div>

        <!-- 5. Pernyataan & Keamanan Pangan -->
        <div class="space-y-3 pt-2 border-t border-[#D5E0D9]">
            <div class="flex items-start gap-2.5">
                <input type="checkbox" name="halal_claim" value="1" id="halal-check" class="mt-0.5 rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                <label for="halal-check" class="text-xs text-[#16251D]">
                    <strong>Saya menyatakan makanan ini halal</strong> (sesuai bahan dan proses pembuatannya).
                </label>
            </div>

            <div class="flex items-start gap-2.5">
                <input type="checkbox" name="is_vegetarian" value="1" id="veg-check" class="mt-0.5 rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                <label for="veg-check" class="text-xs text-[#16251D]">
                    Makanan ini vegetarian (tidak mengandung daging/unggas/seafood).
                </label>
            </div>
        </div>

        <!-- 6. Opsi Lanjutan (Terlipat) -->
        <div x-data="{ open: false }" class="border-t border-[#D5E0D9] pt-3">
            <button type="button" @click="open = !open" class="text-xs font-bold text-[#4E6157] hover:text-[#16251D] flex items-center justify-between w-full">
                <span>Opsi Lanjutan (Deskripsi, Batas Klaim, Galang, Alergen)</span>
                <span x-text="open ? '▲ Tutup' : '▼ Buka'"></span>
            </button>

            <div x-show="open" x-collapse class="space-y-4 pt-4">
                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Deskripsi Tambahan</label>
                    <textarea name="description" rows="2" class="input-field text-xs" placeholder="Informasi tambahan mengenai kondisi makanan, sendok garpu, dll."></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1">Maksimal Porsi per Pengambil</label>
                        <input type="number" name="max_portions_per_claim" value="3" min="1" max="20" class="input-field text-xs">
                        <span class="text-[10px] text-[#4E6157]">Mencegah penimbunan porsi oleh 1 orang.</span>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1">Tujukan untuk Galang Makanan (Opsional)</label>
                        <select name="campaign_id" class="input-field text-xs">
                            <option value="">-- Terbuka untuk umum --</option>
                            @foreach($campaigns as $camp)
                                <option value="{{ $camp->id }}">Galang: {{ $camp->title }} ({{ $camp->beneficiary_name }})</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-[#4E6157]">Diprioritaskan untuk relawan galang selama 2 jam pertama.</span>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1.5">Informasi Alergen</label>
                    <div class="flex items-center gap-3 flex-wrap text-xs">
                        <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="allergens[]" value="Telur"> Telur</label>
                        <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="allergens[]" value="Susu"> Susu/Dairy</label>
                        <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="allergens[]" value="Kacang"> Kacang-kacangan</label>
                        <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="allergens[]" value="Gluten"> Gluten/Gandum</label>
                        <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="allergens[]" value="Seafood"> Seafood</label>
                        <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="allergens[]" value="Kedelai"> Kedelai</label>
                    </div>
                </div>

                @if($user->is_verified)
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                        <label class="inline-flex items-center gap-2 text-xs font-bold text-emerald-900 cursor-pointer">
                            <input type="checkbox" name="show_exact_location" value="1">
                            <span>Tampilkan titik presisi di peta (Hak Istimewa Usaha/Lembaga Terverifikasi)</span>
                        </label>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit" class="btn-primary w-full text-base py-3">
                Simpan & Bagikan Makanan Sekarang
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('createListingForm', () => ({
        photoPreview: null,
        foodType: 'cooked',
        portions: {{ old('portions_total', $duplicate->portions_total ?? 5) }},
        hours: 2,
        lat: {{ $user->default_latitude ?? -7.05194 }},
        lng: {{ $user->default_longitude ?? 110.43778 }},
        address: '{{ old('pickup_address', $duplicate->pickup_address ?? '') }}',
        selectedDistrict: '{{ old('district_id', $user->district_id ?? 1) }}',
        detectedAddress: '',
        searchQuery: '',
        suggestions: [],
        showSuggestions: false,
        basemap: 'standard',
        map: null,
        marker: null,
        osmLayer: null,
        satLayer: null,

        init() {
            this.$nextTick(() => {
                this.initPickerMap();
                this.reverseGeocode();
            });
        },

        initPickerMap() {
            this.map = L.map('picker-map').setView([this.lat, this.lng], 15);

            this.osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            this.satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: '&copy; Esri World Imagery'
            });

            this.marker = L.marker([this.lat, this.lng], {
                draggable: true
            }).addTo(this.map);

            this.marker.bindTooltip("Geser atau klik peta untuk atur titik", {
                direction: 'top',
                offset: [0, -10]
            });

            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.lat = pos.lat;
                this.lng = pos.lng;
                this.reverseGeocode();
            });

            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.lat = e.latlng.lat;
                this.lng = e.latlng.lng;
                this.reverseGeocode();
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
                // Ignore
            }
        },

        selectSuggestion(s) {
            this.searchQuery = s.title;
            this.showSuggestions = false;
            this.lat = s.lat;
            this.lng = s.lng;
            this.marker.setLatLng([s.lat, s.lng]);
            this.map.flyTo([s.lat, s.lng], 16);
            this.detectedAddress = s.display_name;
            if (!this.address) {
                this.address = s.display_name;
            }
        },

        async reverseGeocode() {
            try {
                const res = await fetch(`/api/geocoding/reverse?lat=${this.lat}&lng=${this.lng}`);
                if (res.ok) {
                    const data = await res.json();
                    this.detectedAddress = data.short_address || data.display_name || '';
                }
            } catch (e) {
                // Ignore
            }
        },

        useDetectedAddress() {
            if (this.detectedAddress) {
                this.address = this.detectedAddress;
            }
        },

        appendSafeZone(zoneName) {
            if (this.address) {
                this.address = `${this.address} (${zoneName})`;
            } else {
                this.address = `Titik Temu: ${zoneName}`;
            }
        },

        detectLocation() {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition((pos) => {
                this.lat = pos.coords.latitude;
                this.lng = pos.coords.longitude;
                this.map.flyTo([this.lat, this.lng], 16);
                this.marker.setLatLng([this.lat, this.lng]);
                this.reverseGeocode();
            });
        },

        previewImage(e) {
            const file = e.target.files[0];
            if (file) {
                this.photoPreview = URL.createObjectURL(file);
            }
        }
    }));
});
</script>
@endpush

</x-layouts.app>
