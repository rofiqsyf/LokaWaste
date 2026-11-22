<x-layouts.app title="Daftar Akun — LokaWaste">

<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-xl w-full bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-6 sm:p-8 space-y-6">

        <div class="text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-[#1E7A4C] flex items-center justify-center text-white font-display font-bold text-2xl mb-3 shadow-sm">
                L
            </div>
            <h1 class="font-display font-bold text-2xl text-[#16251D]">Daftar Akun Baru</h1>
            <p class="text-xs text-[#4E6157] mt-1">Satu jenis akun untuk membagikan dan mengambil makanan berlebih.</p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-[#C93A2E] rounded-xl text-xs text-[#C93A2E] space-y-1">
                @foreach ($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="wilayahSelector(@js($provinces))">
            @csrf

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="input-field text-sm" placeholder="Nama Anda" required autofocus>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="input-field text-sm" placeholder="nama@email.com" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Kata Sandi <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="input-field text-sm" placeholder="Min. 8 karakter" required>
                </div>

                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Ulangi Sandi <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="input-field text-sm" placeholder="Konfirmasi sandi" required>
                </div>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Nomor WhatsApp / HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="input-field text-sm" placeholder="08123456789">
            </div>

            <!-- Wilayah Indonesia Section (EMSIFA API) -->
            <div class="p-4 bg-[#F2F7F4] border border-[#D5E0D9] rounded-2xl space-y-3">
                <div class="flex items-center justify-between border-b border-[#D5E0D9] pb-2">
                    <label class="font-bold text-xs text-[#16251D] flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Wilayah Domisili (Seluruh Indonesia)
                    </label>
                    <span x-show="loading" class="text-[11px] font-bold text-[#1E7A4C] animate-pulse">
                        Memuat data wilayah...
                    </span>
                </div>

                <!-- 1. Provinsi -->
                <div>
                    <label class="text-[11px] font-semibold text-[#4E6157] block mb-1">1. Provinsi</label>
                    <select x-model="selectedProvince" @change="onProvinceChange()" class="input-field text-sm">
                        <option value="">-- Pilih Provinsi --</option>
                        <template x-for="p in provinces" :key="p.id">
                            <option :value="p.id" x-text="p.name"></option>
                        </template>
                    </select>
                    <input type="hidden" name="province_id" :value="selectedProvince">
                    <input type="hidden" name="province_name" :value="selectedProvinceName">
                </div>

                <!-- 2. Kabupaten / Kota -->
                <div>
                    <label class="text-[11px] font-semibold text-[#4E6157] block mb-1">2. Kabupaten / Kota</label>
                    <select x-model="selectedRegency" @change="onRegencyChange()" class="input-field text-sm" :disabled="!selectedProvince || loadingRegencies">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        <template x-for="r in regencies" :key="r.id">
                            <option :value="r.id" x-text="r.name"></option>
                        </template>
                    </select>
                    <input type="hidden" name="regency_id" :value="selectedRegency">
                    <input type="hidden" name="regency_name" :value="selectedRegencyName">
                </div>

                <!-- 3. Kecamatan -->
                <div>
                    <label class="text-[11px] font-semibold text-[#4E6157] block mb-1">3. Kecamatan</label>
                    <select x-model="selectedDistrict" @change="onDistrictChange()" name="district_id" class="input-field text-sm" :disabled="!selectedRegency || loadingDistricts">
                        <option value="">-- Pilih Kecamatan --</option>
                        <template x-for="d in districts" :key="d.id">
                            <option :value="d.id" x-text="'Kec. ' + d.name"></option>
                        </template>
                    </select>
                    <input type="hidden" name="district_code" :value="selectedDistrict">
                    <input type="hidden" name="district_name" :value="selectedDistrictName">
                </div>

                <template x-if="selectedDistrictName">
                    <div class="text-[11px] text-[#1E7A4C] bg-white border border-[#D5E0D9] p-2.5 rounded-xl flex items-center gap-1.5 font-medium">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Domisili: <strong x-text="`Kec. ${selectedDistrictName}, ${selectedRegencyName}, ${selectedProvinceName}`"></strong></span>
                    </div>
                </template>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Nama Usaha / Lembaga (Opsional)</label>
                <input type="text" name="organization_name" value="{{ old('organization_name') }}" class="input-field text-sm" placeholder="Isi jika Anda pemilik resto, katering, atau warung">
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-sm">
                Daftar Akun Sekarang
            </button>
        </form>

        <div class="text-center pt-2 text-xs text-[#4E6157]">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-[#1E7A4C] hover:underline">
                Masuk di sini
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('wilayahSelector', (initialProvinces = []) => ({
        provinces: initialProvinces,
        regencies: [],
        districts: [],

        selectedProvince: '',
        selectedProvinceName: '',
        selectedRegency: '',
        selectedRegencyName: '',
        selectedDistrict: '',
        selectedDistrictName: '',

        loadingRegencies: false,
        loadingDistricts: false,

        get loading() {
            return this.loadingRegencies || this.loadingDistricts;
        },

        init() {
            if (!this.provinces || this.provinces.length === 0) {
                this.fetchProvinces();
            }
        },

        async fetchProvinces() {
            try {
                const res = await fetch('/api/wilayah/provinces');
                if (res.ok) {
                    this.provinces = await res.json();
                } else {
                    throw new Error();
                }
            } catch (e) {
                try {
                    const fallback = await fetch('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
                    const raw = await fallback.json();
                    this.provinces = raw.map(p => ({
                        id: p.id,
                        name: this.titleCase(p.name)
                    }));
                } catch (err) {
                    console.error("Gagal memuat provinsi", err);
                }
            }
        },

        async onProvinceChange() {
            this.selectedRegency = '';
            this.selectedRegencyName = '';
            this.selectedDistrict = '';
            this.selectedDistrictName = '';
            this.regencies = [];
            this.districts = [];

            const p = this.provinces.find(item => item.id == this.selectedProvince);
            this.selectedProvinceName = p ? p.name : '';

            if (!this.selectedProvince) return;

            this.loadingRegencies = true;
            try {
                const res = await fetch(`/api/wilayah/regencies/${this.selectedProvince}`);
                if (res.ok) {
                    this.regencies = await res.json();
                } else {
                    throw new Error();
                }
            } catch (e) {
                try {
                    const fallback = await fetch(`https://emsifa.github.io/api-wilayah-indonesia/api/regencies/${this.selectedProvince}.json`);
                    const raw = await fallback.json();
                    this.regencies = raw.map(r => ({
                        id: r.id,
                        province_id: r.province_id,
                        name: this.titleCase(r.name)
                    }));
                } catch (err) {
                    console.error("Gagal memuat kabupaten/kota", err);
                }
            } finally {
                this.loadingRegencies = false;
            }
        },

        async onRegencyChange() {
            this.selectedDistrict = '';
            this.selectedDistrictName = '';
            this.districts = [];

            const r = this.regencies.find(item => item.id == this.selectedRegency);
            this.selectedRegencyName = r ? r.name : '';

            if (!this.selectedRegency) return;

            this.loadingDistricts = true;
            try {
                const res = await fetch(`/api/wilayah/districts/${this.selectedRegency}`);
                if (res.ok) {
                    this.districts = await res.json();
                } else {
                    throw new Error();
                }
            } catch (e) {
                try {
                    const fallback = await fetch(`https://emsifa.github.io/api-wilayah-indonesia/api/districts/${this.selectedRegency}.json`);
                    const raw = await fallback.json();
                    this.districts = raw.map(d => ({
                        id: d.id,
                        regency_id: d.regency_id,
                        name: this.titleCase(d.name)
                    }));
                } catch (err) {
                    console.error("Gagal memuat kecamatan", err);
                }
            } finally {
                this.loadingDistricts = false;
            }
        },

        onDistrictChange() {
            const d = this.districts.find(item => item.id == this.selectedDistrict);
            this.selectedDistrictName = d ? d.name : '';
        },

        titleCase(str) {
            if (!str) return '';
            return str.toLowerCase().replace(/(?:^|\s)\S/g, function(a) {
                return a.toUpperCase();
            });
        }
    }));
});
</script>
@endpush

</x-layouts.app>
