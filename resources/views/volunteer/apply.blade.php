<x-layouts.app title="Ajukan Diri Sebagai Relawan — Locabite">

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8" x-data="volunteerApply(@js($provinces))">

    <!-- Top Navigation -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] mb-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dashboard
        </a>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Daftar Relawan Penyelamat Pangan</h1>
        <p class="text-xs sm:text-sm text-[#4E6157] mt-1">
            Bantu menjemput dan menyalurkan surplus makanan dari donor (resto, katering, supermarket) ke panti asuhan, ponpes, dan tunawisma di kecamatan pilihan Anda.
        </p>
    </div>

    <!-- Active Areas Alert (If already has areas) -->
    @if($myAreas->isNotEmpty())
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-[#1E7A4C]/30 flex items-start gap-3 text-xs">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-[#1E7A4C] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-[#14573A]">Anda Sudah Terdaftar di Wilayah Tertentu</h3>
                <p class="text-[#4E6157] mt-0.5">
                    Saat ini Anda memiliki {{ $myAreas->count() }} kecamatan dalam pengawasan. Anda dapat mengajukan kecamatan atau kabupaten baru melalui formulir ini.
                </p>
                <a href="{{ route('volunteer.areas') }}" class="text-[#1E7A4C] font-bold hover:underline inline-flex items-center gap-1 mt-1.5">
                    <span>Lihat Wilayah Aktif Saya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    @endif

    <!-- Application Form with 4 Clean Steps -->
    <form method="POST" action="{{ route('volunteer.apply.submit') }}" class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-6 sm:p-8 space-y-8">
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

        <!-- Hidden Inputs for EMSIFA Hierarchy Metadata -->
        <input type="hidden" name="province_id" :value="selectedProvince">
        <input type="hidden" name="province_name" :value="selectedProvinceName">
        <input type="hidden" name="regency_id" :value="selectedRegency">
        <input type="hidden" name="regency_name" :value="selectedRegencyName">
        <input type="hidden" name="district_names" :value="JSON.stringify(districtNamesMap)">

        <!-- STEP 1: PILIH WILAYAH (EMSIFA API) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-full bg-[#1E7A4C] text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                    <div>
                        <h2 class="font-display font-bold text-base text-[#16251D]">Wilayah Tanggung Jawab Relawan</h2>
                        <p class="text-xs text-[#4E6157]">Pilih provinsi, kabupaten/kota, dan kecamatan yang siap Anda ampu (EMSIFA Seluruh Indonesia)</p>
                    </div>
                </div>

                <template x-if="loading">
                    <span class="text-xs font-bold text-[#1E7A4C] animate-pulse flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Memuat data...
                    </span>
                </template>
            </div>

            <!-- 1.1 Provinsi Dropdown -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-[#16251D] block">
                    1. Provinsi <span class="text-red-500">*</span>
                </label>
                <select x-model="selectedProvince" @change="onProvinceChange()" class="input-field text-sm" required>
                    <option value="">-- Pilih Provinsi --</option>
                    <template x-for="p in provinces" :key="p.id">
                        <option :value="p.id" x-text="p.name"></option>
                    </template>
                </select>
            </div>

            <!-- 1.2 Kabupaten / Kota Dropdown -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-[#16251D] block">
                    2. Kabupaten / Kota <span class="text-red-500">*</span>
                </label>
                <select x-model="selectedRegency" @change="onRegencyChange()" class="input-field text-sm" :disabled="!selectedProvince || loadingRegencies" required>
                    <option value="">-- Pilih Kabupaten / Kota --</option>
                    <template x-for="r in regencies" :key="r.id">
                        <option :value="r.id" x-text="r.name"></option>
                    </template>
                </select>
            </div>

            <!-- 1.3 Kecamatan Multi-Select Checklist (EMSIFA API) -->
            <div class="space-y-2 pt-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-[#16251D]">
                        3. Kecamatan yang Ingin Anda Ampu <span class="text-red-500">*</span>
                    </label>

                    <template x-if="districts.length > 0">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="selectAllDistricts()" class="text-xs font-bold text-[#1E7A4C] hover:underline cursor-pointer">
                                Pilih Semua (<span x-text="districts.length"></span>)
                            </button>
                            <span class="text-gray-300">|</span>
                            <button type="button" @click="deselectAllDistricts()" class="text-xs font-semibold text-[#4E6157] hover:underline cursor-pointer">
                                Bersihkan
                            </button>
                        </div>
                    </template>
                </div>

                <template x-if="!selectedRegency">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-xs text-[#4E6157] text-center">
                        Pilih provinsi dan kabupaten/kota di atas terlebih dahulu untuk menampilkan daftar kecamatan.
                    </div>
                </template>

                <template x-if="loadingDistricts">
                    <div class="p-6 text-center text-xs text-[#4E6157] bg-gray-50 rounded-2xl border border-gray-100 animate-pulse">
                        Mengambil daftar kecamatan dari server wilayah...
                    </div>
                </template>

                <template x-if="selectedRegency && !loadingDistricts && districts.length > 0">
                    <div class="space-y-2">
                        <!-- Search filter for districts inside regency -->
                        <div class="relative">
                            <input type="text"
                                   x-model="districtSearch"
                                   placeholder="Saring nama kecamatan..."
                                   class="w-full text-xs pl-8 pr-3 py-1.5 rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1E7A4C]">
                            <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Scrollable Checkbox Grid -->
                        <div class="border border-[#D5E0D9] rounded-2xl p-3 max-h-56 overflow-y-auto grid grid-cols-1 sm:grid-cols-2 gap-2 bg-[#FBFDFB]">
                            <template x-for="d in filteredDistricts" :key="d.id">
                                <label class="flex items-center gap-2.5 text-xs text-[#16251D] hover:bg-emerald-50/60 p-2 rounded-xl cursor-pointer border border-transparent hover:border-emerald-200/60 transition">
                                    <input type="checkbox"
                                           name="district_ids[]"
                                           :value="d.id"
                                           x-model="selectedDistricts"
                                           class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C] w-4 h-4 shrink-0">
                                    <span class="font-medium truncate" x-text="`Kec. ${d.name}`"></span>
                                </label>
                            </template>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1 px-1">
                            <span class="text-[#4E6157]">
                                Total kecamatan di wilayah ini: <strong x-text="districts.length"></strong>
                            </span>
                            <span class="font-bold text-[#1E7A4C]" x-text="`Terpilih: ${selectedDistricts.length} kecamatan`"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- STEP 2: LOGISTIK & KESIAPAN OPERASIONAL RELAWAN -->
        <div class="space-y-4 pt-4 border-t border-[#D5E0D9]">
            <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                <span class="w-7 h-7 rounded-full bg-[#1E7A4C] text-white text-xs font-bold flex items-center justify-center shrink-0">2</span>
                <div>
                    <h2 class="font-display font-bold text-base text-[#16251D]">Logistik & Kesiapan Operasional</h2>
                    <p class="text-xs text-[#4E6157]">Informasi moda transportasi dan waktu siaga untuk penjemputan makanan</p>
                </div>
            </div>

            <!-- 2.1 WhatsApp Phone -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-[#16251D] block">
                    Nomor WhatsApp Aktif (Siaga Koordinasi Darurat) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="tel"
                           name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="081234567890"
                           class="input-field text-sm pl-10" required>
                    <svg class="w-4 h-4 text-emerald-600 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <p class="text-[11px] text-[#4E6157]">Digunakan oleh admin dan donor makanan untuk koordinasi penjemputan cepat jika makanan harus segera diselamatkan.</p>
            </div>

            <!-- 2.2 Moda Transportasi / Kapasitas -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#16251D] block">
                    Moda Transportasi Utama Anda <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="transportation" value="Sepeda Motor (Box/Tas Kurir)" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]" checked>
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Sepeda Motor</strong>
                            <span class="text-[#4E6157] text-[11px]">Dilengkapi tas/box kurir. Efektif membawa 5 - 20 porsi makanan.</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="transportation" value="Mobil Pribadi / Minibus" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Mobil Pribadi / Minibus</strong>
                            <span class="text-[#4E6157] text-[11px]">Kapasitas besar. Mampu menjemput surplus katering &gt; 50 porsi.</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="transportation" value="Sepeda / Jalan Kaki" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Sepeda Ontel / Jalan Kaki</strong>
                            <span class="text-[#4E6157] text-[11px]">Radius dekat perkampungan (&lt; 2 km) untuk pemukiman padat.</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="transportation" value="Tanpa Kendaraan (Koordinasi Titik Temu)" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Tanpa Kendaraan Langsung</strong>
                            <span class="text-[#4E6157] text-[11px]">Fokus mengelola galang makanan dan koordinasi titik temu.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2.3 Ketersediaan Waktu Siaga -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#16251D] block">
                    Ketersediaan Waktu Siaga <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="availability" value="Fleksibel (Kapan Saja)" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]" checked>
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Fleksibel / Kapan Saja</strong>
                            <span class="text-[#4E6157] text-[11px]">Siaga saat ada pemberitahuan surplus mendadak di kecamatan.</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="availability" value="Hari Kerja (Senin - Jumat)" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Hari Kerja Saja</strong>
                            <span class="text-[#4E6157] text-[11px]">Senin s/d Jumat (siang atau sore hari).</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="availability" value="Akhir Pekan (Sabtu - Minggu)" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Akhir Pekan Saja</strong>
                            <span class="text-[#4E6157] text-[11px]">Sabtu dan Minggu saat surplus acara pesta pernikahan/katering.</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-2xl border border-gray-200 hover:border-[#1E7A4C] flex items-start gap-3 cursor-pointer bg-white transition has-checked:border-[#1E7A4C] has-checked:bg-emerald-50/50">
                        <input type="radio" name="availability" value="Jam Malam (Penutupan Resto)" class="mt-0.5 text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <div class="text-xs">
                            <strong class="text-[#16251D] block font-semibold">Jam Malam</strong>
                            <span class="text-[#4E6157] text-[11px]">Pukul 20:00 - 23:00 WIB saat restoran tutup dan membagikan stok.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- STEP 3: SASARAN PENERIMA MANFAAT & PENGALAMAN -->
        <div class="space-y-4 pt-4 border-t border-[#D5E0D9]">
            <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                <span class="w-7 h-7 rounded-full bg-[#1E7A4C] text-white text-xs font-bold flex items-center justify-center shrink-0">3</span>
                <div>
                    <h2 class="font-display font-bold text-base text-[#16251D]">Sasaran Penyaluran Pangan & Pengalaman</h2>
                    <p class="text-xs text-[#4E6157]">Lembaga atau kelompok sasaran yang biasa/berencana Anda bantu</p>
                </div>
            </div>

            <!-- 3.1 Target Komunitas Penerima Manfaat -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#16251D] block">
                    Sasaran Penerima Manfaat di Kecamatan Terpilih (Pilih yang relevan):
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="target_beneficiaries[]" value="Panti Asuhan & Yatim Piatu" checked class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <span class="font-medium text-[#16251D]">Panti Asuhan & Yatim Piatu</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="target_beneficiaries[]" value="Pondok Pesantren & Santri" checked class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <span class="font-medium text-[#16251D]">Pondok Pesantren & Santri</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="target_beneficiaries[]" value="Tunawisma & Pekerja Jalanan" class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <span class="font-medium text-[#16251D]">Tunawisma & Pekerja Jalanan</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="target_beneficiaries[]" value="Lansia Dhuafa & Keluarga Prasejahtera" class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <span class="font-medium text-[#16251D]">Lansia Dhuafa & Warga Prasejahtera</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition sm:col-span-2">
                        <input type="checkbox" name="target_beneficiaries[]" value="Dapur Umum & Tanggap Darurat Bencana" class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C]">
                        <span class="font-medium text-[#16251D]">Dapur Umum & Posko Tanggap Bencana Lokal</span>
                    </label>
                </div>
            </div>

            <!-- 3.2 Pengalaman Kerelawanan Sebelumnya (Opsional) -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-[#16251D] block">
                    Pengalaman Komunitas / Kerelawanan Sebelumnya <span class="text-[#4E6157] font-normal">(Opsional)</span>
                </label>
                <input type="text"
                       name="experience"
                       value="{{ old('experience') }}"
                       placeholder="Contoh: Pengurus Karang Taruna RW 04, Relawan PMI, atau pegiat sedekah jumat..."
                       class="input-field text-xs">
            </div>
        </div>

        <!-- STEP 4: MOTIVASI & PAKTA KEAMANAN PANGAN -->
        <div class="space-y-4 pt-4 border-t border-[#D5E0D9]">
            <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                <span class="w-7 h-7 rounded-full bg-[#1E7A4C] text-white text-xs font-bold flex items-center justify-center shrink-0">4</span>
                <div>
                    <h2 class="font-display font-bold text-base text-[#16251D]">Motivasi & Pakta Komitmen SOP</h2>
                    <p class="text-xs text-[#4E6157]">Komitmen etika dan jaminan keselamatan pangan untuk masyarakat</p>
                </div>
            </div>

            <!-- 4.1 Motivasi -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-[#16251D] block">
                    Alasan & Rencana Aksi Anda Sebagai Relawan <span class="text-red-500">*</span>
                </label>
                <textarea name="motivation"
                          rows="4"
                          class="input-field text-xs"
                          placeholder="Ceritakan mengapa Anda peduli terhadap penyelamatan makanan berlebih dan bagaimana rencana Anda mengantarkannya ke penerima manfaat..."
                          required>{{ old('motivation') }}</textarea>
                <p class="text-[11px] text-[#4E6157]">Minimal 10 karakter. Isian ini akan ditinjau langsung oleh admin wilayah saat verifikasi akun relawan.</p>
            </div>

            <!-- 4.2 Pakta SOP Keselamatan & Higienitas Pangan -->
            <div class="p-4 rounded-2xl bg-[#F2F7F4] border border-[#1E7A4C]/30 space-y-3">
                <h3 class="font-bold text-xs text-[#14573A] flex items-center gap-1.5 uppercase tracking-wide">
                    <svg class="w-4 h-4 text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Pakta Integritas & Keselamatan Pangan Relawan
                </h3>

                <ul class="text-xs text-[#4E6157] space-y-1.5 pl-5 list-disc">
                    <li>Segera menyalurkan makanan dalam kondisi higienis maksimal 2 jam setelah dijemput dari donor.</li>
                    <li>Memastikan wadah tertutup rapat, bersih, dan terlindung dari sinar matahari langsung / debu jalanan.</li>
                    <li><strong>100% Bebas Biaya:</strong> Dilarang keras memungut biaya transportasi atau biaya apa pun kepada penerima donasi.</li>
                    <li>Bertindak santun dan menjaga nama baik komunitas saat berinteraksi dengan donor maupun penerima.</li>
                </ul>

                <label class="flex items-start gap-2.5 pt-2 border-t border-emerald-200/60 cursor-pointer">
                    <input type="checkbox"
                           name="has_food_safety_commitment"
                           value="1"
                           x-model="hasPledge"
                           class="rounded border-[#74877B] text-[#1E7A4C] focus:ring-[#1E7A4C] w-4 h-4 mt-0.5 shrink-0" required>
                    <span class="text-xs font-bold text-[#14573A]">
                        Saya telah membaca, memahami, dan berikrar mematuhi seluruh SOP Keselamatan Pangan di atas.
                    </span>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit"
                    class="btn-primary w-full text-sm py-3.5 flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition active:scale-[0.99] cursor-pointer"
                    :disabled="!selectedRegency || selectedDistricts.length === 0 || !hasPledge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Kirim Pengajuan Relawan</span>
            </button>
            <p class="text-[11px] text-[#4E6157] text-center mt-2">
                Setiap kecamatan akan ditinjau secara teliti oleh Admin Locabite. Status dapat dipantau di Dashboard Anda.
            </p>
        </div>

    </form>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('volunteerApply', (initialProvinces = []) => ({
        provinces: initialProvinces || [],
        regencies: [],
        districts: [],
        selectedProvince: '',
        selectedProvinceName: '',
        selectedRegency: '',
        selectedRegencyName: '',
        selectedDistricts: [],
        districtNamesMap: {},
        districtSearch: '',
        loadingRegencies: false,
        loadingDistricts: false,
        hasPledge: true,

        get loading() {
            return this.loadingRegencies || this.loadingDistricts;
        },

        get filteredDistricts() {
            if (!this.districtSearch.trim()) return this.districts;
            const q = this.districtSearch.toLowerCase();
            return this.districts.filter(d => d.name.toLowerCase().includes(q));
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
                }
            } catch (e) {
                console.error("Gagal memuat provinsi:", e);
            }
        },

        async onProvinceChange() {
            this.selectedRegency = '';
            this.selectedRegencyName = '';
            this.regencies = [];
            this.districts = [];
            this.selectedDistricts = [];
            this.districtNamesMap = {};

            const p = this.provinces.find(item => String(item.id) === String(this.selectedProvince));
            this.selectedProvinceName = p ? p.name : '';

            if (!this.selectedProvince) return;

            this.loadingRegencies = true;
            try {
                const res = await fetch(`/api/wilayah/regencies/${this.selectedProvince}`);
                if (res.ok) {
                    this.regencies = await res.json();
                }
            } catch (e) {
                console.error("Gagal memuat kabupaten:", e);
            } finally {
                this.loadingRegencies = false;
            }
        },

        async onRegencyChange() {
            this.districts = [];
            this.selectedDistricts = [];
            this.districtNamesMap = {};
            this.districtSearch = '';

            const r = this.regencies.find(item => String(item.id) === String(this.selectedRegency));
            this.selectedRegencyName = r ? r.name : '';

            if (!this.selectedRegency) return;

            this.loadingDistricts = true;
            try {
                const res = await fetch(`/api/wilayah/districts/${this.selectedRegency}`);
                if (res.ok) {
                    this.districts = await res.json();
                    // Pre-fill name mapping
                    this.districts.forEach(d => {
                        this.districtNamesMap[d.id] = d.name;
                    });
                }
            } catch (e) {
                console.error("Gagal memuat kecamatan:", e);
            } finally {
                this.loadingDistricts = false;
            }
        },

        selectAllDistricts() {
            this.selectedDistricts = this.districts.map(d => String(d.id));
        },

        deselectAllDistricts() {
            this.selectedDistricts = [];
        }
    }));
});
</script>
@endpush

</x-layouts.app>
