<x-layouts.app :title="'Pengaturan Website — Admin ' . site_setting('site_name')">

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10"
     x-data="{
         activeTab: 'branding',
         siteName: @js($settings['site_name']),
         siteTagline: @js($settings['site_tagline']),
         logoPreview: @js($logoUrl),
         faviconPreview: @js($faviconUrl),
         announcementEnabled: {{ ($settings['announcement_enabled'] ?? '0') === '1' ? 'true' : 'false' }},
         announcementText: @js($settings['announcement_text']),
         previewFile(e, target) {
             const file = e.target.files[0];
             if (!file) return;
             const reader = new FileReader();
             reader.onload = (ev) => {
                 if (target === 'logo') this.logoPreview = ev.target.result;
                 if (target === 'favicon') this.faviconPreview = ev.target.result;
             };
             reader.readAsDataURL(file);
         }
     }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-[#4E6157] mb-1">
                    <span>Panel Administrasi</span>
                    <span>/</span>
                    <span class="text-[#16251D] font-bold">Pengaturan Website</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-[#16251D]">
                    Pengaturan &amp; Kustomisasi Website
                </h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1">
                    Ubah identitas nama website, logo, konten hero, parameter peta default, aturan klaim, dan pengumuman global secara real-time.
                </p>
            </div>

            <x-admin-nav active="settings" />
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-[#C93A2E] rounded-2xl text-xs text-[#C93A2E] space-y-1">
                <strong class="font-bold block">Mohon periksa input berikut:</strong>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Live Brand Header & Announcement Preview Card -->
        <div class="bg-white rounded-3xl border border-[#D5E0D9] p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#A8DC2E] animate-pulse"></span>
                    <span class="text-xs font-bold text-[#16251D] uppercase tracking-wider">Pratinjau Langsung Navbar &amp; Identitas</span>
                </div>
                <span class="text-[11px] text-[#4E6157]">Perubahan otomatis menyelaraskan seluruh halaman website</span>
            </div>

            <div class="p-4 rounded-2xl bg-[#F2F6F3] border border-[#D5E0D9] space-y-3">
                <template x-if="announcementEnabled && announcementText.trim().length > 0">
                    <div class="px-4 py-2 rounded-xl bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] text-xs font-bold text-center" x-text="announcementText"></div>
                </template>

                <div class="flex items-center justify-between bg-white/85 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-white border border-[#D5E0D9] flex items-center justify-center p-1 shadow-2xs overflow-hidden">
                            <template x-if="logoPreview">
                                <img :src="logoPreview" alt="Pratinjau Logo" class="w-full h-full object-contain rounded-xl">
                            </template>
                            <template x-if="!logoPreview">
                                <x-brand-logo size="sm" :interactive="false" />
                            </template>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-display font-extrabold text-xl tracking-tight text-[#16251D] leading-none lowercase" x-text="(siteName || 'lokawaste').toLowerCase()"></span>
                            <span class="text-[9px] uppercase tracking-wider text-[#4E6157] font-bold mt-0.5" x-text="siteTagline || 'Penyelamat Pangan'"></span>
                        </div>
                    </div>

                    <div class="hidden sm:flex items-center gap-2 text-[11px] font-semibold text-[#4E6157]">
                        <span class="px-3 py-1 rounded-full bg-[#D7F57C] text-[#16251D] font-bold">Peta</span>
                        <span class="px-3 py-1">Katalog Makanan</span>
                        <span class="px-3 py-1">Galang</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Switcher Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <button type="button" @click="activeTab = 'branding'"
                    :class="activeTab === 'branding' ? 'bg-[#16251D] text-white shadow-xs' : 'bg-white text-[#4E6157] hover:text-[#16251D] border border-[#D5E0D9]'"
                    class="h-9 px-4 rounded-full text-xs font-bold transition shrink-0 cursor-pointer">
                1. Identitas &amp; Logo
            </button>
            <button type="button" @click="activeTab = 'hero'"
                    :class="activeTab === 'hero' ? 'bg-[#16251D] text-white shadow-xs' : 'bg-white text-[#4E6157] hover:text-[#16251D] border border-[#D5E0D9]'"
                    class="h-9 px-4 rounded-full text-xs font-bold transition shrink-0 cursor-pointer">
                2. Peta, Beranda &amp; Teks Hero
            </button>
            <button type="button" @click="activeTab = 'operations'"
                    :class="activeTab === 'operations' ? 'bg-[#16251D] text-white shadow-xs' : 'bg-white text-[#4E6157] hover:text-[#16251D] border border-[#D5E0D9]'"
                    class="h-9 px-4 rounded-full text-xs font-bold transition shrink-0 cursor-pointer">
                3. Aturan Operasional &amp; Dampak ESG
            </button>
            <button type="button" @click="activeTab = 'contact'"
                    :class="activeTab === 'contact' ? 'bg-[#16251D] text-white shadow-xs' : 'bg-white text-[#4E6157] hover:text-[#16251D] border border-[#D5E0D9]'"
                    class="h-9 px-4 rounded-full text-xs font-bold transition shrink-0 cursor-pointer">
                4. Kontak &amp; Pengumuman Global
            </button>
        </div>

        <!-- Main Unified Settings Form -->
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- TAB 1: BRANDING & LOGO -->
            <div x-show="activeTab === 'branding'" class="bg-white rounded-3xl border border-[#D5E0D9] p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="font-display font-bold text-lg text-[#16251D]">Identitas &amp; Logo Website</h2>
                    <p class="text-xs text-[#4E6157] mt-0.5">Ubah nama merek, tagline, deskripsi SEO, logo navbar/autentikasi, dan favicon tab peramban.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Nama Website <span class="text-red-500">*</span></label>
                        <input type="text" name="site_name" x-model="siteName" value="{{ old('site_name', $settings['site_name']) }}" class="input-field text-sm font-bold" required>
                        <span class="text-[11px] text-[#4E6157] mt-1 block">Ditampilkan pada navbar, judul halaman, halaman masuk/daftar, dan halaman error.</span>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Tagline Singkat</label>
                        <input type="text" name="site_tagline" x-model="siteTagline" value="{{ old('site_tagline', $settings['site_tagline']) }}" class="input-field text-sm" placeholder="Penyelamat Pangan">
                        <span class="text-[11px] text-[#4E6157] mt-1 block">Subjudul kecil di bawah nama website pada navbar desktop.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Deskripsi Meta SEO Website</label>
                        <textarea name="site_description" rows="2" class="input-field text-xs">{{ old('site_description', $settings['site_description']) }}</textarea>
                    </div>
                </div>

                <!-- Upload Logo & Favicon -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                    <div class="p-4 rounded-2xl bg-[#F8FAF8] border border-gray-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-[#16251D]">Logo Utama Website</span>
                            @if($logoUrl)
                                <button type="submit" form="reset-logo-form" class="text-[11px] font-bold text-[#C93A2E] hover:underline cursor-pointer">
                                    Hapus &amp; Pakai Logo Bawaan
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-white border border-[#D5E0D9] flex items-center justify-center p-1.5 shrink-0 shadow-2xs overflow-hidden">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" alt="Logo" class="w-full h-full object-contain rounded-xl">
                                </template>
                                <template x-if="!logoPreview">
                                    <x-brand-logo size="md" :interactive="false" />
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <input type="file" name="site_logo" accept="image/png,image/jpeg,image/webp" @change="previewFile($event, 'logo')" class="text-xs w-full file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#D7F57C] file:text-[#16251D] hover:file:bg-[#C8EE63] cursor-pointer">
                                <span class="text-[10px] text-[#4E6157] block mt-1.5">Format PNG, JPG, atau WebP (Maks. 2 MB). Kosongkan untuk tetap memakai logo GPS dinamis.</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F8FAF8] border border-gray-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-[#16251D]">Favicon Tab Browser (Opsional)</span>
                            @if($faviconUrl)
                                <button type="submit" form="reset-favicon-form" class="text-[11px] font-bold text-[#C93A2E] hover:underline cursor-pointer">
                                    Kembalikan Favicon Bawaan
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-white border border-[#D5E0D9] flex items-center justify-center p-2 shrink-0 shadow-2xs overflow-hidden">
                                <template x-if="faviconPreview">
                                    <img :src="faviconPreview" alt="Favicon" class="w-10 h-10 object-contain rounded">
                                </template>
                                <template x-if="!faviconPreview">
                                    <span class="text-[10px] font-bold text-[#4E6157] text-center">Ikon GPS Bawaan</span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <input type="file" name="site_favicon" accept="image/png,image/jpeg,image/webp" @change="previewFile($event, 'favicon')" class="text-xs w-full file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white file:text-[#16251D] hover:file:bg-gray-100 cursor-pointer">
                                <span class="text-[10px] text-[#4E6157] block mt-1.5">Ikon persegi ukuran kecil (mis. 64×64 px, PNG/WebP, Maks. 1 MB).</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PETA, BERANDA & HERO -->
            <div x-show="activeTab === 'hero'" style="display: none;" class="bg-white rounded-3xl border border-[#D5E0D9] p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="font-display font-bold text-lg text-[#16251D]">Konfigurasi Peta, Beranda &amp; Teks Hero</h2>
                    <p class="text-xs text-[#4E6157] mt-0.5">Sesuaikan titik tengah awal peta (kota/wilayah operasi utama), radius default, serta judul halaman publik.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Judul Tab Halaman Utama</label>
                        <input type="text" name="home_title" value="{{ old('home_title', $settings['home_title']) }}" class="input-field text-sm" required>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Judul Sidebar Daftar Peta</label>
                        <input type="text" name="home_sidebar_title" value="{{ old('home_sidebar_title', $settings['home_sidebar_title']) }}" class="input-field text-sm" required>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Placeholder Kolom Pencarian Peta</label>
                        <input type="text" name="home_search_placeholder" value="{{ old('home_search_placeholder', $settings['home_search_placeholder']) }}" class="input-field text-sm" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Latitude Pusat Peta Default</label>
                        <input type="number" step="0.00001" name="default_map_lat" value="{{ old('default_map_lat', $settings['default_map_lat']) }}" class="input-field text-sm font-mono" required>
                    </div>
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Longitude Pusat Peta Default</label>
                        <input type="number" step="0.00001" name="default_map_lng" value="{{ old('default_map_lng', $settings['default_map_lng']) }}" class="input-field text-sm font-mono" required>
                    </div>
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Radius Pencarian Default</label>
                        <select name="default_map_radius_km" class="input-field text-sm">
                            @foreach(['1' => '1 km', '5' => '5 km', '10' => '10 km', '20' => '20 km'] as $val => $label)
                                <option value="{{ $val }}" {{ (string) old('default_map_radius_km', $settings['default_map_radius_km']) === (string) $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                    <div class="space-y-3">
                        <label class="font-bold text-xs text-[#16251D] block">Judul Hero Katalog Makanan</label>
                        <input type="text" name="catalog_hero_title" value="{{ old('catalog_hero_title', $settings['catalog_hero_title']) }}" class="input-field text-sm" required>
                        <label class="font-bold text-xs text-[#16251D] block">Subjudul Katalog</label>
                        <textarea name="catalog_hero_subtitle" rows="2" class="input-field text-xs">{{ old('catalog_hero_subtitle', $settings['catalog_hero_subtitle']) }}</textarea>
                    </div>

                    <div class="space-y-3">
                        <label class="font-bold text-xs text-[#16251D] block">Judul Hero Galang Pangan</label>
                        <input type="text" name="campaigns_hero_title" value="{{ old('campaigns_hero_title', $settings['campaigns_hero_title']) }}" class="input-field text-sm" required>
                        <label class="font-bold text-xs text-[#16251D] block">Subjudul Galang Pangan</label>
                        <textarea name="campaigns_hero_subtitle" rows="2" class="input-field text-xs">{{ old('campaigns_hero_subtitle', $settings['campaigns_hero_subtitle']) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- TAB 3: ATURAN OPERASIONAL & ESG -->
            <div x-show="activeTab === 'operations'" style="display: none;" class="bg-white rounded-3xl border border-[#D5E0D9] p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="font-display font-bold text-lg text-[#16251D]">Aturan Operasional &amp; Kalkulasi Dampak ESG</h2>
                    <p class="text-xs text-[#4E6157] mt-0.5">Nilai batas sistem anti-penimbunan dan koefisien perhitungan dampak lingkungan pada Dashboard.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Maks. Klaim Aktif / Akun</label>
                        <input type="number" name="max_active_claims_per_user" min="1" max="20" value="{{ old('max_active_claims_per_user', $settings['max_active_claims_per_user']) }}" class="input-field text-sm font-bold" required>
                        <span class="text-[11px] text-[#4E6157] mt-1 block">Batas klaim pending/approved bersamaan per pengguna.</span>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Ambang Batas No-Show (30 Hari)</label>
                        <input type="number" name="no_show_penalty_threshold" min="1" max="20" value="{{ old('no_show_penalty_threshold', $settings['no_show_penalty_threshold']) }}" class="input-field text-sm font-bold" required>
                        <span class="text-[11px] text-[#4E6157] mt-1 block">Jumlah gagal jemput sebelum akun dibatasi otomatis.</span>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Durasi Pembatasan Klaim (Hari)</label>
                        <input type="number" name="no_show_restriction_days" min="1" max="90" value="{{ old('no_show_restriction_days', $settings['no_show_restriction_days']) }}" class="input-field text-sm font-bold" required>
                        <span class="text-[11px] text-[#4E6157] mt-1 block">Lama masa pembekuan klaim ketika melewati batas no-show.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-4 border-t border-gray-100">
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Emisi CO₂e / Porsi (kg)</label>
                        <input type="number" step="0.01" name="esg_co2_per_portion" min="0" value="{{ old('esg_co2_per_portion', $settings['esg_co2_per_portion']) }}" class="input-field text-sm font-bold" required>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Air Bersih Terhemat / Porsi (Liter)</label>
                        <input type="number" name="esg_water_per_portion" min="0" value="{{ old('esg_water_per_portion', $settings['esg_water_per_portion']) }}" class="input-field text-sm font-bold" required>
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Nilai Ekonomi / Porsi (Rp)</label>
                        <input type="number" name="esg_value_per_portion" min="0" value="{{ old('esg_value_per_portion', $settings['esg_value_per_portion']) }}" class="input-field text-sm font-bold" required>
                    </div>
                </div>
            </div>

            <!-- TAB 4: KONTAK & PENGUMUMAN GLOBAL -->
            <div x-show="activeTab === 'contact'" style="display: none;" class="bg-white rounded-3xl border border-[#D5E0D9] p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="font-display font-bold text-lg text-[#16251D]">Kontak Resmi &amp; Banner Pengumuman Global</h2>
                    <p class="text-xs text-[#4E6157] mt-0.5">Informasi kontak komunitas dan spanduk pengumuman darurat/info penting di seluruh halaman.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Email Dukungan / Resmi</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" class="input-field text-sm" placeholder="halo@lokawaste.id">
                    </div>

                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Nomor WhatsApp Admin</label>
                        <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp']) }}" class="input-field text-sm" placeholder="6281234567890">
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-[#F8FAF8] border border-gray-200 space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="announcement_enabled" value="1" x-model="announcementEnabled" class="w-4 h-4 rounded border-[#74877B] text-[#16251D] focus:ring-[#A8DC2E]">
                        <div>
                            <span class="font-bold text-xs text-[#16251D] block">Tampilkan Banner Pengumuman Global</span>
                            <span class="text-[11px] text-[#4E6157]">Jika aktif, banner akan muncul di bagian atas halaman untuk seluruh pengunjung.</span>
                        </div>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-bold text-xs text-[#16251D] block mb-1">Teks Pengumuman</label>
                            <input type="text" name="announcement_text" x-model="announcementText" value="{{ old('announcement_text', $settings['announcement_text']) }}" class="input-field text-xs" placeholder="Contoh: Dibuka pendaftaran relawan untuk Kecamatan Banyumanik!">
                        </div>
                        <div>
                            <label class="font-bold text-xs text-[#16251D] block mb-1">Tautan CTA Pengumuman (Opsional)</label>
                            <input type="url" name="announcement_url" value="{{ old('announcement_url', $settings['announcement_url']) }}" class="input-field text-xs" placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Toolbar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-3xl border border-[#D5E0D9] p-4 sm:p-5 shadow-xs">
                <button type="submit" form="reset-all-form"
                        class="text-xs font-bold text-[#C93A2E] hover:underline cursor-pointer order-2 sm:order-1">
                    Kembalikan Semua ke Setelan Awal
                </button>

                <button type="submit" class="btn-primary text-sm px-7 py-3 w-full sm:w-auto order-1 sm:order-2 shadow-sm">
                    Simpan Seluruh Pengaturan
                </button>
            </div>
        </form>

        <!-- Hidden Auxiliary Reset Forms -->
        <form id="reset-logo-form" method="POST" action="{{ route('admin.settings.reset-logo') }}" class="hidden">
            @csrf
            <input type="hidden" name="target" value="logo">
        </form>
        <form id="reset-favicon-form" method="POST" action="{{ route('admin.settings.reset-logo') }}" class="hidden">
            @csrf
            <input type="hidden" name="target" value="favicon">
        </form>
        <form id="reset-all-form" method="POST" action="{{ route('admin.settings.reset-all') }}" onsubmit="return confirm('Kembalikan seluruh pengaturan website ke setelan awal? Logo custom akan dihapus.')" class="hidden">
            @csrf
        </form>

    </div>
</div>

</x-layouts.app>
