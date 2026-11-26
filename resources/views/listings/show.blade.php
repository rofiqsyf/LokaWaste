<x-layouts.app :title="$listing->title . ' — LokaWaste'">

<div x-data="qrHandover()" class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6">

    <!-- Top Navigation / Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4E6157] hover:text-[#16251D]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Peta
        </a>

        <div class="flex items-center gap-2">
            <x-status-badge :status="$listing->status" />
            @if($listing->user->is_verified)
                <span class="badge-verified text-xs">Terverifikasi</span>
            @endif
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="glass-frosted rounded-3xl shadow-card overflow-hidden">
        <!-- Photo Section -->
        <div class="relative h-64 sm:h-80 bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($listing->photo_path)
                <img src="{{ asset('storage/' . $listing->photo_path) }}"
                     alt="{{ $listing->title }}"
                     class="w-full h-full object-cover">
            @else
                <div class="text-center p-6 text-[#4E6157]">
                    <div class="w-16 h-16 mx-auto mb-2 rounded-2xl bg-white/80 flex items-center justify-center text-[#1E7A4C] shadow-xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold">{{ $listing->category->name }}</span>
                </div>
            @endif

            <!-- Floating Time Ring on Image corner -->
            <div class="absolute top-4 right-4 glass-clear p-2.5 rounded-2xl shadow-card flex items-center gap-3">
                <x-time-ring :listing="$listing" />
                <div class="text-right pr-1">
                    <span class="text-[10px] text-[#4E6157] font-bold uppercase tracking-wider block">Batas Ambil</span>
                    <span class="font-display font-black text-sm text-[#16251D] tracking-tight">
                        {{ $listing->expires_at->setTimezone($listing->timezone())->format('H:i') }} {{ $listing->tzLabel() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Details Content -->
        <div class="p-6 sm:p-8 space-y-6">

            <!-- Title & Provider Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#D5E0D9] pb-6">
                <div>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-[#4E6157]">
                            {{ $listing->category->name }}
                        </span>
                        @if($listing->halal_claim)
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63]/80">
                                Pernyataan Halal Pemberi
                            </span>
                        @endif
                        @if($listing->is_vegetarian)
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63]/80">
                                Vegetarian
                            </span>
                        @endif
                    </div>
                    <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">{{ $listing->title }}</h1>
                    <p class="text-sm text-[#4E6157] mt-1">
                        Dibagikan oleh <strong class="text-[#16251D]">{{ $listing->user->organization_name ?? $listing->user->name }}</strong>
                        @if($listing->user->isVolunteer())
                            <span class="ml-1 inline-block"><x-volunteer-badge label="Relawan" /></span>
                        @endif
                    </p>
                </div>

                <div class="sm:text-right shrink-0 glass-clear p-4 rounded-2xl">
                    <span class="text-xs text-[#4E6157] block">Sisa Makanan</span>
                    <span class="font-display font-bold text-2xl text-[#1E7A4C]">{{ $listing->portions_left }}</span>
                    <span class="text-xs text-[#4E6157]">/ {{ $listing->portions_total }} porsi</span>
                </div>
            </div>

            <!-- Campaign Priority Notice (if applicable) -->
            @if($listing->campaign_id && $listing->campaign)
                <div class="p-4 rounded-2xl bg-blue-50 border border-[#2A6F97]/30 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-[#2A6F97] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="text-xs text-[#2A6F97] space-y-1">
                        <p class="font-bold text-sm">Ditujukan untuk Galang Makanan:</p>
                        <p class="font-semibold">{{ $listing->campaign->title }} ({{ $listing->campaign->beneficiary_name }})</p>
                        @if($listing->campaign_priority_until && $listing->campaign_priority_until->isFuture())
                            <p class="text-[#7A4A00] font-bold flex items-center gap-1.5 mt-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Diprioritaskan untuk relawan galang hingga pukul {{ $listing->campaign_priority_until->setTimezone($listing->timezone())->format('H:i') }} {{ $listing->tzLabel() }}.
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Description -->
            <div>
                <h2 class="font-display font-bold text-base text-[#16251D] mb-2">Keterangan Makanan</h2>
                <p class="text-sm text-[#16251D] leading-relaxed whitespace-pre-line">{{ $listing->description }}</p>
            </div>

            <!-- Food Safety Information (FR-36, SDD 3.8) -->
            <div class="p-5 rounded-2xl glass-dock space-y-3">
                <h3 class="font-display font-bold text-sm text-[#16251D] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Informasi & Keamanan Pangan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[#4E6157] block">Jenis Makanan:</span>
                        <strong class="text-[#16251D]">
                            {{ match($listing->food_type) { 'cooked' => 'Makanan Dimasak (Siap Santap)', 'packaged' => 'Makanan Kemasan', 'raw' => 'Bahan Mentah', default => $listing->food_type } }}
                        </strong>
                    </div>

                    @if($listing->prepared_at)
                    <div>
                        <span class="text-[#4E6157] block">Waktu Dibuat / Dimasak:</span>
                        <strong class="text-[#16251D]">
                            {{ $listing->prepared_at->setTimezone($listing->timezone())->format('d M Y, H:i') }} {{ $listing->tzLabel() }}
                        </strong>
                    </div>
                    @endif

                    @if(!empty($listing->allergens) && count($listing->allergens) > 0)
                    <div>
                        <span class="text-[#4E6157] block">Alergen:</span>
                        <div class="flex items-center gap-1 mt-1 flex-wrap">
                            @foreach($listing->allergens as $al)
                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-bold text-[10px]">{{ $al }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($listing->storage_note)
                    <div>
                        <span class="text-[#4E6157] block">Catatan Penyimpanan:</span>
                        <strong class="text-[#16251D]">{{ $listing->storage_note }}</strong>
                    </div>
                    @endif
                </div>

                <p class="text-[11px] text-[#4E6157] italic pt-1 border-t border-[#D5E0D9]">
                    Peringatan: Periksa kondisi makanan sebelum dikonsumsi. Pemberi bertanggung jawab atas kelayakan makanan yang dibagikan.
                </p>
            </div>

            <!-- Location & Privacy Section (FR-37, SDD 6.5) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="font-display font-bold text-base text-[#16251D]">Lokasi Pengambilan</h2>
                    <span class="text-xs font-semibold text-[#4E6157]">Kec. {{ $listing->district->name }}, {{ $listing->district->regency->name }}</span>
                </div>

                @if($canSeeExact)
                    <!-- EXACT LOCATION UNLOCKED (Owner or Approved Claimer) -->
                    <div class="p-4 rounded-2xl bg-[#D7F57C]/20 border border-[#C8EE63]/80 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#16251D]">
                            <div class="w-5 h-5 rounded-full bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-[#16251D]" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </div>
                            <span>Titik Tepat & Alamat Dibuka</span>
                        </div>
                        <p class="text-sm font-bold text-[#16251D]">{{ $listing->pickup_address }}</p>
                        @if($listing->user->phone)
                            <p class="text-xs text-[#4E6157]">Kontak Pemberi: <strong class="text-[#16251D]">{{ $listing->user->phone }}</strong></p>
                        @endif
                    </div>
                @else
                    <!-- APPROXIMATE LOCATION NOTICE -->
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-[#2A6F97]/30 text-xs text-[#2A6F97] flex items-start gap-3">
                        <svg class="w-5 h-5 shrink-0 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-bold">Lokasi Perkiraan (Privasi Pemberi Terlindungi)</p>
                            <p class="text-[#4E6157] mt-0.5">Alamat lengkap dan titik presisi akan dibuka setelah klaim Anda disetujui oleh pemberi makanan.</p>
                        </div>
                    </div>
                @endif

                <!-- Mini Leaflet Map -->
                <div id="listing-mini-map" class="h-52 w-full rounded-2xl overflow-hidden border border-[#D5E0D9] relative shadow-xs"></div>

                <!-- Google Maps Navigation Action Toolbar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $canSeeExact ? $listing->latitude : $listing->public_latitude }},{{ $canSeeExact ? $listing->longitude : $listing->public_longitude }}"
                       target="_blank"
                       class="btn-primary text-xs py-2 px-3.5 flex items-center justify-center gap-1.5 shadow-xs">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        <span>Petunjuk Arah Google Maps</span>
                    </a>

                    <a href="{{ route('home', ['focus' => $listing->id, 'lat' => $listing->public_latitude, 'lng' => $listing->public_longitude]) }}"
                       class="btn-secondary text-xs py-2 px-3.5 flex items-center justify-center gap-1.5 shadow-xs hover:border-[#C8EE63] hover:text-[#16251D]">
                        <svg class="w-3.5 h-3.5 text-[#16251D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span>Buka di Peta Interaktif</span>
                    </a>
                </div>
            </div>

            <!-- ACTION SECTION -->
            <div class="pt-4 border-t border-[#D5E0D9]">
                @auth
                    @if(Auth::id() === $listing->user_id)
                        <!-- OWNER PANEL -->
                        <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-display font-bold text-sm text-[#7A4A00]">Ini adalah listing makanan milik Anda</h3>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('listings.duplicate', $listing->id) }}" class="btn-secondary text-xs px-3 py-1.5">
                                        Bagikan lagi
                                    </a>
                                    @if(in_array($listing->status, ['available', 'reserved']))
                                        <form method="POST" action="{{ route('listings.cancel', $listing->id) }}" onsubmit="return confirm('Yakin ingin membatalkan listing ini?')">
                                            @csrf
                                            <button type="submit" class="btn-danger text-xs px-3 py-1.5">
                                                Batalkan listing
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Incoming Claims to decide -->
                            <div>
                                <h4 class="text-xs font-bold text-[#16251D] mb-2 uppercase tracking-wider">Klaim Masuk ({{ $listing->claims->count() }})</h4>
                                @if($listing->claims->isEmpty())
                                    <p class="text-xs text-[#4E6157]">Belum ada pengguna yang mengajukan klaim.</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach($listing->claims as $claim)
                                            <div class="p-3 bg-white rounded-xl border border-[#D5E0D9] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <strong class="text-sm text-[#16251D]">{{ $claim->user->name }}</strong>
                                                        <x-status-badge :status="$claim->status" />
                                                    </div>
                                                    <p class="text-[#4E6157] mt-0.5">
                                                        Meminta <strong class="text-[#1E7A4C]">{{ $claim->portions_requested }} porsi</strong>
                                                        @if($claim->campaign_id)
                                                            <span class="text-[#2A6F97] font-semibold">(atas nama galang {{ $claim->campaign?->title }})</span>
                                                        @endif
                                                    </p>
                                                    @if($claim->status === 'approved')
                                                        <p class="mt-1 font-mono font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded inline-block">
                                                            Kode ambil: {{ $claim->pickup_code }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <!-- Owner Actions on Claim -->
                                                <div class="flex items-center gap-2">
                                                    @if($claim->status === 'pending')
                                                        <form method="POST" action="{{ route('claims.approve', $claim->id) }}" class="flex items-center gap-1">
                                                            @csrf
                                                            <input type="number" name="portions" value="{{ min($claim->portions_requested, $listing->portions_left) }}"
                                                                   min="1" max="{{ min($claim->portions_requested, $listing->portions_left) }}"
                                                                   class="w-16 px-2 py-1 text-xs border rounded">
                                                            <button type="submit" class="btn-primary text-xs px-3 py-1">Setujui</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('claims.reject', $claim->id) }}">
                                                            @csrf
                                                            <button type="submit" class="btn-danger text-xs px-2.5 py-1">Tolak</button>
                                                        </form>
                                                    @elseif($claim->status === 'approved')
                                                        <!-- Verify pickup code form & QR Scanner -->
                                                        <div class="flex items-center gap-2">
                                                            <button type="button"
                                                                    @click="startScanning({{ $claim->id }})"
                                                                    class="btn-primary text-xs px-2.5 py-1.5 flex items-center gap-1 shadow-xs cursor-pointer">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                                <span>Scan QR</span>
                                                            </button>
                                                            <form method="POST" action="{{ route('claims.verify-pickup', $claim->id) }}" class="flex items-center gap-1">
                                                                @csrf
                                                                <input type="text" name="pickup_code" placeholder="Kode manual" maxlength="6"
                                                                       class="w-24 px-2 py-1 text-xs uppercase font-mono font-bold border rounded" required>
                                                                <button type="submit" class="btn-secondary text-xs px-2.5 py-1">OK</button>
                                                            </form>
                                                            <form method="POST" action="{{ route('claims.cancel', $claim->id) }}">
                                                                @csrf
                                                                <button type="submit" class="text-xs text-[#C93A2E] hover:underline" onclick="return confirm('Batalkan klaim disetujui? Porsi akan dikembalikan.')">
                                                                    Batal
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                    @else
                        <!-- OTHER USERS: CLAIM FORM OR CURRENT CLAIM STATUS -->
                        @if($userActiveClaim)
                            <div class="p-5 rounded-2xl bg-blue-50 border border-[#2A6F97]/30 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-display font-bold text-base text-[#2A6F97]">Status Klaim Anda</h3>
                                        <x-status-badge :status="$userActiveClaim->status" />
                                    </div>
                                    <form method="POST" action="{{ route('claims.cancel', $userActiveClaim->id) }}" onsubmit="return confirm('Yakin ingin membatalkan klaim Anda?')">
                                        @csrf
                                        <button type="submit" class="text-xs text-[#C93A2E] hover:underline font-bold">
                                            Batalkan Klaim
                                        </button>
                                    </form>
                                </div>

                                @if($userActiveClaim->status === 'approved')
                                    <!-- Smart QR Code & Pickup Code Handover Box -->
                                    <div class="p-4 bg-[#D7F57C]/25 rounded-2xl border-2 border-[#C8EE63] flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                                        <div class="w-28 h-28 shrink-0 bg-white p-1.5 rounded-2xl border border-[#C8EE63] shadow-2xs flex items-center justify-center cursor-pointer group hover:scale-105 transition-transform"
                                             @click="openQrEnlarged({{ $userActiveClaim->id }}, '{{ $userActiveClaim->pickup_code }}', '{{ addslashes($listing->title) }}', {{ $userActiveClaim->portions_approved ?? $userActiveClaim->portions_requested }})"
                                             title="Ketuk untuk perbesar QR Code">
                                            <div data-lokawaste-qr="LOKAWASTE:CLAIM:{{ $userActiveClaim->id }}:{{ $userActiveClaim->pickup_code }}" data-locabite-qr="LOCABITE:CLAIM:{{ $userActiveClaim->id }}:{{ $userActiveClaim->pickup_code }}" class="w-full h-full flex items-center justify-center">
                                                <div class="w-5 h-5 border-2 border-[#16251D] border-t-transparent rounded-full animate-spin"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0 space-y-1.5">
                                            <span class="text-[11px] font-bold text-[#14573A] block">Tunjukkan QR atau Kode kepada Pemberi:</span>
                                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                                <span class="font-mono font-black text-2xl tracking-widest text-[#16251D] bg-white px-3 py-1 rounded-xl border border-[#C8EE63]">
                                                    {{ $userActiveClaim->pickup_code }}
                                                </span>
                                                <button type="button"
                                                        @click="copyCode('{{ $userActiveClaim->pickup_code }}')"
                                                        class="p-2 rounded-xl bg-white border border-[#C8EE63] text-[#16251D] hover:bg-[#D7F57C]/40 transition-colors shadow-2xs cursor-pointer"
                                                        title="Salin Kode">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                </button>
                                            </div>
                                            <div class="flex items-center justify-center sm:justify-start gap-2 pt-0.5">
                                                <span class="text-xs text-[#1E7A4C] font-bold">Disetujui: {{ $userActiveClaim->portions_approved }} porsi</span>
                                                <span class="text-[#88998F]">&bull;</span>
                                                <button type="button"
                                                        @click="openQrEnlarged({{ $userActiveClaim->id }}, '{{ $userActiveClaim->pickup_code }}', '{{ addslashes($listing->title) }}', {{ $userActiveClaim->portions_approved ?? $userActiveClaim->portions_requested }})"
                                                        class="text-xs font-bold text-[#2A6F97] hover:underline flex items-center gap-1 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                                    Perbesar QR Layar Penuh
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($userActiveClaim->status === 'pending')
                                    <p class="text-xs text-[#4E6157]">
                                        Permintaan <strong class="text-[#16251D]">{{ $userActiveClaim->portions_requested }} porsi</strong> Anda sedang menunggu konfirmasi pemberi. Mohon pantau notifikasi atau dashboard.
                                    </p>
                                @endif
                            </div>

                        @elseif($listing->isClaimableBy(Auth::user()))
                            <!-- CLAIM FORM -->
                            <form method="POST" action="{{ route('claims.store', $listing->id) }}" class="space-y-4">
                                @csrf
                                <div class="p-5 rounded-2xl glass-card border border-[#C8EE63]/80 shadow-xs space-y-3">
                                    <h3 class="font-display font-bold text-base text-[#16251D]">Ajukan Pengambilan Makanan</h3>

                                    <div class="flex items-center gap-4">
                                        <div>
                                            <label class="text-xs font-semibold text-[#4E6157] block mb-1">Jumlah Porsi</label>
                                            <input type="number" name="portions" value="1" min="1"
                                                   max="{{ min($listing->max_portions_per_claim, $listing->portions_left) }}"
                                                   class="input-field w-28 text-center font-bold text-base" required>
                                        </div>
                                        <div class="text-xs text-[#4E6157] pt-4">
                                            @if($listing->max_portions_per_claim < $listing->portions_left)
                                                Dibatasi oleh pemberi: Maksimal <strong class="text-[#16251D]">{{ $listing->max_portions_per_claim }} porsi</strong> per klaim.
                                            @else
                                                Bebas diambil hingga <strong class="text-[#16251D]">{{ $listing->portions_left }} porsi</strong> (Sesuai sisa tersedia).
                                            @endif
                                        </div>
                                    </div>

                                    <!-- If volunteer, optional campaign claim -->
                                    @if($volunteerCampaigns->isNotEmpty())
                                        <div class="pt-2">
                                            <label class="text-xs font-semibold text-[#2A6F97] block mb-1">Ambil atas nama Galang Makanan (Relawan)</label>
                                            <select name="campaign_id" class="input-field text-xs">
                                                <option value="">-- Klaim Pribadi (Bukan Galang) --</option>
                                                @foreach($volunteerCampaigns as $camp)
                                                    <option value="{{ $camp->id }}">Galang: {{ $camp->title }} ({{ $camp->beneficiary_name }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="pt-2">
                                        <button type="submit" class="btn-primary w-full text-base py-3">
                                            Ajukan klaim sekarang
                                        </button>
                                    </div>
                                </div>
                            </form>

                        @else
                            <div class="p-4 rounded-xl bg-gray-100 text-center text-xs text-[#4E6157]">
                                @if($listing->status !== 'available')
                                    Makanan ini sudah tidak tersedia untuk diklaim.
                                @elseif($listing->campaign_priority_until && $listing->campaign_priority_until->isFuture())
                                    Diprioritaskan untuk relawan galang sampai {{ $listing->campaign_priority_until->setTimezone($listing->timezone())->format('H:i') }}. Anda bisa mengklaim setelahnya.
                                @elseif(Auth::user()->isRestrictedFromClaiming())
                                    Akun Anda sedang dibatasi untuk mengklaim hingga {{ Auth::user()->claim_restricted_until->format('d M Y') }} karena riwayat tidak hadir (no-show).
                                @else
                                    Listing ini tidak dapat diklaim saat ini.
                                @endif
                            </div>
                        @endif
                    @endif

                @else
                    <!-- GUEST PROMPT -->
                    <div class="p-5 rounded-2xl glass-dock text-center space-y-3">
                        <p class="text-sm font-semibold text-[#16251D]">Ingin mengambil makanan ini?</p>
                        <div class="flex items-center justify-center gap-2.5">
                            <a href="{{ route('login') }}" class="h-9 px-4 rounded-full text-xs font-bold text-[#16251D] bg-white hover:bg-[#D7F57C]/30 border border-[#D5E0D9] hover:border-[#C8EE63] shadow-2xs hover:shadow-xs active:scale-95 transition-all inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#4E6157]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <span>Masuk</span>
                            </a>
                            <a href="{{ route('register') }}" class="h-9 px-5 rounded-full text-xs font-bold text-[#16251D] bg-[#D7F57C] hover:bg-[#C8EE63] active:bg-[#B4E634] shadow-xs hover:shadow-md active:scale-95 transition-all inline-flex items-center gap-1.5 border border-white/60">
                                <svg class="w-3.5 h-3.5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                <span>Daftar Akun Gratis</span>
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Report Issue Button -->
            <div class="text-center pt-2">
                <button type="button" onclick="document.getElementById('report-modal').showModal()" class="text-xs text-[#4E6157] hover:text-[#C93A2E] underline">
                    Laporkan listing atau masalah makanan ini
                </button>
            </div>

        </div>
    </div>

    <!-- SMART CAMERA QR SCANNER MODAL SHEET -->
    <div x-show="scannerOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="closeScanner()"
             class="glass-frosted w-full max-w-md rounded-3xl shadow-2xl border border-white/80 overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/70">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-sm text-[#16251D]">Scan QR Pengambilan Makanan</h3>
                        <p class="text-[11px] text-[#4E6157]">Arahkan kamera ke QR Code di layar pemohon</p>
                    </div>
                </div>

                <button type="button" @click="closeScanner()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-black flex items-center justify-center transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scanner Viewport Body -->
            <div class="p-4 sm:p-5 space-y-4 overflow-y-auto">
                <!-- Camera Box -->
                <div class="relative w-full aspect-square bg-gray-900 rounded-2xl overflow-hidden border-2 border-[#A8DC2E]/60 shadow-inner flex items-center justify-center">
                    <div id="qr-camera-stream" class="w-full h-full"></div>

                    <!-- Scanning Reticle Overlay -->
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="w-52 h-52 border-2 border-[#D7F57C] rounded-2xl relative shadow-[0_0_0_9999px_rgba(0,0,0,0.35)]">
                            <div class="absolute -top-1 -left-1 w-5 h-5 border-t-4 border-l-4 border-[#D7F57C] rounded-tl-lg"></div>
                            <div class="absolute -top-1 -right-1 w-5 h-5 border-t-4 border-r-4 border-[#D7F57C] rounded-tr-lg"></div>
                            <div class="absolute -bottom-1 -left-1 w-5 h-5 border-b-4 border-l-4 border-[#D7F57C] rounded-bl-lg"></div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 border-b-4 border-r-4 border-[#D7F57C] rounded-br-lg"></div>
                            <div class="w-full h-0.5 bg-[#D7F57C] shadow-[0_0_8px_#D7F57C] absolute top-1/2 -translate-y-1/2 animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Loading / Processing indicator -->
                    <div x-show="scanningLoading" class="absolute inset-0 bg-black/75 flex flex-col items-center justify-center text-white space-y-2 z-10">
                        <div class="w-8 h-8 border-3 border-[#D7F57C] border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs font-bold">Memverifikasi Serah Terima...</span>
                    </div>
                </div>

                <!-- Success Message Banner -->
                <div x-show="scanSuccessMessage" class="p-3.5 bg-[#D7F57C]/30 border border-[#C8EE63] rounded-xl text-[#16251D] text-xs font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#16251D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="scanSuccessMessage"></span>
                </div>

                <!-- Error Message Banner -->
                <div x-show="scanErrorMessage" class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="scanErrorMessage"></span>
                    </div>
                    <button type="button" @click="scanErrorMessage = null" class="text-red-700 font-bold">&times;</button>
                </div>

                <!-- Manual Code Input Section (Fallback / Alternative) -->
                <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-200/80 space-y-2">
                    <span class="text-xs font-bold text-[#16251D] block">Atau Masukkan Kode 6-Digit Manual:</span>
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-model="manualCode"
                               @keydown.enter.prevent="submitManualVerification()"
                               maxlength="10"
                               placeholder="Contoh: AB12CD"
                               class="input-field text-center font-mono font-bold uppercase tracking-widest text-sm flex-1">
                        <button type="button"
                                @click="submitManualVerification()"
                                :disabled="scanningLoading"
                                class="btn-primary text-xs px-4 py-2.5 shrink-0 cursor-pointer">
                            Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ENLARGED QR CODE MODAL SHEET -->
    <div x-show="qrModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="closeQrEnlarged()"
             class="glass-frosted w-full max-w-sm rounded-3xl shadow-2xl border border-white/80 p-6 text-center space-y-4">
            
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-[#16251D] bg-[#D7F57C]/40 px-2.5 py-0.5 rounded-full border border-[#C8EE63] font-bold">
                    QR Serah Terima
                </span>
                <button type="button" @click="closeQrEnlarged()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-black flex items-center justify-center transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <h3 class="font-display font-black text-lg text-[#16251D] leading-snug" x-text="activeQr.title"></h3>
                <p class="text-xs text-[#1E7A4C] font-semibold mt-1" x-text="`${activeQr.portions} Porsi Disetujui`"></p>
            </div>

            <!-- Big QR Image -->
            <div class="p-3 bg-white rounded-2xl border-2 border-[#1E7A4C] shadow-sm flex items-center justify-center">
                <template x-if="activeQr.dataUrl">
                    <img :src="activeQr.dataUrl" alt="QR Code Pengambilan" class="w-64 h-64 mx-auto rounded-xl">
                </template>
                <template x-if="!activeQr.dataUrl">
                    <div class="w-64 h-64 flex items-center justify-center text-xs text-[#4E6157]">Membuat QR...</div>
                </template>
            </div>

            <!-- Code Readout -->
            <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200 text-xs">
                <span class="text-[#4E6157] block text-[11px]">Kode Manual:</span>
                <span class="font-mono font-black text-xl tracking-widest text-[#14573A]" x-text="activeQr.code"></span>
            </div>

            <p class="text-[11px] text-[#4E6157] leading-relaxed">
                Tunjukkan layar ini kepada pemberi makanan. Pemberi makanan akan memindai QR ini atau memasukkan kode di atas untuk menyelesaikan serah terima.
            </p>
        </div>
    </div>

</div>

<!-- REPORT MODAL DIALOG -->
<dialog id="report-modal"
        onclick="if (event.target === this) this.close()"
        class="glass-frosted rounded-3xl p-6 sm:p-7 w-[calc(100%-2rem)] max-w-md fixed inset-0 m-auto backdrop:bg-black/60 backdrop:backdrop-blur-sm border border-white/80 shadow-2xl max-h-[90vh] overflow-y-auto">
    <form method="POST" action="{{ route('reports.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="listing_id" value="{{ $listing->id }}">
        
        <div class="flex items-start justify-between gap-3 pb-1 border-b border-gray-100">
            <div>
                <h3 class="font-display font-bold text-lg text-[#16251D]">Laporkan Masalah</h3>
                <p class="text-xs text-[#4E6157] mt-0.5">Laporan Anda membantu menjaga keselamatan dan kepercayaan komunitas pangan LokaWaste.</p>
            </div>
            <button type="button"
                    onclick="document.getElementById('report-modal').close()"
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-[#4E6157] hover:text-[#16251D] flex items-center justify-center shrink-0 transition active:scale-95 cursor-pointer"
                    title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div>
            <label class="text-xs font-bold text-[#16251D] block mb-1">Alasan Laporan</label>
            <select name="reason" class="input-field text-xs font-semibold" required>
                <option value="expired_food">Makanan sudah kedaluwarsa / tidak layak</option>
                <option value="fake_listing">Listing fiktif / tidak ada di tempat</option>
                <option value="unsafe_food">Makanan berbau / tidak higienis</option>
                <option value="harassment">Pemberi tidak sopan / mengganggu</option>
                <option value="other">Alasan lainnya</option>
            </select>
        </div>

        <div>
            <label class="text-xs font-bold text-[#16251D] block mb-1">Keterangan Tambahan</label>
            <textarea name="detail" rows="3" class="input-field text-xs" placeholder="Jelaskan secara singkat apa yang Anda temukan..."></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" onclick="document.getElementById('report-modal').close()" class="px-4 py-2 text-xs font-bold text-[#4E6157] hover:bg-gray-100 rounded-xl transition cursor-pointer">
                Batal
            </button>
            <button type="submit" class="btn-danger text-xs px-5 py-2.5 rounded-xl font-bold cursor-pointer transition active:scale-95 shadow-xs">
                Kirim Laporan
            </button>
        </div>
    </form>
</dialog>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Show mini map
    const lat = {{ $canSeeExact ? $listing->latitude : $listing->public_latitude }};
    const lng = {{ $canSeeExact ? $listing->longitude : $listing->public_longitude }};
    const isExact = {{ $canSeeExact ? 'true' : 'false' }};

    const map = L.map('listing-mini-map', {
        zoomControl: false,
        dragging: false,
        scrollWheelZoom: false,
    }).setView([lat, lng], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    if (isExact) {
        // Precise pin
        L.marker([lat, lng]).addTo(map);
    } else {
        // Privacy approximate circle (SDD 6.5)
        L.circle([lat, lng], {
            radius: 250,
            color: '#2A6F97',
            fillColor: '#2A6F97',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(map);
    }
});
</script>
@endpush

</x-layouts.app>
