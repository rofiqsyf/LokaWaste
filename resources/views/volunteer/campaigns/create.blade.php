<x-layouts.app title="Buat Galang Makanan — Locabite">

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8" x-data="createCampaignForm()">

    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] inline-flex items-center gap-1 mb-2">
            &larr; Batal & Kembali
        </a>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Buat Artikel Galang Makanan</h1>
        <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Publikasikan kebutuhan pangan panti, pesantren, atau tunawisma di kecamatan binaan Anda.</p>
    </div>

    <form method="POST" action="{{ route('volunteer.campaigns.store') }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-6 sm:p-8 space-y-6">
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

        <!-- 1. Judul & Kecamatan Binaan -->
        <div class="space-y-4">
            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Judul Galang Makanan <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Makan Malam untuk 40 Anak Panti Asuhan Nurul Huda" class="input-field text-sm" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Kecamatan Binaan Anda <span class="text-red-500">*</span></label>
                    <select name="district_id" x-model="selectedDistrict" class="input-field text-sm" required>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" data-lat="{{ $d->latitude }}" data-lng="{{ $d->longitude }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>
                                Kec. {{ $d->name }} ({{ $d->regency->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Jenis Penerima Manfaat <span class="text-red-500">*</span></label>
                    <select name="beneficiary_type" x-model="beneficiaryType" class="input-field text-sm" required>
                        <option value="panti_asuhan">Panti Asuhan</option>
                        <option value="ponpes">Pondok Pesantren</option>
                        <option value="panti_jompo">Panti Jompo / Lansia</option>
                        <option value="tunawisma">Komunitas Tunawisma</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Nama Penerima & Alamat -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-[#D5E0D9]">
            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Nama Lembaga / Komunitas <span class="text-red-500">*</span></label>
                <input type="text" name="beneficiary_name" value="{{ old('beneficiary_name') }}" placeholder="Contoh: Panti Asuhan Nurul Huda" class="input-field text-sm" required>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Kebutuhan Harian Singkat</label>
                <input type="text" name="daily_need" value="{{ old('daily_need') }}" placeholder="Contoh: 40 porsi makan malam" class="input-field text-sm">
            </div>

            <div class="sm:col-span-2">
                <label class="font-bold text-xs text-[#16251D] block mb-1">Alamat Penyaluran / Titik Kumpul <span class="text-red-500">*</span></label>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="Jl. Melati No. 12, Kel. Bulusan" class="input-field text-sm" required>
            </div>
        </div>

        <!-- 3. Target & Periode -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-[#D5E0D9]">
            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Target Porsi Total <span class="text-red-500">*</span></label>
                <input type="number" name="target_portions" value="{{ old('target_portions', 300) }}" min="1" class="input-field text-sm font-bold" required>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="starts_at" value="{{ old('starts_at', now()->format('Y-m-d')) }}" class="input-field text-sm" required>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Tanggal Berakhir <span class="text-red-500">*</span></label>
                <input type="date" name="ends_at" value="{{ old('ends_at', now()->addDays(30)->format('Y-m-d')) }}" class="input-field text-sm" required>
            </div>
        </div>

        <!-- 4. Peta Titik Penyaluran & Privasi Presisi (SDD 3.7 Rule 23 / 25) -->
        <div class="space-y-3 pt-2 border-t border-[#D5E0D9]">
            <div class="flex items-center justify-between">
                <div>
                    <label class="font-bold text-xs text-[#16251D] block">Titik Koordinat Penyaluran <span class="text-red-500">*</span></label>
                    <p class="text-[11px] text-[#4E6157]">Klik peta atau cari alamat di bawah untuk menentukan lokasi presisi.</p>
                </div>
                <div class="text-xs font-bold text-[#2A6F97]" x-text="`(${lat.toFixed(5)}, ${lng.toFixed(5)})`"></div>
            </div>

            <!-- Autocomplete Search for Campaign Location -->
            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       @input.debounce.300ms="fetchSuggestions()"
                       @focus="if (suggestions.length > 0) showSuggestions = true"
                       placeholder="Cari nama tempat / jalan penyaluran (misal: Jl. Bulusan, Panti Asuhan)..."
                       class="w-full pl-8 pr-8 py-2 text-xs rounded-xl border border-[#74877B] text-[#16251D] focus:ring-2 focus:ring-[#2A6F97] bg-white">
                <span class="absolute left-2.5 top-2.5 text-xs text-gray-400">🔍</span>
                <button type="button" x-show="searchQuery" @click="searchQuery = ''; suggestions = []; showSuggestions = false;" class="absolute right-2.5 top-2 text-xs font-bold text-gray-400 hover:text-gray-600">✕</button>

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

            <div id="camp-map" class="h-56 w-full rounded-2xl border border-[#D5E0D9] overflow-hidden relative"></div>

            <input type="hidden" name="latitude" :value="lat">
            <input type="hidden" name="longitude" :value="lng">

            <!-- Presisi Lokasi Selection (Area vs Exact) -->
            <div class="p-4 bg-gray-50 rounded-2xl border border-[#D5E0D9] space-y-2">
                <label class="font-bold text-xs text-[#16251D] block">Presisi Tampilan Lokasi di Peta Publik:</label>
                <div class="flex items-center gap-4 text-xs">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="location_precision" value="exact" :checked="beneficiaryType !== 'tunawisma'" class="text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <span>Titik Tepat (Cocok untuk Panti Asuhan/Lembaga Tetap)</span>
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="location_precision" value="area" :checked="beneficiaryType === 'tunawisma'" class="text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <span>Tampilkan sebagai Area Saja (Wajib untuk Tunawisma demi Privasi)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 5. Foto Sampul & Cerita -->
        <div class="space-y-4 pt-2 border-t border-[#D5E0D9]">
            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Foto Sampul (Opsional)</label>
                <input type="file" name="cover" accept="image/*" class="text-xs">
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Isi Artikel / Cerita Kebutuhan <span class="text-red-500">*</span></label>
                <textarea name="body" rows="6" class="input-field text-sm" placeholder="Ceritakan profil penerima, berapa anak asuh atau warga yang membutuhkan, jadwal makan malam, dll..." required>{{ old('body') }}</textarea>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-primary w-full text-base py-3">
                Simpan Sebagai Draf Galang
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('createCampaignForm', () => ({
        selectedDistrict: '{{ $districts->first()->id ?? 1 }}',
        beneficiaryType: 'panti_asuhan',
        lat: {{ $districts->first()->latitude ?? -7.05194 }},
        lng: {{ $districts->first()->longitude ?? 110.43778 }},
        searchQuery: '',
        suggestions: [],
        showSuggestions: false,
        map: null,
        marker: null,

        init() {
            this.$nextTick(() => this.initMap());
        },

        initMap() {
            this.map = L.map('camp-map').setView([this.lat, this.lng], 14);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);

            this.marker.bindTooltip("Geser atau klik peta untuk atur titik", {
                direction: 'top',
                offset: [0, -10]
            });

            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.lat = pos.lat;
                this.lng = pos.lng;
            });

            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.lat = e.latlng.lat;
                this.lng = e.latlng.lng;
            });
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
            this.map.flyTo([s.lat, s.lng], 15);
        }
    }));
});
</script>
@endpush

</x-layouts.app>
