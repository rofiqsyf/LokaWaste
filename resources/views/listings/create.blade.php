<x-layouts.app title="Bagikan Makanan — LokaWaste">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8" x-data="createListingForm()">

    <!-- Header & Breadcrumb -->
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#D5E0D9]/80 pb-6">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4E6157] hover:text-[#16251D] mb-2 group transition">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Peta</span>
            </a>
            <h1 class="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-[#16251D] tracking-tight">Bagikan Makanan Berlebih</h1>
            <p class="text-xs sm:text-sm text-[#4E6157] mt-1 max-w-xl">Salurkan makanan surplus Anda ke warga dan komunitas terdekat. Cepat, aman, dan privasi alamat terlindungi.</p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
            <div class="px-3 py-1.5 rounded-full bg-[#D7F57C]/40 border border-[#C8EE63] text-[#16251D] flex items-center gap-1.5 shadow-2xs text-xs font-bold">
                <svg class="w-3.5 h-3.5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>100% Bebas Biaya</span>
            </div>
            <div class="px-3 py-1.5 rounded-full bg-white border border-[#D5E0D9] text-[#4E6157] flex items-center gap-1.5 shadow-2xs text-xs font-medium">
                <svg class="w-3.5 h-3.5 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Privasi Alamat Terlindungi</span>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="p-4 sm:p-5 bg-red-50 border border-[#C93A2E] rounded-3xl text-xs text-[#C93A2E] space-y-1.5 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4 h-4 shrink-0 text-[#C93A2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                    </svg>
                    <span>Mohon perbaiki data berikut sebelum menyimpan:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-6 font-medium">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Core Details (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- CARD 1: Identitas Makanan -->
                <div class="glass-card rounded-3xl shadow-card p-5 sm:p-7 space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#D7F57C] text-[#16251D] text-xs font-black flex items-center justify-center shrink-0 border border-[#C8EE63]">1</span>
                            <h2 class="font-display font-bold text-base text-[#16251D]">Identitas Makanan</h2>
                        </div>
                        <span class="text-[11px] font-semibold text-[#4E6157]">Langkah 1 dari 3</span>
                    </div>

                    <!-- Photo Upload Dropzone -->
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-2">
                            Foto Makanan <span class="text-[#4E6157] font-normal">(Opsional, sangat disarankan)</span>
                        </label>
                        <div class="relative">
                            <input type="file" name="photo" id="photo-input" accept="image/*" @change="previewImage($event)" class="sr-only">
                            
                            <template x-if="photoPreview">
                                <div class="relative rounded-2xl overflow-hidden border border-[#D5E0D9] bg-gray-50 max-h-56 group">
                                    <img :src="photoPreview" class="w-full h-48 sm:h-56 object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <label for="photo-input" class="px-3 py-1.5 rounded-xl bg-white text-[#16251D] text-xs font-bold cursor-pointer hover:bg-gray-100 transition shadow-sm">
                                            Ganti Foto
                                        </label>
                                        <button type="button" @click="removePhoto()" class="px-3 py-1.5 rounded-xl bg-[#C93A2E] text-white text-xs font-bold hover:bg-red-700 transition shadow-sm">
                                            Hapus
                                        </button>
                                    </div>
                                    <div class="absolute bottom-2 left-2 px-2.5 py-1 rounded-lg bg-black/70 backdrop-blur-xs text-[10px] font-semibold text-white flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-[#D7F57C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Metadata GPS otomatis dibersihkan</span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!photoPreview">
                                <label for="photo-input" class="cursor-pointer border-2 border-dashed border-[#D5E0D9] hover:border-[#A8DC2E] rounded-2xl p-5 sm:p-6 transition-all duration-200 bg-gray-50/60 hover:bg-[#D7F57C]/15 flex flex-col items-center justify-center text-center group">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-[#D5E0D9] shadow-2xs text-[#4E6157] group-hover:text-[#16251D] group-hover:border-[#A8DC2E] flex items-center justify-center transition mb-2.5">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold text-[#16251D] group-hover:text-[#16251D] transition">Ketuk untuk unggah foto makanan</span>
                                    <span class="text-[11px] text-[#4E6157] mt-0.5">Format JPG, PNG, atau WebP (Maks. 2 MB)</span>
                                    <div class="mt-2.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white border border-gray-200 text-[10px] font-semibold text-[#4E6157]">
                                        <svg class="w-3 h-3 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span>EXIF GPS otomatis dihapus demi privasi rumah</span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Food Title -->
                    <div>
                        <label class="font-bold text-xs text-[#16251D] flex items-center justify-between mb-1.5">
                            <span>Nama Makanan <span class="text-red-500">*</span></span>
                            <span class="text-[11px] font-normal text-[#4E6157]">Sebutkan jumlah & menu lengkap</span>
                        </label>
                        <input type="text"
                               name="title"
                               x-model="title"
                               placeholder="Contoh: 15 Kotak Nasi Ayam Bakar Madu Komplit"
                               class="input-field text-sm font-medium placeholder:text-[#4E6157]/50"
                               required>
                    </div>

                    <!-- Food Type Segmented Cards -->
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-2">Jenis Pangan <span class="text-red-500">*</span></label>
                        <input type="hidden" name="food_type" :value="foodType">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <!-- Cooked (Siap Santap) -->
                            <div @click="foodType = 'cooked'"
                                 :class="foodType === 'cooked' ? 'border-[#A8DC2E] bg-[#D7F57C]/30 ring-2 ring-[#A8DC2E]/30 text-[#16251D] font-bold shadow-xs' : 'border-gray-200 bg-white hover:bg-[#D7F57C]/10 hover:border-gray-300 text-[#4E6157]'"
                                 class="p-3 rounded-2xl border cursor-pointer transition-all duration-200 flex sm:flex-col items-center sm:text-center gap-2.5 group">
                                <div class="w-8 h-8 rounded-xl bg-white border border-[#D5E0D9] flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                    <!-- Spoon & Fork (Sendok & Garpu Makanan Masak) -->
                                    <svg class="w-4.5 h-4.5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 2v5a2 2 0 002 2 2 2 0 002-2V2m-2 7v13M6 2v3m4-3v3M16 2a3 3 0 00-3 3v2a3 3 0 006 0V5a3 3 0 00-3-3zm0 7v13"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-xs text-[#16251D]">Siap Santap</div>
                                    <div class="text-[10px] text-[#4E6157] leading-tight mt-0.5">Makanan masak dapur / katering</div>
                                </div>
                            </div>

                            <!-- Packaged (Kemasan Pangan) -->
                            <div @click="foodType = 'packaged'"
                                 :class="foodType === 'packaged' ? 'border-[#A8DC2E] bg-[#D7F57C]/30 ring-2 ring-[#A8DC2E]/30 text-[#16251D] font-bold shadow-xs' : 'border-gray-200 bg-white hover:bg-[#D7F57C]/10 hover:border-gray-300 text-[#4E6157]'"
                                 class="p-3 rounded-2xl border cursor-pointer transition-all duration-200 flex sm:flex-col items-center sm:text-center gap-2.5 group">
                                <div class="w-8 h-8 rounded-xl bg-white border border-[#D5E0D9] flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                    <!-- Canned Food / Hermetic Food Package (Kaleng & Kemasan Pangan) -->
                                    <svg class="w-4.5 h-4.5 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <ellipse cx="12" cy="5.5" rx="6.5" ry="2.5" stroke-width="2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 5.5v13c0 1.38 2.91 2.5 6.5 2.5s6.5-1.12 6.5-2.5v-13"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 11.5c0 1.38 2.91 2.5 6.5 2.5s6.5-1.12 6.5-2.5"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-xs text-[#16251D]">Kemasan</div>
                                    <div class="text-[10px] text-[#4E6157] leading-tight mt-0.5">Roti, kaleng, biskuit, snack</div>
                                </div>
                            </div>

                            <!-- Raw (Bahan Mentah / Hasil Tani & Sayuran) -->
                            <div @click="foodType = 'raw'"
                                 :class="foodType === 'raw' ? 'border-[#A8DC2E] bg-[#D7F57C]/30 ring-2 ring-[#A8DC2E]/30 text-[#16251D] font-bold shadow-xs' : 'border-gray-200 bg-white hover:bg-[#D7F57C]/10 hover:border-gray-300 text-[#4E6157]'"
                                 class="p-3 rounded-2xl border cursor-pointer transition-all duration-200 flex sm:flex-col items-center sm:text-center gap-2.5 group">
                                <div class="w-8 h-8 rounded-xl bg-white border border-[#D5E0D9] flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                    <!-- Fresh Carrot / Vegetables (Wortel & Sayuran Segar) -->
                                    <svg class="w-4.5 h-4.5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.27 21.7s9.87-3.5 12.73-6.36a4.5 4.5 0 0 0-6.36-6.37C5.77 11.84 2.27 21.7 2.27 21.7z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l6-6M17 3l4 4M13 7l4 4"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-xs text-[#16251D]">Bahan Mentah</div>
                                    <div class="text-[10px] text-[#4E6157] leading-tight mt-0.5">Sayuran, buah segar, beras</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Selector -->
                    <div>
                        <label class="font-bold text-xs text-[#16251D] block mb-1.5">Kategori Makanan <span class="text-red-500">*</span></label>
                        <select name="category_id" x-model="categoryId" class="input-field text-sm" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $duplicate->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- CARD 2: Kuantitas & Masa Layak Konsumsi -->
                <div class="glass-card rounded-3xl shadow-card p-5 sm:p-7 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#D7F57C] text-[#16251D] text-xs font-black flex items-center justify-center shrink-0 border border-[#C8EE63]">2</span>
                            <h2 class="font-display font-bold text-base text-[#16251D]">Kuantitas & Masa Layak</h2>
                        </div>
                        <span class="text-[11px] font-semibold text-[#4E6157]">Langkah 2 dari 3</span>
                    </div>

                    <!-- Portions Stepper with Quick Presets -->
                    <div>
                        <label class="font-bold text-xs text-[#16251D] flex items-center justify-between mb-2">
                            <span>Jumlah Porsi Tersedia <span class="text-red-500">*</span></span>
                            <span class="text-[11px] font-semibold text-[#16251D] bg-[#D7F57C]/40 px-2.5 py-0.5 rounded-full border border-[#C8EE63] font-bold" x-text="`Cukup untuk ~${portions} orang`"></span>
                        </label>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <!-- Tactile Stepper Box -->
                            <div class="inline-flex items-center bg-gray-50 p-1.5 rounded-2xl border border-gray-200 shadow-2xs">
                                <button type="button"
                                        @click="portions = Math.max(1, portions - 1)"
                                        class="w-12 h-12 rounded-xl bg-white hover:bg-[#D7F57C]/30 active:scale-95 text-[#16251D] hover:text-[#16251D] font-bold text-xl flex items-center justify-center transition shadow-2xs"
                                        title="Kurangi porsi">
                                    -
                                </button>
                                <div class="px-4 text-center">
                                    <input type="number"
                                           name="portions_total"
                                           x-model="portions"
                                           min="1"
                                           max="500"
                                           class="w-20 text-center font-display font-black text-2xl text-[#16251D] bg-transparent border-0 focus:ring-0 p-0"
                                           required>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#4E6157] block -mt-1">Porsi</span>
                                </div>
                                <button type="button"
                                        @click="portions = portions + 1"
                                        class="w-12 h-12 rounded-xl bg-white hover:bg-[#D7F57C]/30 active:scale-95 text-[#16251D] hover:text-[#16251D] font-bold text-xl flex items-center justify-center transition shadow-2xs"
                                        title="Tambah porsi">
                                    +
                                </button>
                            </div>

                            <!-- Quick Preset Portion Chips -->
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <template x-for="p in [5, 10, 20, 50]" :key="p">
                                    <button type="button"
                                            @click="portions = p; if (customMaxPortions > portions) customMaxPortions = portions;"
                                            :class="portions === p ? 'bg-[#D7F57C] text-[#16251D] font-bold border-[#C8EE63] shadow-xs' : 'bg-gray-100 hover:bg-[#D7F57C]/20 text-[#4E6157] border-gray-200'"
                                            class="px-3 py-2 rounded-xl text-xs font-semibold border transition active:scale-95"
                                            x-text="`${p} porsi`">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Max Portions Per Claim Setting (SD 3.7 Rule) -->
                    <div class="pt-4 border-t border-gray-100">
                        <label class="font-bold text-xs text-[#16251D] flex items-center justify-between mb-2">
                            <span>Batas Ajuan Porsi per Pengambil</span>
                            <span class="text-[11px] font-semibold text-[#16251D] bg-[#D7F57C]/40 px-2.5 py-0.5 rounded-full border border-[#C8EE63] font-bold"
                                  x-text="hasPortionLimit ? `Maks. ${customMaxPortions} porsi / akun` : 'Bebas (Tanpa Batas)'"></span>
                        </label>

                        <!-- Segmented Switcher: Bebas vs Batasi -->
                        <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-2xl border border-gray-200">
                            <button type="button"
                                    @click="hasPortionLimit = false"
                                    :class="!hasPortionLimit ? 'bg-white text-[#16251D] font-bold shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] font-medium'"
                                    class="py-2 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" :class="!hasPortionLimit ? 'text-[#16251D]' : 'text-[#4E6157]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Bebas (Tanpa Batas)</span>
                            </button>
                            <button type="button"
                                    @click="hasPortionLimit = true; if (!customMaxPortions || customMaxPortions > portions) customMaxPortions = Math.min(2, portions);"
                                    :class="hasPortionLimit ? 'bg-[#D7F57C] text-[#16251D] font-bold shadow-xs border border-[#C8EE63]' : 'text-[#4E6157] hover:text-[#16251D] font-medium'"
                                    class="py-2 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" :class="hasPortionLimit ? 'text-[#16251D]' : 'text-[#4E6157]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Batasi per Pengambil</span>
                            </button>
                        </div>

                        <!-- Limit Details (Visible when hasPortionLimit is true) -->
                        <div x-show="hasPortionLimit" x-transition class="mt-3 p-3.5 bg-[#D7F57C]/20 rounded-2xl border border-[#C8EE63]/70 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold text-[#16251D] block">Maksimal Porsi per Permintaan:</span>
                                    <span class="text-[11px] text-[#4E6157] block">Berapa porsi yang boleh diajukan oleh satu orang penerima?</span>
                                </div>
                                <div class="inline-flex items-center bg-white p-1 rounded-xl border border-gray-200 shadow-2xs self-start sm:self-auto">
                                    <button type="button"
                                            @click="customMaxPortions = Math.max(1, customMaxPortions - 1)"
                                            class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 active:scale-95 text-[#16251D] font-bold text-base flex items-center justify-center transition cursor-pointer"
                                            title="Kurangi batas porsi">
                                        -
                                    </button>
                                    <input type="number"
                                           x-model.number="customMaxPortions"
                                           min="1"
                                           :max="portions"
                                           @input="if (customMaxPortions > portions) customMaxPortions = portions; if (customMaxPortions < 1) customMaxPortions = 1"
                                           class="w-14 text-center font-bold text-base text-[#16251D] bg-transparent border-0 focus:ring-0 p-0">
                                    <button type="button"
                                            @click="customMaxPortions = Math.min(portions, customMaxPortions + 1)"
                                            class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 active:scale-95 text-[#16251D] font-bold text-base flex items-center justify-center transition cursor-pointer"
                                            title="Tambah batas porsi">
                                        +
                                    </button>
                                </div>
                            </div>

                            <p class="text-[11px] text-[#4E6157] leading-relaxed pt-2 border-t border-[#C8EE63]/60">
                                Dengan batas <strong class="text-[#16251D]" x-text="`${customMaxPortions} porsi/orang`"></strong>, total <strong class="text-[#16251D]" x-text="`${portions} porsi`"></strong> ini akan menjangkau minimal <strong class="text-[#16251D]" x-text="`~${Math.ceil(portions / Math.max(1, customMaxPortions))} orang`"></strong> penerima berbeda.
                            </p>
                        </div>

                        <!-- Explanatory text for Unlimited -->
                        <div x-show="!hasPortionLimit" x-transition class="mt-2 text-[11px] text-[#4E6157] flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#16251D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Pemohon bebas meminta jumlah porsi berapa saja sesuai sisa stok tersedia (hingga <span class="font-bold text-[#16251D]" x-text="`${portions} porsi`"></span>).</span>
                        </div>

                        <input type="hidden" name="max_portions_per_claim" :value="hasPortionLimit ? customMaxPortions : portions">
                    </div>

                    <!-- Expiration Time Selector (Full Custom Clock Time) -->
                    <div class="pt-4 border-t border-gray-100 space-y-3">
                        <label class="font-bold text-xs text-[#16251D] flex items-center justify-between">
                            <span>Batas Waktu Pengambilan <span class="text-red-500">*</span></span>
                            <span class="text-[11px] font-semibold text-[#16251D]">Jam / Pukul Pengambilan</span>
                        </label>

                        <!-- Custom Clock Time Input Field -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#4E6157]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <input type="time"
                                       x-model="clockTime"
                                       @change="onClockTimeChange()"
                                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 hover:bg-white focus:bg-white rounded-xl border border-gray-200 focus:border-[#A8DC2E] focus:ring-2 focus:ring-[#D7F57C]/40 font-bold text-sm text-[#16251D] transition cursor-pointer">
                            </div>
                            <span class="text-xs font-bold text-[#4E6157] px-3 py-2 rounded-xl bg-gray-100 shrink-0" x-text="clockIsTomorrow ? 'Besok Hari' : 'Hari Ini'"></span>
                        </div>

                        <!-- Hidden Inputs for Form Submission -->
                        <input type="hidden" name="hours_valid" :value="hours">
                        <input type="hidden" name="expires_time" :value="clockTime">

                        <!-- Live Expiration Deadline Indicator -->
                        <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex items-center justify-between gap-3 text-xs text-amber-900">
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="truncate">Batas klaim: <strong class="font-black text-[#16251D]" x-text="deadlineFormatted"></strong></span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white text-amber-800 border border-amber-300 shrink-0 shadow-2xs">Otomatis Ditutup</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Jaminan Keamanan Pangan & Halal -->
                <div class="glass-card rounded-3xl shadow-card p-5 sm:p-7 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#D7F57C] text-[#16251D] text-xs font-black flex items-center justify-center shrink-0 border border-[#C8EE63]">3</span>
                            <h2 class="font-display font-bold text-base text-[#16251D]">Keamanan & Sertifikasi</h2>
                        </div>
                        <span class="text-[11px] font-semibold text-[#4E6157]">Langkah 3 dari 3</span>
                    </div>

                    <!-- Halal Card Toggle -->
                    <label class="p-3.5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-start gap-3"
                           :class="halal ? 'border-[#C8EE63] bg-[#D7F57C]/25 ring-2 ring-[#C8EE63]/40' : 'border-gray-200 hover:bg-gray-50'">
                        <input type="checkbox" name="halal_claim" value="1" x-model="halal" class="mt-1 w-4 h-4 rounded text-[#16251D] accent-[#16251D] focus:ring-[#A8DC2E] border-gray-300">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-[#16251D]">Saya menyatakan makanan ini Halal</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">Halal</span>
                            </div>
                            <p class="text-[11px] text-[#4E6157] mt-0.5">Bahan dan proses pengolahan bebas dari unsur non-halal sesuai syariat Islam.</p>
                        </div>
                    </label>

                    <!-- Vegetarian Card Toggle -->
                    <label class="p-3.5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-start gap-3"
                           :class="vegetarian ? 'border-[#C8EE63] bg-[#D7F57C]/25 ring-2 ring-[#C8EE63]/40' : 'border-gray-200 hover:bg-gray-50'">
                        <input type="checkbox" name="is_vegetarian" value="1" x-model="vegetarian" class="mt-1 w-4 h-4 rounded text-[#16251D] accent-[#16251D] focus:ring-[#A8DC2E] border-gray-300">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-[#16251D]">Makanan Vegetarian</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">Vegetarian</span>
                            </div>
                            <p class="text-[11px] text-[#4E6157] mt-0.5">Bebas dari daging mamalia, unggas, ikan, dan produk laut.</p>
                        </div>
                    </label>
                </div>

                <!-- CARD 4: Opsi Lanjutan (Alergen, Galang, Batas Klaim) -->
                <div x-data="{ open: false }" class="glass-card rounded-3xl shadow-card overflow-hidden">
                    <button type="button"
                            @click="open = !open"
                            class="p-5 sm:p-6 w-full flex items-center justify-between text-left hover:bg-gray-50/70 transition">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gray-100 text-[#4E6157] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-sm text-[#16251D]">Opsi Tambahan & Preferensi</h3>
                                <p class="text-[11px] text-[#4E6157]">Informasi alergen, petunjuk simpan, galang makanan panti, & batas klaim</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-[#16251D] flex items-center gap-1">
                            <span x-text="open ? 'Sembunyikan' : 'Buka Opsi'"></span>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>

                    <div x-show="open" x-collapse class="p-5 sm:p-6 pt-0 border-t border-gray-100 space-y-5">
                        <!-- Storage Note -->
                        <div>
                            <label class="font-bold text-xs text-[#16251D] block mb-1.5">Petunjuk Penyimpanan (Opsional)</label>
                            <input type="text"
                                   name="storage_note"
                                   placeholder="Misal: Simpan dalam suhu ruang, segera panaskan sebelum disantap"
                                   class="input-field text-xs">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="font-bold text-xs text-[#16251D] block mb-1.5">Deskripsi Tambahan</label>
                            <textarea name="description"
                                      rows="2"
                                      placeholder="Keterangan tambahan untuk pemohon (kemasan bawa sendiri, sendok garpu, dll.)"
                                      class="input-field text-xs"></textarea>
                        </div>


                        <!-- Campaign Priority Selector -->
                        <div>
                            <label class="font-bold text-xs text-[#16251D] block mb-1">Tujukan untuk Galang Panti</label>
                            <select name="campaign_id" class="input-field text-xs">
                                <option value="">-- Terbuka untuk Umum --</option>
                                @foreach($campaigns as $camp)
                                    <option value="{{ $camp->id }}">Galang: {{ $camp->title }} ({{ $camp->beneficiary_name }})</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-[#4E6157] mt-1 block">Diprioritaskan untuk relawan galang selama 2 jam pertama.</span>
                        </div>

                        <!-- Allergen Chips -->
                        <div>
                            <label class="font-bold text-xs text-[#16251D] block mb-2">Informasi Alergen</label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @foreach(['Telur', 'Susu', 'Kacang', 'Gluten', 'Seafood', 'Kedelai'] as $allergen)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="allergens[]" value="{{ $allergen }}" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-semibold text-[#4E6157] peer-checked:bg-amber-50 peer-checked:text-amber-900 peer-checked:border-amber-300 peer-checked:font-bold transition-all inline-flex items-center gap-1.5 shadow-2xs">
                                            <span>{{ $allergen }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @if($user->is_verified)
                            <div class="p-3.5 bg-[#D7F57C]/25 rounded-2xl border border-[#C8EE63] text-xs">
                                <label class="inline-flex items-center gap-2.5 font-bold text-[#16251D] cursor-pointer">
                                    <input type="checkbox" name="show_exact_location" value="1" class="rounded text-[#16251D] focus:ring-[#A8DC2E]">
                                    <span>Tampilkan titik presisi langsung di peta publik (Hak Istimewa Usaha/Mitra Terverifikasi)</span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Location Picker & Live Summary (5 cols) -->
            <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">

                <!-- CARD: Titik Penjemputan & Peta Spasial -->
                <div class="glass-card rounded-3xl shadow-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h2 class="font-display font-bold text-base text-[#16251D]">Titik Penjemputan</h2>
                        </div>

                        <!-- Top actions: Satellite switch & GPS -->
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    @click="toggleBasemap()"
                                    class="text-[11px] font-semibold text-[#4E6157] hover:text-[#16251D] px-2.5 py-1 rounded-lg border border-gray-200 bg-gray-50 hover:bg-white transition flex items-center gap-1 shadow-2xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                <span x-text="basemap === 'satellite' ? 'Satelit' : 'Jalan'"></span>
                            </button>
                            <button type="button"
                                    @click="detectLocation()"
                                    class="text-[11px] font-bold text-[#16251D] bg-white hover:bg-[#D7F57C]/40 border border-gray-200 hover:border-[#C8EE63] px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shadow-2xs active:scale-95 group"
                                    title="Pusatkan ke GPS Saya (Google Maps Style)">
                                <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="7" stroke-width="2" />
                                    <circle cx="12" cy="12" r="3" fill="currentColor" stroke="none" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                                </svg>
                                <span>GPS Saya</span>
                            </button>
                        </div>
                    </div>

                    <!-- Smart Google Maps Search Bar with Autocomplete Dropdown -->
                    <div class="relative">
                        <div class="relative flex items-center bg-white rounded-2xl border border-[#D5E0D9] shadow-xs focus-within:border-[#A8DC2E] focus-within:ring-2 focus-within:ring-[#D7F57C]/40 p-1 transition-all">
                            <div class="relative flex-1 min-w-0">
                                <input type="text"
                                       x-model="searchQuery"
                                       @input.debounce.300ms="fetchSuggestions()"
                                       @focus="if (suggestions.length > 0) showSuggestions = true"
                                       @keydown.enter.prevent="selectFirstOrSearch()"
                                       placeholder="Cari jalan, patokan, gedung, kelurahan (misal: Tembalang)..."
                                       class="w-full h-9 pl-9 pr-8 text-xs rounded-xl border-0 text-[#16251D] placeholder:text-[#4E6157]/60 focus:outline-none focus:ring-0 bg-transparent transition-all">
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

                            <!-- Cari Button -->
                            <button type="button"
                                    @click="selectFirstOrSearch()"
                                    class="h-8.5 px-3 rounded-xl bg-[#D7F57C] hover:bg-[#C8EE63] active:bg-[#B4E634] text-[#16251D] font-bold text-xs flex items-center justify-center gap-1 transition shadow-2xs active:scale-95 shrink-0 cursor-pointer border border-[#C8EE63]">
                                <span>Cari</span>
                            </button>
                        </div>

                        <!-- Autocomplete Suggestions Dropdown (Google Maps Style) -->
                        <div x-show="showSuggestions && suggestions.length > 0"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-98"
                             @click.away="showSuggestions = false"
                             class="absolute top-full left-0 right-0 mt-2 bg-white/98 backdrop-blur-2xl rounded-2xl shadow-[0_20px_45px_-8px_rgba(22,37,29,0.22)] border border-[#D5E0D9] overflow-hidden z-50 p-2 space-y-1 max-h-64 overflow-y-auto">
                            
                            <div class="px-3 py-1.5 flex items-center justify-between border-b border-gray-100 mb-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#A8DC2E] animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-[#16251D] tracking-wide uppercase">Hasil Pencarian Tempat</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63]" x-text="`${suggestions.length} opsi`"></span>
                            </div>

                            <template x-for="(s, idx) in suggestions" :key="s.id || idx">
                                <div @click="selectSuggestion(s)"
                                     class="p-2.5 rounded-xl hover:bg-[#D7F57C]/20 cursor-pointer transition flex items-center justify-between gap-2.5 border border-transparent hover:border-[#C8EE63]/60 active:scale-[0.99] group">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <div class="w-8 h-8 rounded-xl bg-[#D7F57C] text-[#16251D] group-hover:bg-[#C8EE63] group-hover:text-[#16251D] flex items-center justify-center shrink-0 transition-colors shadow-2xs border border-[#C8EE63]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-xs text-[#16251D] group-hover:text-[#16251D] truncate transition-colors" x-text="s.title"></span>
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-gray-100 text-[#4E6157] group-hover:bg-[#D7F57C] group-hover:text-[#16251D] transition-colors shrink-0" x-text="s.type_label || 'Wilayah'"></span>
                                                <template x-if="getDistanceKm(s.lat, s.lng)">
                                                    <span class="text-[10px] font-semibold text-[#16251D] bg-[#D7F57C]/40 px-1.5 py-0.2 rounded shrink-0 border border-[#C8EE63]/60" x-text="`~${getDistanceKm(s.lat, s.lng)} km`"></span>
                                                </template>
                                            </div>
                                            <p class="text-[11px] text-[#4E6157] group-hover:text-[#16251D] truncate transition-colors mt-0.5" x-text="s.subtitle || s.display_name"></p>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-bold text-[#16251D] bg-white group-hover:bg-[#D7F57C] px-2.5 py-1 rounded-lg border border-[#C8EE63] shrink-0 transition shadow-2xs">
                                        Pilih Titik &rarr;
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Interactive Map Canvas -->
                    <div class="relative rounded-2xl border border-[#D5E0D9] overflow-hidden shadow-xs">
                        <div id="picker-map" class="h-64 sm:h-72 w-full"></div>

                        <!-- Zoom Dock (+ / -) Google Maps Style -->
                        <div class="absolute bottom-3 right-3 z-10 flex flex-col items-center gap-1.5 pointer-events-auto">
                            <div class="flex flex-col rounded-xl bg-white/95 backdrop-blur-md border border-[#D5E0D9] shadow-sm overflow-hidden">
                                <button type="button" @click="map.zoomIn()" class="w-8 h-8 flex items-center justify-center text-[#16251D] hover:bg-gray-100 transition border-b border-gray-100" title="Perbesar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                                <button type="button" @click="map.zoomOut()" class="w-8 h-8 flex items-center justify-center text-[#16251D] hover:bg-gray-100 transition" title="Perkecil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Instruction badge over map -->
                        <div class="absolute top-2.5 left-2.5 z-10 px-2.5 py-1 rounded-xl bg-white/95 backdrop-blur-md text-[10px] font-bold text-[#16251D] border border-gray-200/80 shadow-2xs pointer-events-none">
                            Geser pin merah untuk atur titik
                        </div>
                    </div>

                    <!-- Reverse Geocode Suggestion Pill -->
                    <template x-if="detectedAddress">
                        <div class="p-2.5 bg-[#D7F57C]/25 rounded-2xl border border-[#C8EE63] flex items-center justify-between gap-2 text-xs">
                            <div class="truncate text-[#16251D] flex items-center gap-1.5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-[#16251D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span class="truncate"><strong class="font-bold">Titik:</strong> <span x-text="detectedAddress"></span></span>
                            </div>
                            <button type="button" @click="useDetectedAddress()" class="btn-primary text-[10px] py-1 px-2.5 rounded-lg shrink-0">
                                Salin ke Alamat
                            </button>
                        </div>
                    </template>

                    <!-- Wilayah Kecamatan (Sinkron Otomatis dari Peta Spasial) -->
                    <div class="p-3 bg-[#D7F57C]/25 rounded-2xl border border-[#C8EE63] flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 rounded-xl bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold text-[#4E6157] block">Wilayah Kecamatan (Sinkron dari Peta):</span>
                                <span class="font-extrabold text-[#16251D] truncate block text-xs" x-text="selectedDistrictName"></span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white text-[#16251D] border border-[#C8EE63] text-[10px] font-bold shrink-0 shadow-2xs">
                            <svg class="w-3 h-3 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Otomatis Sinkron
                        </span>
                    </div>

                    <!-- Address Input & Hidden District Sync -->
                    <div class="space-y-2.5">
                        <div>
                            <label class="font-bold text-xs text-[#16251D] block mb-1">Patokan / Detail Alamat <span class="text-red-500">*</span></label>
                            <input type="text"
                                   name="pickup_address"
                                   x-model="address"
                                   placeholder="Misal: Warung Bu Sari, Jl. Banjarsari No. 12"
                                   class="input-field text-xs font-medium"
                                   required>
                        </div>
                        <input type="hidden" name="district_id" :value="selectedDistrict">
                    </div>

                    <!-- Rekomendasi Titik Temu Aman (Safe Zones) -->
                    <div class="space-y-1.5 pt-1">
                        <span class="text-[11px] font-bold text-[#4E6157] block">Rekomendasi Titik Temu Aman (Demi Privasi Rumah):</span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" @click="appendSafeZone('Pos Satpam / Gerbang')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 hover:bg-[#D7F57C]/40 hover:text-[#16251D] hover:border-[#C8EE63] text-[11px] font-semibold rounded-lg border border-[#D5E0D9] transition active:scale-95">
                                <svg class="w-3 h-3 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Pos Satpam</span>
                            </button>
                            <button type="button" @click="appendSafeZone('Minimarket 24 Jam')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 hover:bg-[#D7F57C]/40 hover:text-[#16251D] hover:border-[#C8EE63] text-[11px] font-semibold rounded-lg border border-[#D5E0D9] transition active:scale-95">
                                <svg class="w-3 h-3 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Minimarket 24 Jam</span>
                            </button>
                            <button type="button" @click="appendSafeZone('Halaman Masjid')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 hover:bg-[#D7F57C]/40 hover:text-[#16251D] hover:border-[#C8EE63] text-[11px] font-semibold rounded-lg border border-[#D5E0D9] transition active:scale-95">
                                <svg class="w-3 h-3 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                <span>Halaman Masjid</span>
                            </button>
                            <button type="button" @click="appendSafeZone('Area SPBU')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 hover:bg-[#D7F57C]/40 hover:text-[#16251D] hover:border-[#C8EE63] text-[11px] font-semibold rounded-lg border border-[#D5E0D9] transition active:scale-95">
                                <svg class="w-3 h-3 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 22h12M4 4h10a1 1 0 011 1v17H4V5a1 1 0 011-1zM7 8h4M15 9h2a2 2 0 012 2v6a2 2 0 002 2h0a2 2 0 002-2V9a2 2 0 00-2-2h-1M19 5l-2 2"/></svg>
                                <span>Area SPBU</span>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden Coordinates -->
                    <input type="hidden" name="latitude" :value="lat">
                    <input type="hidden" name="longitude" :value="lng">

                    <!-- Privacy Shield Guarantee -->
                    <div class="p-3 bg-blue-50/70 rounded-2xl border border-blue-200/80 text-[11px] text-[#2A6F97] flex items-start gap-2.5">
                        <svg class="w-4 h-4 shrink-0 text-[#2A6F97] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span><strong>Perlindungan Privasi:</strong> Publik hanya melihat perkiraan titik radius ~200m di peta. Alamat presisi ini hanya dibuka kepada pemohon yang klaimnya telah Anda setujui.</span>
                    </div>
                </div>

                <!-- CARD: Pratinjau Listing Makanan (Live Feed Preview — Gaya Too Good To Go & Olio) -->
                <div class="glass-card rounded-3xl shadow-card overflow-hidden space-y-0">
                    <!-- Preview Card Header -->
                    <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#A8DC2E]"></span>
                            <span class="font-display font-bold text-xs text-[#16251D] uppercase tracking-wider">Pratinjau Listing</span>
                        </div>
                        <span class="text-[11px] text-[#4E6157]">Tampilan di feed tetangga</span>
                    </div>

                    <!-- Live Card Body -->
                    <div class="p-5 sm:p-6 space-y-4">
                        <!-- Photo / Category Visual Banner -->
                        <div class="relative rounded-2xl overflow-hidden border border-gray-100 bg-gray-50 h-44 sm:h-48 group">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!photoPreview">
                                <div class="w-full h-full bg-gradient-to-br from-[#D7F57C]/25 via-white to-gray-50 flex flex-col items-center justify-center p-4 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-[#C8EE63] shadow-2xs text-[#16251D] flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v3m-4-3v3m8-3v3M4 11h16a8 8 0 01-16 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold text-[#16251D]" x-text="categoryName"></span>
                                    <span class="text-[10px] text-[#4E6157] mt-0.5">Foto akan tampil di sini saat diunggah</span>
                                </div>
                            </template>

                            <!-- Floating Top Pills on Image -->
                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1 pointer-events-none">
                                <span class="px-2.5 py-1 rounded-full bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-2xs text-[11px] font-bold"
                                      x-text="`${portions} Porsi Tersedia`"></span>

                                <span class="px-2.5 py-1 rounded-full bg-[#16251D]/90 backdrop-blur-md shadow-2xs text-[11px] font-bold text-white flex items-center gap-1">
                                    <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="deadlineBadgeText"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Title & Tags -->
                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap mb-1.5">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-gray-100 text-[#4E6157]" x-text="categoryName"></span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63]/60" x-text="foodTypeLabel"></span>
                                <template x-if="halal">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63]/80">Halal</span>
                                </template>
                                <template x-if="vegetarian">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63]/80">Vegetarian</span>
                                </template>
                            </div>

                            <h3 class="font-display font-bold text-base sm:text-lg text-[#16251D] leading-snug line-clamp-2"
                                x-text="title || 'Nama Makanan Belum Diisi'"></h3>
                            
                            <div class="flex items-center gap-2 mt-1.5 text-xs text-[#4E6157]">
                                <span class="font-medium text-[#16251D]">{{ $user->name }}</span>
                                @if($user->is_verified)
                                    <span class="text-xs text-[#2A6F97] font-bold flex items-center gap-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Terverifikasi
                                    </span>
                                @endif
                                <span class="text-gray-300">•</span>
                                <span class="text-[#4E6157] truncate max-w-[150px]" x-text="address ? address.split(',')[0] : (detectedAddress ? detectedAddress.split(',')[0] : 'Lokasi Terpilih')"></span>
                            </div>
                        </div>

                        <!-- Schedule & Meeting Spot Info Box -->
                        <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-100 text-xs space-y-2">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[11px] text-[#4E6157] block">Batas Waktu Pengambilan:</span>
                                    <strong class="font-bold text-[#16251D]" x-text="deadlineFormatted"></strong>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 pt-2 border-t border-gray-200/60">
                                <svg class="w-4 h-4 text-[#16251D] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[11px] text-[#4E6157] block">Batas per Pengambil:</span>
                                    <strong class="font-bold text-[#16251D]" x-text="hasPortionLimit ? `Maksimal ${customMaxPortions} Porsi / Akun` : 'Bebas (Tanpa Batasan Porsi)'"></strong>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 pt-2 border-t border-gray-200/60">
                                <svg class="w-4 h-4 text-[#16251D] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[11px] text-[#4E6157] block">Titik Penjemputan:</span>
                                    <strong class="font-bold text-[#16251D] line-clamp-1" x-text="address || 'Belum diisi (Wajib)'"></strong>
                                    <span class="text-[10px] text-[#4E6157]">Alamat presisi aman terlindungi</span>
                                </div>
                            </div>
                        </div>

                        <!-- Primary Action Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full py-3.5 px-5 rounded-2xl bg-[#D7F57C] hover:bg-[#C8EE63] active:bg-[#B4E634] active:scale-[0.98] text-[#16251D] font-black text-sm sm:text-base flex items-center justify-center gap-2 border border-[#C8EE63] shadow-xs hover:shadow-md transition-all duration-150 cursor-pointer group">
                                <svg class="w-5 h-5 text-[#16251D] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Bagikan Makanan Sekarang</span>
                            </button>
                        </div>

                        <!-- Human Safety Reassurance -->
                        <div class="p-3 bg-[#D7F57C]/25 rounded-2xl border border-[#C8EE63]/70 text-[11px] text-[#16251D] flex items-start gap-2">
                            <svg class="w-4 h-4 text-[#16251D] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <div class="leading-relaxed">
                                <strong>Anda Memegang Kendali:</strong> Setiap pemohon harus meminta porsi dan Anda berhak menyetujui atau menolak sebelum alamat lengkap diberikan.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('createListingForm', () => ({
        photoPreview: null,
        title: '{{ old('title', $duplicate->title ?? '') }}',
        foodType: '{{ old('food_type', $duplicate->food_type ?? 'cooked') }}',
        categoryId: '{{ old('category_id', $duplicate->category_id ?? ($categories->first()->id ?? 1)) }}',
        portions: {{ old('portions_total', $duplicate->portions_total ?? 5) }},
        hasPortionLimit: {{ old('max_portions_per_claim') && old('max_portions_per_claim') < old('portions_total', 5) ? 'true' : 'false' }},
        customMaxPortions: {{ old('max_portions_per_claim', 2) }},
        hours: {{ old('hours_valid', 2) }},
        timeMode: 'clock',
        clockTime: '{{ old('expires_time', '') }}',
        clockIsTomorrow: false,
        lat: {{ old('latitude', $user->default_latitude ?? -7.05194) }},
        lng: {{ old('longitude', $user->default_longitude ?? 110.43778) }},
        address: '{{ old('pickup_address', $duplicate->pickup_address ?? '') }}',
        selectedDistrict: '{{ old('district_id', $duplicate->district_id ?? $user->district_id ?? ($districts->first()->id ?? 1)) }}',
        halal: {{ old('halal_claim', true) ? 'true' : 'false' }},
        vegetarian: {{ old('is_vegetarian', false) ? 'true' : 'false' }},
        detectedAddress: '',
        searchQuery: '',
        suggestions: [],
        showSuggestions: false,
        basemap: 'standard',
        map: null,
        marker: null,
        osmLayer: null,
        satLayer: null,

        categoriesMap: {
            @foreach($categories as $cat)
                '{{ $cat->id }}': '{{ addslashes($cat->name) }}',
            @endforeach
        },

        districtsMap: {
            @foreach($districts as $d)
                '{{ $d->id }}': 'Kec. {{ addslashes($d->name) }}',
            @endforeach
        },

        districtsList: [
            @foreach($districts as $d)
                {
                    id: {{ $d->id }},
                    name: '{{ addslashes($d->name) }}',
                    regency_name: '{{ addslashes($d->regency->name ?? '') }}',
                    latitude: {{ $d->latitude ? (float) $d->latitude : 'null' }},
                    longitude: {{ $d->longitude ? (float) $d->longitude : 'null' }}
                },
            @endforeach
        ],

        findNearestDistrict(lat, lng) {
            if (!lat || !lng) return;
            let best = null;
            let minDist = Infinity;
            for (const d of this.districtsList) {
                if (d.latitude === null || d.longitude === null) continue;
                const dLat = (d.latitude - lat) * (Math.PI / 180);
                const dLng = (d.longitude - lng) * (Math.PI / 180);
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                          Math.cos(lat * (Math.PI / 180)) * Math.cos(d.latitude * (Math.PI / 180)) *
                          Math.sin(dLng / 2) * Math.sin(dLng / 2);
                const dist = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                if (dist < minDist) {
                    minDist = dist;
                    best = d;
                }
            }
            if (best) {
                this.selectedDistrict = best.id;
                this.selectedDistrictName = `Kec. ${best.name}` + (best.regency_name ? ` (${best.regency_name})` : '');
            }
        },

        getDistanceKm(targetLat, targetLng) {
            if (!targetLat || !targetLng || !this.lat || !this.lng) return null;
            const dLat = (targetLat - this.lat) * (Math.PI / 180);
            const dLng = (targetLng - this.lng) * (Math.PI / 180);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(this.lat * (Math.PI / 180)) * Math.cos(targetLat * (Math.PI / 180)) *
                      Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const d = 6371 * c;
            return d < 10 ? d.toFixed(1) : Math.round(d);
        },

        clearSearch() {
            this.searchQuery = '';
            this.suggestions = [];
            this.showSuggestions = false;
        },

        async selectFirstOrSearch() {
            if (this.suggestions && this.suggestions.length > 0) {
                this.selectSuggestion(this.suggestions[0]);
            } else if (this.searchQuery.trim().length >= 2) {
                await this.fetchSuggestions();
                if (this.suggestions && this.suggestions.length > 0) {
                    this.selectSuggestion(this.suggestions[0]);
                }
            }
        },

        get categoryName() {
            return this.categoriesMap[this.categoryId] || 'Makanan';
        },

        get selectedDistrictName() {
            return this.districtsMap[this.selectedDistrict] || 'Area Terdekat';
        },

        get foodTypeLabel() {
            if (this.foodType === 'cooked') return 'Siap Santap';
            if (this.foodType === 'packaged') return 'Kemasan';
            return 'Bahan Mentah';
        },

        onClockTimeChange() {
            if (!this.clockTime) return;
            const [hStr, mStr] = this.clockTime.split(':');
            const h = parseInt(hStr, 10);
            const m = parseInt(mStr, 10);

            const now = new Date();
            let target = new Date(now.getFullYear(), now.getMonth(), now.getDate(), h, m, 0);

            if (target.getTime() - now.getTime() < 10 * 60 * 1000) {
                target.setDate(target.getDate() + 1);
                this.clockIsTomorrow = true;
            } else {
                this.clockIsTomorrow = target.getDate() !== now.getDate();
            }

            const diffMs = target.getTime() - now.getTime();
            const diffHours = Math.max(1, Math.min(24, Math.round(diffMs / (3600 * 1000))));
            this.hours = diffHours;
        },

        get deadlineBadgeText() {
            if (this.clockTime) {
                return `Batas: ${this.clockTime} WIB`;
            }
            return `Sisa ${this.hours} Jam`;
        },

        get deadlineFormatted() {
            if (this.clockTime) {
                const [hStr, mStr] = this.clockTime.split(':');
                const now = new Date();
                let target = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(hStr, 10), parseInt(mStr, 10), 0);
                if (target.getTime() - now.getTime() < 10 * 60 * 1000) {
                    target.setDate(target.getDate() + 1);
                }
                const isToday = target.getDate() === now.getDate();
                const diffMs = Math.max(0, target.getTime() - now.getTime());
                const remainHours = Math.floor(diffMs / (3600 * 1000));
                const remainMins = Math.floor((diffMs % (3600 * 1000)) / (60 * 1000));
                const remainText = remainHours > 0 ? `${remainHours} jam ${remainMins} mnt` : `${remainMins} menit`;

                return `Pukul ${this.clockTime} WIB (${isToday ? 'Hari ini' : 'Besok'} • sisa ~${remainText})`;
            }

            const d = new Date(Date.now() + this.hours * 3600 * 1000);
            const hoursStr = String(d.getHours()).padStart(2, '0');
            const minsStr = String(d.getMinutes()).padStart(2, '0');
            const isToday = d.getDate() === (new Date()).getDate();
            return `Pukul ${hoursStr}:${minsStr} WIB (${isToday ? 'Hari ini' : 'Besok'} • sisa ${this.hours} jam)`;
        },

        init() {
            this.findNearestDistrict(this.lat, this.lng);

            if (!this.clockTime) {
                const defaultTarget = new Date(Date.now() + this.hours * 3600 * 1000);
                this.clockTime = `${String(defaultTarget.getHours()).padStart(2, '0')}:${String(defaultTarget.getMinutes()).padStart(2, '0')}`;
                this.clockIsTomorrow = defaultTarget.getDate() !== (new Date()).getDate();
            } else {
                this.onClockTimeChange();
            }

            this.$nextTick(() => {
                this.initPickerMap();
                this.reverseGeocode();

                // Automatically calibrate to device GPS location if available
                if (navigator.geolocation && !{{ old('latitude') ? 'true' : 'false' }}) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.lat = pos.coords.latitude;
                        this.lng = pos.coords.longitude;
                        if (this.map) this.map.setView([this.lat, this.lng], 16);
                        if (this.marker) this.marker.setLatLng([this.lat, this.lng]);
                        this.findNearestDistrict(this.lat, this.lng);
                        this.reverseGeocode();
                    }, () => {}, { enableHighAccuracy: true, timeout: 6000 });
                }
            });
        },

        initPickerMap() {
            this.map = L.map('picker-map', { zoomControl: false }).setView([this.lat, this.lng], 15);

            this.osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            this.satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: '&copy; Esri World Imagery'
            });

            // Custom Google Maps-style red teardrop pin
            const customPin = L.divIcon({
                className: 'custom-picker-pin',
                html: `
                    <div style="position:relative; width:34px; height:42px; transform:translate(-17px, -42px);">
                        <svg viewBox="0 0 24 24" style="width:34px; height:42px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" fill="none">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#C93A2E"/>
                            <circle cx="12" cy="9" r="3.2" fill="#FFFFFF"/>
                        </svg>
                    </div>
                `,
                iconSize: [34, 42],
                iconAnchor: [17, 42]
            });

            this.marker = L.marker([this.lat, this.lng], {
                draggable: true,
                icon: customPin
            }).addTo(this.map);

            this.marker.bindTooltip("Geser pin ini ke titik temu presisi", {
                direction: 'top',
                offset: [0, -38]
            });

            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.lat = pos.lat;
                this.lng = pos.lng;
                this.findNearestDistrict(this.lat, this.lng);
                this.reverseGeocode();
            });

            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.lat = e.latlng.lat;
                this.lng = e.latlng.lng;
                this.findNearestDistrict(this.lat, this.lng);
                this.reverseGeocode();
            });

            // Prevent grey tiles by observing container resize
            if (window.ResizeObserver) {
                const ro = new ResizeObserver(() => {
                    if (this.map) this.map.invalidateSize();
                });
                const el = document.getElementById('picker-map');
                if (el) ro.observe(el);
            }
            setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 300);
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
            if (this.marker) this.marker.setLatLng([s.lat, s.lng]);
            if (this.map) this.map.flyTo([s.lat, s.lng], 16);
            this.detectedAddress = s.display_name;
            if (!this.address) {
                this.address = s.display_name;
            }
            this.findNearestDistrict(s.lat, s.lng);
            this.reverseGeocode();
        },

        async reverseGeocode() {
            try {
                const res = await fetch(`/api/geocoding/reverse?lat=${this.lat}&lng=${this.lng}`);
                if (res.ok) {
                    const data = await res.json();
                    this.detectedAddress = data.short_address || data.display_name || '';
                    if (data.district_id) {
                        this.selectedDistrict = data.district_id;
                        this.selectedDistrictName = `Kec. ${data.district_name}` + (data.regency_name ? ` (${data.regency_name})` : '');
                    }
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
                this.findNearestDistrict(this.lat, this.lng);
                this.reverseGeocode();
            });
        },

        async previewImage(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('File yang dipilih bukan berkas gambar yang valid.');
                e.target.value = '';
                return;
            }

            // Immediate preview for instant user feedback
            this.photoPreview = URL.createObjectURL(file);

            // Auto-compress large photos (from mobile cameras) client-side
            // This prevents PostTooLargeException and strips camera EXIF before upload
            try {
                const compressedBlob = await this.compressImage(file, 1600, 1600, 0.82);
                
                // Replace file in input with the compressed version
                const dt = new DataTransfer();
                const optimizedFile = new File([compressedBlob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });
                dt.items.add(optimizedFile);
                e.target.files = dt.files;
                
                this.photoPreview = URL.createObjectURL(optimizedFile);
            } catch (err) {
                console.warn('Kompresi browser dilewati:', err);
            }
        },

        compressImage(file, maxWidth, maxHeight, quality) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob) {
                                resolve(blob);
                            } else {
                                reject(new Error('Gagal mengompres gambar'));
                            }
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = reject;
                    img.src = e.target.result;
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        },

        removePhoto() {
            this.photoPreview = null;
            const input = document.getElementById('photo-input');
            if (input) input.value = '';
        }
    }));
});
</script>
@endpush

</x-layouts.app>
