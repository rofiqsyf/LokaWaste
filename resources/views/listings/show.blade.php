<x-layouts.app :title="$listing->title . ' — Locabite'">

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6">

    <!-- Top Navigation / Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C]">
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
    <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card overflow-hidden">
        <!-- Photo Section -->
        <div class="relative h-64 sm:h-80 bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($listing->photo_path)
                <img src="{{ asset('storage/' . $listing->photo_path) }}"
                     alt="{{ $listing->title }}"
                     class="w-full h-full object-cover">
            @else
                <div class="text-center p-6 text-[#4E6157]">
                    <div class="w-16 h-16 mx-auto mb-2 rounded-2xl bg-white/80 flex items-center justify-center text-3xl shadow-xs">
                        🍲
                    </div>
                    <span class="text-xs font-semibold">{{ $listing->category->name }}</span>
                </div>
            @endif

            <!-- Floating Time Ring on Image corner -->
            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md p-2 rounded-2xl shadow-card flex items-center gap-2">
                <x-time-ring :listing="$listing" />
                <div class="text-right pr-1">
                    <span class="text-[10px] text-[#4E6157] font-semibold uppercase block">Batas Ambil</span>
                    <span class="font-display font-bold text-sm text-[#16251D]">
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
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                                Pernyataan Halal Pemberi
                            </span>
                        @endif
                        @if($listing->is_vegetarian)
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-800">
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

                <div class="sm:text-right shrink-0 bg-[#F2F6F3] p-4 rounded-2xl border border-[#D5E0D9]">
                    <span class="text-xs text-[#4E6157] block">Sisa Makanan</span>
                    <span class="font-display font-bold text-2xl text-[#1E7A4C]">{{ $listing->portions_left }}</span>
                    <span class="text-xs text-[#4E6157]">/ {{ $listing->portions_total }} porsi</span>
                </div>
            </div>

            <!-- Campaign Priority Notice (if applicable) -->
            @if($listing->campaign_id && $listing->campaign)
                <div class="p-4 rounded-2xl bg-blue-50 border border-[#2A6F97]/30 flex items-start gap-3">
                    <span class="text-xl">🏛️</span>
                    <div class="text-xs text-[#2A6F97] space-y-1">
                        <p class="font-bold text-sm">Ditujukan untuk Galang Makanan:</p>
                        <p class="font-semibold">{{ $listing->campaign->title }} ({{ $listing->campaign->beneficiary_name }})</p>
                        @if($listing->campaign_priority_until && $listing->campaign_priority_until->isFuture())
                            <p class="text-[#7A4A00] font-bold">
                                ⏳ Diprioritaskan untuk relawan galang hingga pukul {{ $listing->campaign_priority_until->setTimezone($listing->timezone())->format('H:i') }} {{ $listing->tzLabel() }}.
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
            <div class="p-5 rounded-2xl bg-[#F2F6F3] border border-[#D5E0D9] space-y-3">
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
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-[#1E7A4C]/30 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#14573A]">
                            <svg class="w-4 h-4 text-[#1E7A4C]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm3.707 9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Titik Tepat & Alamat Dibuka
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
                <div id="listing-mini-map" class="h-48 w-full rounded-2xl overflow-hidden border border-[#D5E0D9]"></div>
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
                                                        <!-- Verify pickup code form -->
                                                        <form method="POST" action="{{ route('claims.verify-pickup', $claim->id) }}" class="flex items-center gap-1">
                                                            @csrf
                                                            <input type="text" name="pickup_code" placeholder="Kode 6 digit" maxlength="6"
                                                                   class="w-24 px-2 py-1 text-xs uppercase font-mono font-bold border rounded" required>
                                                            <button type="submit" class="btn-primary text-xs px-3 py-1">Verifikasi</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('claims.cancel', $claim->id) }}">
                                                            @csrf
                                                            <button type="submit" class="text-xs text-[#C93A2E] hover:underline" onclick="return confirm('Batalkan klaim disetujui? Porsi akan dikembalikan.')">
                                                                Batal
                                                            </button>
                                                        </form>
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
                                    <!-- UNLOCKED PICKUP CODE -->
                                    <div class="p-4 bg-white rounded-xl border-2 border-[#1E7A4C] text-center space-y-1">
                                        <span class="text-xs text-[#4E6157] font-semibold">Tunjukkan kode ini kepada pemberi saat serah terima:</span>
                                        <div class="font-mono font-bold text-3xl tracking-widest text-[#14573A]">
                                            {{ $userActiveClaim->pickup_code }}
                                        </div>
                                        <span class="text-xs text-[#1E7A4C] font-bold block">Disetujui: {{ $userActiveClaim->portions_approved }} porsi</span>
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
                                <div class="p-5 rounded-2xl bg-white border border-[#1E7A4C]/30 shadow-xs space-y-3">
                                    <h3 class="font-display font-bold text-base text-[#16251D]">Ajukan Pengambilan Makanan</h3>

                                    <div class="flex items-center gap-4">
                                        <div>
                                            <label class="text-xs font-semibold text-[#4E6157] block mb-1">Jumlah Porsi</label>
                                            <input type="number" name="portions" value="1" min="1"
                                                   max="{{ min($listing->max_portions_per_claim, $listing->portions_left) }}"
                                                   class="input-field w-28 text-center font-bold text-base" required>
                                        </div>
                                        <div class="text-xs text-[#4E6157] pt-4">
                                            Maksimal <strong class="text-[#16251D]">{{ min($listing->max_portions_per_claim, $listing->portions_left) }} porsi</strong> per klaim.
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
                    <div class="p-5 rounded-2xl bg-[#F2F6F3] text-center space-y-3">
                        <p class="text-sm font-semibold text-[#16251D]">Ingin mengambil makanan ini?</p>
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('login') }}" class="btn-secondary text-xs px-4 py-2">Masuk</a>
                            <a href="{{ route('register') }}" class="btn-primary text-xs px-4 py-2">Daftar Akun Gratis</a>
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

</div>

<!-- REPORT MODAL DIALOG -->
<dialog id="report-modal" class="rounded-3xl p-6 max-w-md w-full backdrop:bg-black/40 border border-[#D5E0D9] shadow-sheet">
    <form method="POST" action="{{ route('reports.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="listing_id" value="{{ $listing->id }}">
        <h3 class="font-display font-bold text-lg text-[#16251D]">Laporkan Masalah</h3>
        <p class="text-xs text-[#4E6157]">Laporan Anda membantu menjaga keselamatan dan kepercayaan komunitas pangan Locabite.</p>

        <div>
            <label class="text-xs font-semibold text-[#16251D] block mb-1">Alasan Laporan</label>
            <select name="reason" class="input-field text-xs" required>
                <option value="expired_food">Makanan sudah kedaluwarsa / tidak layak</option>
                <option value="fake_listing">Listing fiktif / tidak ada di tempat</option>
                <option value="unsafe_food">Makanan berbau / tidak higienis</option>
                <option value="harassment">Pemberi tidak sopan / mengganggu</option>
                <option value="other">Alasan lainnya</option>
            </select>
        </div>

        <div>
            <label class="text-xs font-semibold text-[#16251D] block mb-1">Keterangan Tambahan</label>
            <textarea name="detail" rows="3" class="input-field text-xs" placeholder="Jelaskan secara singkat apa yang Anda temukan..."></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" onclick="document.getElementById('report-modal').close()" class="px-4 py-2 text-xs font-semibold text-[#4E6157] hover:bg-gray-100 rounded-xl">
                Batal
            </button>
            <button type="submit" class="btn-danger text-xs px-4 py-2">
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
