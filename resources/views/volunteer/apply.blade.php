<x-layouts.app title="Ajukan Diri Sebagai Relawan — Locabite">

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8" x-data="volunteerApply()">

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] inline-flex items-center gap-1 mb-2">
            &larr; Kembali
        </a>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Jadi Relawan Wilayah</h1>
        <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Bantu menyalurkan makanan berlebih di wilayahmu ke panti asuhan, ponpes, dan tunawisma. Setiap pengajuan ditinjau per kecamatan oleh admin.</p>
    </div>

    <!-- Application Form with numbered steps (SDD 4.7) -->
    <form method="POST" action="{{ route('volunteer.apply.submit') }}" class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-6 sm:p-8 space-y-6">
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

        <!-- Step 1: Pilih Kabupaten/Kota -->
        <div class="space-y-2">
            <label class="font-display font-bold text-sm text-[#16251D] flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-[#1E7A4C] text-white text-xs flex items-center justify-center shrink-0">1</span>
                Pilih Kabupaten / Kota <span class="text-red-500">*</span>
            </label>
            <select name="regency_id" x-model="selectedRegency" @change="fetchDistricts()" class="input-field text-sm" required>
                <option value="">-- Pilih Kabupaten/Kota --</option>
                @foreach($regencies as $reg)
                    <option value="{{ $reg->id }}">
                        {{ $reg->name }} ({{ $reg->province->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Step 2: Pilih Kecamatan (SDD 3.9 Hierarchical Picker) -->
        <div class="space-y-2 pt-2 border-t border-[#D5E0D9]">
            <div class="flex items-center justify-between">
                <label class="font-display font-bold text-sm text-[#16251D] flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#1E7A4C] text-white text-xs flex items-center justify-center shrink-0">2</span>
                    Pilih Kecamatan yang Ingin Anda Ampu <span class="text-red-500">*</span>
                </label>
                <template x-if="districts.length > 0">
                    <button type="button" @click="selectAllDistricts()" class="text-xs font-bold text-[#1E7A4C] hover:underline">
                        Pilih semua
                    </button>
                </template>
            </div>

            <template x-if="!selectedRegency">
                <p class="text-xs text-[#4E6157] italic p-3 bg-gray-50 rounded-xl">
                    Pilih kabupaten/kota di atas terlebih dahulu untuk menampilkan daftar kecamatan.
                </p>
            </template>

            <template x-if="loadingDistricts">
                <div class="p-4 text-center text-xs text-[#4E6157] animate-pulse">
                    Memuat daftar kecamatan...
                </div>
            </template>

            <template x-if="selectedRegency && !loadingDistricts">
                <div class="border border-[#74877B] rounded-2xl p-4 max-h-56 overflow-y-auto space-y-2.5">
                    <template x-for="d in districts" :key="d.id">
                        <label class="flex items-center gap-3 text-xs text-[#16251D] hover:bg-gray-50 p-1.5 rounded-lg cursor-pointer">
                            <input type="checkbox" name="district_ids[]" :value="d.id" x-model="selectedDistricts"
                                   class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C] w-4 h-4">
                            <span class="font-medium" x-text="`Kec. ${d.name}`"></span>
                        </label>
                    </template>
                </div>
            </template>

            <template x-if="selectedDistricts.length > 0">
                <p class="text-xs font-bold text-[#1E7A4C]">
                    Terpilih: <span x-text="selectedDistricts.length"></span> kecamatan
                </p>
            </template>
        </div>

        <!-- Step 3: Motivasi -->
        <div class="space-y-2 pt-2 border-t border-[#D5E0D9]">
            <label class="font-display font-bold text-sm text-[#16251D] flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-[#1E7A4C] text-white text-xs flex items-center justify-center shrink-0">3</span>
                Tulis Alasan & Motivasi Singkat <span class="text-red-500">*</span>
            </label>
            <textarea name="motivation" rows="4" class="input-field text-sm"
                      placeholder="Ceritakan latar belakang, komunitas pangan, atau panti asuhan yang biasa Anda bantu di kecamatan terpilih..." required></textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit" class="btn-primary w-full text-base py-3" :disabled="!selectedRegency || selectedDistricts.length === 0">
                Kirim Pengajuan Relawan
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('volunteerApply', () => ({
        selectedRegency: '',
        districts: [],
        selectedDistricts: [],
        loadingDistricts: false,

        async fetchDistricts() {
            if (!this.selectedRegency) {
                this.districts = [];
                this.selectedDistricts = [];
                return;
            }

            this.loadingDistricts = true;
            this.selectedDistricts = [];

            try {
                const res = await fetch(`/api/regions/regencies/${this.selectedRegency}/districts`);
                this.districts = await res.json();
            } catch (e) {
                alert("Gagal memuat kecamatan.");
            } finally {
                this.loadingDistricts = false;
            }
        },

        selectAllDistricts() {
            this.selectedDistricts = this.districts.map(d => d.id);
        }
    }));
});
</script>
@endpush

</x-layouts.app>
