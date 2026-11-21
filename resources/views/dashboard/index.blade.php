<x-layouts.app title="Dashboard — Locabite">

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="{ tab: '{{ $tab }}' }">

    <!-- User Welcome & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-[#D5E0D9] shadow-card">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="font-display font-bold text-2xl text-[#16251D]">Halo, {{ $user->name }}</h1>
                @if($user->isVolunteer())
                    <x-volunteer-badge label="Relawan Wilayah" />
                @endif
                @if($user->is_verified)
                    <span class="badge-verified text-xs">Terverifikasi</span>
                @endif
            </div>
            <p class="text-xs text-[#4E6157] mt-1">Satu akun untuk membagikan dan mengambil makanan berlebih.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('listings.create') }}" class="btn-primary text-xs">
                + Bagikan makanan
            </a>
            <a href="{{ route('home') }}" class="btn-secondary text-xs">
                Cari makanan
            </a>
        </div>
    </div>

    <!-- Invitation Card to become Volunteer (for non-volunteers) -->
    @if(!$user->isVolunteer())
        <div x-data="{ dismissed: localStorage.getItem('locabite_vol_invite_dismissed') === '1' }"
             x-show="!dismissed"
             class="p-5 rounded-3xl bg-linear-to-r from-emerald-50 to-teal-50 border border-[#1E7A4C]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100/80 text-[#1E7A4C] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm text-[#14573A]">Ingin membantu lebih jauh di wilayahmu?</h3>
                    <p class="text-xs text-[#4E6157] mt-0.5">Daftarkan diri sebagai relawan kecamatan untuk mengelola dan menyalurkan surplus makanan ke panti asuhan dan warga rentan.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('volunteer.apply') }}" class="btn-primary text-xs py-2 px-3">
                    Ajukan diri sebagai Relawan
                </a>
                <button type="button" @click="dismissed = true; localStorage.setItem('locabite_vol_invite_dismissed', '1')" class="text-xs text-[#4E6157] hover:text-[#16251D] px-2 py-1">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    <!-- TABS NAVIGATION -->
    <div class="flex items-center gap-2 border-b border-[#D5E0D9] overflow-x-auto scrollbar-none pb-px">
        <button @click="tab = 'ringkasan'"
                :class="tab === 'ringkasan' ? 'border-[#1E7A4C] text-[#1E7A4C] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                class="px-4 py-2.5 text-sm border-b-2 transition shrink-0">
            Ringkasan
        </button>
        <button @click="tab = 'bagikan'"
                :class="tab === 'bagikan' ? 'border-[#1E7A4C] text-[#1E7A4C] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                class="px-4 py-2.5 text-sm border-b-2 transition shrink-0 flex items-center gap-1.5">
            <span>Yang saya bagikan</span>
            @if($incomingPendingClaims->isNotEmpty())
                <span class="w-5 h-5 rounded-full bg-[#B45309] text-white text-[11px] font-bold flex items-center justify-center">
                    {{ $incomingPendingClaims->count() }}
                </span>
            @endif
        </button>
        <button @click="tab = 'ambil'"
                :class="tab === 'ambil' ? 'border-[#1E7A4C] text-[#1E7A4C] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                class="px-4 py-2.5 text-sm border-b-2 transition shrink-0">
            Yang saya ambil
        </button>
        @if($user->isVolunteer())
            <button @click="tab = 'galang'"
                    :class="tab === 'galang' ? 'border-[#1E7A4C] text-[#1E7A4C] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                    class="px-4 py-2.5 text-sm border-b-2 transition shrink-0 flex items-center gap-1.5">
                <span>Galang saya</span>
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 text-[#2A6F97]">Relawan</span>
            </button>
        @endif
    </div>

    <!-- TAB 1: RINGKASAN -->
    <div x-show="tab === 'ringkasan'" class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs text-[#4E6157] block">Porsi Makanan Dibagikan</span>
                <div class="font-display font-bold text-3xl text-[#1E7A4C] mt-1">{{ $portionsShared }}</div>
                <span class="text-[11px] text-[#4E6157]">dari {{ $myListings->count() }} postingan</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs text-[#4E6157] block">Porsi Makanan Diambil</span>
                <div class="font-display font-bold text-3xl text-[#2A6F97] mt-1">{{ $portionsTaken }}</div>
                <span class="text-[11px] text-[#4E6157]">berhasil diselamatkan</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs text-[#4E6157] block">Klaim Masuk Menunggu</span>
                <div class="font-display font-bold text-3xl text-[#B45309] mt-1">{{ $incomingPendingClaims->count() }}</div>
                <span class="text-[11px] text-[#4E6157]">membutuhkan keputusan Anda</span>
            </div>
        </div>

        <!-- Personal Impact Engine & ESG Scoreboard (design.md 1.F & 5.3) -->
        <div class="bg-linear-to-br from-white to-[#F2F7F4] p-6 rounded-3xl border border-[#1E7A4C]/30 shadow-card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#D5E0D9] pb-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#1E7A4C] bg-emerald-100 px-2.5 py-0.5 rounded-full inline-block mb-1">
                        Personal Impact Engine & ESG
                    </span>
                    <h3 class="font-display font-bold text-lg text-[#16251D]">Dampak Penyelamatan Pangan Anda</h3>
                    <p class="text-xs text-[#4E6157]">Kontribusi nyata Anda terhadap pencegahan emisi karbon dan pelestarian sumber daya bumi.</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-xs font-bold text-[#14573A] bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl inline-block">
                        {{ $impactLevel }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-[#D5E0D9]">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-semibold text-[#4E6157]">Emisi CO₂e Tercegah</span>
                    </div>
                    <div class="font-display font-bold text-2xl text-[#1E7A4C]">{{ $co2Avoided }} <span class="text-xs font-normal text-[#4E6157]">kg CO₂e</span></div>
                    <p class="text-[11px] text-[#4E6157] mt-1">Estimasi 0.85 kg emisi per porsi makanan terbuang.</p>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-[#D5E0D9]">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span class="text-xs font-semibold text-[#4E6157]">Air Bersih Terhemat</span>
                    </div>
                    <div class="font-display font-bold text-2xl text-[#2A6F97]">{{ number_format($waterSaved, 0, ',', '.') }} <span class="text-xs font-normal text-[#4E6157]">Liter</span></div>
                    <p class="text-[11px] text-[#4E6157] mt-1">Estimasi 320L jejak air pada rantai pasok pangan.</p>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-[#D5E0D9]">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-[#B45309]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-semibold text-[#4E6157]">Nilai Ekonomi Pangan</span>
                    </div>
                    <div class="font-display font-bold text-2xl text-[#B45309]">Rp {{ number_format($economicValue, 0, ',', '.') }}</div>
                    <p class="text-[11px] text-[#4E6157] mt-1">Nilai makanan layak yang teralokasikan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: YANG SAYA BAGIKAN -->
    <div x-show="tab === 'bagikan'" class="space-y-6">
        <!-- Incoming Claims Section on Top (Time Sensitive!) -->
        @if($incomingPendingClaims->isNotEmpty())
            <div class="bg-amber-50/70 p-5 rounded-3xl border border-amber-200 space-y-3">
                <h2 class="font-display font-bold text-sm text-[#7A4A00] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#B45309]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Klaim Masuk Menunggu Persetujuan Anda</span>
                </h2>

                <div class="space-y-2">
                    @foreach($incomingPendingClaims as $claim)
                        <div class="p-3.5 bg-white rounded-2xl border border-[#D5E0D9] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-xs">
                            <div>
                                <div class="flex items-center gap-2">
                                    <strong class="text-sm text-[#16251D]">{{ $claim->user->name }}</strong>
                                    <span class="text-[#4E6157]">pada</span>
                                    <strong class="text-[#1E7A4C]">{{ $claim->listing->title }}</strong>
                                </div>
                                <p class="text-[#4E6157] mt-0.5">
                                    Meminta <strong class="text-[#1E7A4C]">{{ $claim->portions_requested }} porsi</strong> (sisa listing: {{ $claim->listing->portions_left }} porsi)
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('claims.approve', $claim->id) }}" class="flex items-center gap-1">
                                    @csrf
                                    <input type="number" name="portions" value="{{ min($claim->portions_requested, $claim->listing->portions_left) }}"
                                           min="1" max="{{ min($claim->portions_requested, $claim->listing->portions_left) }}"
                                           class="w-16 px-2 py-1 text-xs border rounded">
                                    <button type="submit" class="btn-primary text-xs px-3 py-1">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('claims.reject', $claim->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-danger text-xs px-2.5 py-1">Tolak</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- My Listings Table -->
        <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
            <div class="p-5 border-b border-[#D5E0D9] flex items-center justify-between">
                <h2 class="font-display font-bold text-base text-[#16251D]">Daftar Makanan yang Saya Bagikan</h2>
                <a href="{{ route('listings.create') }}" class="btn-primary text-xs py-1.5 px-3">
                    + Bagikan Baru
                </a>
            </div>

            @if($myListings->isEmpty())
                <div class="p-10 text-center">
                    <p class="text-xs text-[#4E6157]">Belum ada makanan yang Anda bagikan.</p>
                    <a href="{{ route('listings.create') }}" class="btn-primary text-xs mt-3 inline-flex">Buat Listing Pertama dalam 30 Detik</a>
                </div>
            @else
                <div class="divide-y divide-[#D5E0D9]">
                    @foreach($myListings as $listing)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <x-time-ring :listing="$listing" />
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <h3 class="font-display font-bold text-sm text-[#16251D]">{{ $listing->title }}</h3>
                                        <x-status-badge :status="$listing->status" />
                                    </div>
                                    <p class="text-xs text-[#4E6157]">
                                        Tersisa <strong class="text-[#1E7A4C]">{{ $listing->portions_left }}</strong> dari {{ $listing->portions_total }} porsi •
                                        {{ $listing->claims->count() }} klaim diajukan
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <a href="{{ route('listings.show', $listing->id) }}" class="btn-secondary text-xs px-3 py-1.5">
                                    Kelola & Detail
                                </a>
                                <a href="{{ route('listings.duplicate', $listing->id) }}" class="text-xs text-[#1E7A4C] hover:underline px-2 py-1">
                                    Bagikan lagi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: YANG SAYA AMBIL -->
    <div x-show="tab === 'ambil'" class="space-y-6">
        <h2 class="font-display font-bold text-base text-[#16251D]">Riwayat Pengambilan Makanan</h2>

        @if($myClaims->isEmpty())
            <div class="bg-white p-10 rounded-3xl border border-[#D5E0D9] text-center">
                <p class="text-xs text-[#4E6157]">Anda belum pernah mengajukan klaim makanan.</p>
                <a href="{{ route('home') }}" class="btn-secondary text-xs mt-3 inline-flex">Cari Makanan di Peta</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($myClaims as $claim)
                    <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-[#4E6157]">{{ $claim->created_at->format('d M Y, H:i') }}</span>
                            <x-status-badge :status="$claim->status" />
                        </div>

                        <div>
                            <h3 class="font-display font-bold text-base text-[#16251D]">{{ $claim->listing->title }}</h3>
                            <p class="text-xs text-[#4E6157] mt-0.5">Pemberi: {{ $claim->listing->user->organization_name ?? $claim->listing->user->name }}</p>
                            <p class="text-xs text-[#1E7A4C] font-semibold mt-1">
                                Porsi: {{ $claim->portions_approved ?? $claim->portions_requested }} porsi
                            </p>
                        </div>

                        @if($claim->status === 'approved')
                            <!-- Big Pickup Code Box -->
                            <div class="p-4 bg-emerald-50 rounded-2xl border-2 border-[#1E7A4C] text-center">
                                <span class="text-[11px] text-[#14573A] font-bold block">Tunjukkan kode ini saat mengambil makanan:</span>
                                <div class="font-mono font-bold text-3xl tracking-widest text-[#14573A] my-1">
                                    {{ $claim->pickup_code }}
                                </div>
                                <span class="text-xs text-[#4E6157] block">{{ $claim->listing->pickup_address }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-2 border-t border-[#D5E0D9]">
                            <a href="{{ route('listings.show', $claim->listing_id) }}" class="text-xs font-bold text-[#1E7A4C] hover:underline">
                                Lihat Listing &rarr;
                            </a>

                            @if($claim->status === 'picked_up' && !$claim->review)
                                <!-- Review button -->
                                <button type="button" onclick="document.getElementById('review-modal-{{ $claim->id }}').showModal()" class="btn-secondary text-xs px-3 py-1">
                                    Beri Ulasan
                                </button>
                            @elseif($claim->review)
                                <span class="text-xs text-amber-600 font-bold inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>{{ $claim->review->rating }} / 5</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- REVIEW DIALOG -->
                    <dialog id="review-modal-{{ $claim->id }}" class="rounded-3xl p-6 max-w-sm w-full backdrop:bg-black/40 border border-[#D5E0D9] shadow-sheet">
                        <form method="POST" action="{{ route('claims.review', $claim->id) }}" class="space-y-4">
                            @csrf
                            <h3 class="font-display font-bold text-base text-[#16251D]">Beri Ulasan Makanan</h3>
                            <div>
                                <label class="text-xs font-semibold block mb-1">Rating (1-5)</label>
                                <select name="rating" class="input-field text-xs" required>
                                    <option value="5">⭐⭐⭐⭐⭐ Sangat Baik & Bersih</option>
                                    <option value="4">⭐⭐⭐⭐ Baik</option>
                                    <option value="3">⭐⭐⭐ Cukup</option>
                                    <option value="2">⭐⭐ Kurang</option>
                                    <option value="1">⭐ Buruk</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold block mb-1">Komentar Singkat</label>
                                <textarea name="comment" rows="2" class="input-field text-xs" placeholder="Ceritakan pengalaman serah terima Anda..."></textarea>
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" onclick="document.getElementById('review-modal-{{ $claim->id }}').close()" class="px-3 py-1.5 text-xs font-semibold text-[#4E6157]">Batal</button>
                                <button type="submit" class="btn-primary text-xs px-3 py-1.5">Kirim Ulasan</button>
                            </div>
                        </form>
                    </dialog>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 4: GALANG SAYA (Volunteers only) -->
    @if($user->isVolunteer())
        <div x-show="tab === 'galang'" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display font-bold text-base text-[#16251D]">Galang Makanan yang Anda Kelola</h2>
                    <p class="text-xs text-[#4E6157]">Inisiatif pangan untuk panti asuhan, ponpes, dan tunawisma di kecamatan binaan Anda.</p>
                </div>
                <a href="{{ route('volunteer.campaigns.create') }}" class="btn-primary text-xs">
                    + Buat Galang Baru
                </a>
            </div>

            @if($myCampaigns->isEmpty())
                <div class="bg-white p-10 rounded-3xl border border-[#D5E0D9] text-center">
                    <p class="text-xs text-[#4E6157]">Anda belum memiliki artikel galang makanan.</p>
                    <a href="{{ route('volunteer.campaigns.create') }}" class="btn-primary text-xs mt-3 inline-flex">Buat Galang Pertama</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($myCampaigns as $camp)
                        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-[#2A6F97]">
                                    Kec. {{ $camp->district->name }}
                                </span>
                                <x-status-badge :status="$camp->status" />
                            </div>

                            <h3 class="font-display font-bold text-base text-[#16251D]">{{ $camp->title }}</h3>
                            <p class="text-xs text-[#4E6157]">Tujuan: {{ $camp->beneficiary_name }}</p>

                            <!-- Progress -->
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-[#1E7A4C] h-2 rounded-full" style="width: {{ $camp->progressPercent() }}%"></div>
                            </div>
                            <span class="text-xs text-[#4E6157] block">
                                {{ $camp->collected_portions }} dari {{ $camp->target_portions }} porsi terkumpul ({{ $camp->progressPercent() }}%)
                            </span>

                            <div class="flex items-center justify-between pt-2 border-t border-[#D5E0D9]">
                                <a href="{{ route('campaigns.show', $camp->slug) }}" class="text-xs font-bold text-[#2A6F97] hover:underline">
                                    Buka Halaman Publik &rarr;
                                </a>

                                @if($camp->status === 'draft')
                                    <form method="POST" action="{{ route('volunteer.campaigns.submit', $camp->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-primary text-xs px-3 py-1">
                                            Ajukan Tinjauan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>

</x-layouts.app>
