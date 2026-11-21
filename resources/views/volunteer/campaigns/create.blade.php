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
            <div>
                <label class="font-bold text-xs text-[#16251D] block">Titik Koordinat Penyaluran <span class="text-red-500">*</span></label>
                <p class="text-[11px] text-[#4E6157]">Geser pin pada peta untuk menentukan lokasi tujuan penerima manfaat.</p>
            </div>

            <div id="camp-map" class="h-48 w-full rounded-2xl border border-[#D5E0D9] overflow-hidden"></div>

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
        }
    }));
});
</script>
@endpush

</x-layouts.app>
