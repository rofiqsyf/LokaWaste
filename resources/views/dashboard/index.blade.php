<x-layouts.app title="Dashboard — LokaWaste">

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="{ tab: '{{ $tab }}', ...qrHandover() }">

    <!-- User Welcome & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 glass-card p-6 rounded-3xl shadow-card">
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

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Camera QR Scanner Button for Seller / Donor -->
            <button type="button"
                    @click="openScanner()"
                    class="btn-secondary text-xs flex items-center gap-1.5 shadow-2xs py-2 px-3.5 bg-white hover:bg-gray-50 border border-gray-200 text-[#16251D]">
                <svg class="w-4 h-4 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Scan QR Serah Terima</span>
            </button>

            <a href="{{ route('listings.create') }}" class="btn-primary text-xs py-2 px-3.5">
                + Bagikan makanan
            </a>
            <a href="{{ route('home') }}" class="btn-secondary text-xs py-2 px-3.5">
                Cari makanan
            </a>
        </div>
    </div>

    <!-- Invitation Card to become Volunteer (for non-volunteers) -->
    @if(!$user->isVolunteer())
        <div x-data="{ dismissed: localStorage.getItem('lokawaste_vol_invite_dismissed') === '1' || localStorage.getItem('locabite_vol_invite_dismissed') === '1' }"
             x-show="!dismissed"
             class="p-5 rounded-3xl glass-card border border-[#C8EE63]/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#D7F57C] text-[#16251D] flex items-center justify-center shrink-0 mt-0.5 border border-[#C8EE63]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm text-[#16251D]">Ingin membantu lebih jauh di wilayahmu?</h3>
                    <p class="text-xs text-[#4E6157] mt-0.5">Daftarkan diri sebagai relawan kecamatan untuk mengelola dan menyalurkan surplus makanan ke panti asuhan dan warga rentan.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('volunteer.apply') }}" class="btn-primary text-xs py-2 px-3">
                    Ajukan diri sebagai Relawan
                </a>
                <button type="button" @click="dismissed = true; localStorage.setItem('lokawaste_vol_invite_dismissed', '1'); localStorage.setItem('locabite_vol_invite_dismissed', '1')" class="text-xs text-[#4E6157] hover:text-[#16251D] px-2 py-1">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    <!-- TABS NAVIGATION -->
    <div class="flex items-center gap-2 border-b border-[#D5E0D9] overflow-x-auto scrollbar-none pb-px">
        <button @click="tab = 'ringkasan'"
                :class="tab === 'ringkasan' ? 'border-[#16251D] text-[#16251D] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                class="px-4 py-2.5 text-sm border-b-2 transition shrink-0 cursor-pointer">
            Ringkasan
        </button>
        <button @click="tab = 'bagikan'"
                :class="tab === 'bagikan' ? 'border-[#16251D] text-[#16251D] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                class="px-4 py-2.5 text-sm border-b-2 transition shrink-0 flex items-center gap-1.5 cursor-pointer">
            <span>Yang saya bagikan</span>
            @if($incomingPendingClaims->isNotEmpty())
                <span class="w-5 h-5 rounded-full bg-[#B45309] text-white text-[11px] font-bold flex items-center justify-center">
                    {{ $incomingPendingClaims->count() }}
                </span>
            @endif
        </button>
        <button @click="tab = 'ambil'"
                :class="tab === 'ambil' ? 'border-[#16251D] text-[#16251D] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                class="px-4 py-2.5 text-sm border-b-2 transition shrink-0 cursor-pointer">
            Yang saya ambil
        </button>
        @if($user->isVolunteer())
            <button @click="tab = 'galang'"
                    :class="tab === 'galang' ? 'border-[#16251D] text-[#16251D] font-bold' : 'border-transparent text-[#4E6157] hover:text-[#16251D]'"
                    class="px-4 py-2.5 text-sm border-b-2 transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                <span>Galang saya</span>
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">Relawan</span>
            </button>
        @endif
    </div>

    <!-- TAB 1: RINGKASAN & ANALITIK DINAMIS REAL-TIME -->
    <div x-show="tab === 'ringkasan'" x-data="dashboardAnalytics(@json($analyticsData))" class="space-y-6">
        <!-- Date Filter Control Bar -->
        <div class="glass-card p-4 sm:p-5 rounded-3xl shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#A8DC2E] animate-pulse"></span>
                    <h2 class="font-display font-bold text-base text-[#16251D]">Analitik & Jejak Penyelamatan Pangan</h2>
                </div>
                <p class="text-xs text-[#4E6157] mt-0.5">Filter data analitik secara interaktif dan real-time sesuai rentang tanggal.</p>
            </div>

            <!-- Preset Filter Buttons -->
            <div class="flex flex-wrap items-center gap-1.5 glass-dock p-1.5 rounded-2xl">
                <button type="button"
                        @click="setFilter('7d')"
                        :class="filter === '7d' ? 'bg-[#D7F57C] text-[#16251D] shadow-2xs font-bold border border-[#C8EE63]' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80 font-medium'"
                        class="px-3 py-1.5 text-xs rounded-xl transition cursor-pointer">
                    7 Hari
                </button>
                <button type="button"
                        @click="setFilter('30d')"
                        :class="filter === '30d' ? 'bg-[#D7F57C] text-[#16251D] shadow-2xs font-bold border border-[#C8EE63]' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80 font-medium'"
                        class="px-3 py-1.5 text-xs rounded-xl transition cursor-pointer">
                    30 Hari
                </button>
                <button type="button"
                        @click="setFilter('month')"
                        :class="filter === 'month' ? 'bg-[#D7F57C] text-[#16251D] shadow-2xs font-bold border border-[#C8EE63]' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80 font-medium'"
                        class="px-3 py-1.5 text-xs rounded-xl transition cursor-pointer">
                    Bulan Ini
                </button>
                <button type="button"
                        @click="setFilter('all')"
                        :class="filter === 'all' ? 'bg-[#D7F57C] text-[#16251D] shadow-2xs font-bold border border-[#C8EE63]' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80 font-medium'"
                        class="px-3 py-1.5 text-xs rounded-xl transition cursor-pointer">
                    Semua Waktu
                </button>
                <button type="button"
                        @click="setFilter('custom')"
                        :class="filter === 'custom' ? 'bg-[#2A6F97] text-white shadow-2xs font-bold' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80 font-medium'"
                        class="px-3 py-1.5 text-xs rounded-xl transition flex items-center gap-1 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Kustom</span>
                </button>
            </div>
        </div>

        <!-- Custom Date Range Picker Sub-bar -->
        <div x-show="showCustomInputs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-blue-50/80 p-4 rounded-2xl border border-blue-200/70 flex flex-wrap items-center gap-3">
            <span class="text-xs font-bold text-[#2A6F97]">Rentang Tanggal Kustom:</span>
            <div class="flex items-center gap-2 text-xs">
                <label class="text-[#4E6157]">Dari:</label>
                <input type="date" x-model="customStart" class="px-2.5 py-1 rounded-lg border border-gray-300 bg-white text-xs font-bold text-[#16251D]">
            </div>
            <div class="flex items-center gap-2 text-xs">
                <label class="text-[#4E6157]">Sampai:</label>
                <input type="date" x-model="customEnd" class="px-2.5 py-1 rounded-lg border border-gray-300 bg-white text-xs font-bold text-[#16251D]">
            </div>
            <button type="button"
                    @click="applyCustomDates()"
                    class="px-3 py-1.5 bg-[#2A6F97] hover:bg-[#1E4D6B] text-white rounded-lg text-xs font-bold shadow-2xs transition active:scale-95 cursor-pointer">
                Terapkan Tanggal
            </button>
        </div>

        <!-- Dynamic Responsive Stats Strip -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card glass-card-hover p-4 sm:p-5 rounded-3xl shadow-xs">
                <div class="flex items-center justify-between text-[#4E6157] text-xs mb-1">
                    <span>Porsi Dibagikan</span>
                    <svg class="w-4 h-4 text-[#1E7A4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div class="font-display font-black text-2xl sm:text-3xl text-[#1E7A4C]" x-text="metrics.portionsShared"></div>
                <span class="text-[11px] text-[#4E6157]" x-text="`${metrics.totalListings} postingan pada periode ini`"></span>
            </div>

            <div class="glass-card glass-card-hover p-4 sm:p-5 rounded-3xl shadow-xs">
                <div class="flex items-center justify-between text-[#4E6157] text-xs mb-1">
                    <span>Porsi Diselamatkan</span>
                    <svg class="w-4 h-4 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="font-display font-black text-2xl sm:text-3xl text-[#2A6F97]" x-text="metrics.portionsTaken"></div>
                <span class="text-[11px] text-[#4E6157]">berhasil diambil pemohon</span>
            </div>

            <div class="glass-card glass-card-hover p-4 sm:p-5 rounded-3xl shadow-xs">
                <div class="flex items-center justify-between text-[#4E6157] text-xs mb-1">
                    <span>Emisi Karbon Dicegah</span>
                    <svg class="w-4 h-4 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="font-display font-black text-2xl sm:text-3xl text-[#16251D]">
                    <span x-text="metrics.co2Avoided"></span> <span class="text-xs font-normal text-[#4E6157]">kg CO₂e</span>
                </div>
                <span class="text-[11px] text-[#4E6157]">setara mencegah timbunan TPA</span>
            </div>

            <div class="glass-card glass-card-hover p-4 sm:p-5 rounded-3xl shadow-xs">
                <div class="flex items-center justify-between text-[#4E6157] text-xs mb-1">
                    <span>Tingkat Keberhasilan</span>
                    <svg class="w-4 h-4 text-[#B45309]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="font-display font-black text-2xl sm:text-3xl text-[#B45309]">
                    <span x-text="metrics.completionRate"></span>%
                </div>
                <span class="text-[11px] text-[#4E6157]">rasio serah terima sukses</span>
            </div>
        </div>

        <!-- Professional Dual Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 1: Daily Trend Line Chart (Wide) -->
            <div class="lg:col-span-2 glass-card p-5 sm:p-6 rounded-3xl shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-display font-bold text-base text-[#16251D]">Tren Penyelamatan Pangan Harian</h3>
                        <p class="text-xs text-[#4E6157] mt-0.5">Perbandingan porsi makanan yang dibagikan dan yang diselamatkan.</p>
                    </div>
                </div>
                <div class="relative h-64 sm:h-72 w-full">
                    <canvas id="analytics-trend-chart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Category Donut Chart -->
            <div class="glass-card p-5 sm:p-6 rounded-3xl shadow-card space-y-4">
                <div>
                    <h3 class="font-display font-bold text-base text-[#16251D]">Distribusi Kategori Pangan</h3>
                    <p class="text-xs text-[#4E6157] mt-0.5">Proporsi jenis pangan pada periode terpilih.</p>
                </div>
                <div class="relative h-64 sm:h-72 w-full flex items-center justify-center">
                    <canvas id="analytics-category-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Personal Impact Engine & ESG Scoreboard (design.md 1.F & 5.3) -->
        <div class="glass-card p-6 rounded-3xl border border-[#C8EE63]/70 shadow-card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#D5E0D9] pb-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#16251D] bg-[#D7F57C] px-2.5 py-0.5 rounded-full inline-block mb-1 border border-[#C8EE63]">
                        Personal Impact Engine & ESG
                    </span>
                    <h3 class="font-display font-bold text-lg text-[#16251D]">Dampak Kumulatif Penyelamatan Pangan</h3>
                    <p class="text-xs text-[#4E6157]">Kontribusi nyata Anda terhadap pencegahan emisi karbon dan pelestarian sumber daya bumi.</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-xs font-bold text-[#16251D] bg-[#D7F57C]/40 border border-[#C8EE63] px-3 py-1.5 rounded-xl inline-block">
                        {{ $impactLevel }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="glass-pill p-4 rounded-2xl">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-semibold text-[#4E6157]">Emisi CO₂e Tercegah</span>
                    </div>
                    <div class="font-display font-bold text-2xl text-[#16251D]"><span x-text="metrics.co2Avoided"></span> <span class="text-xs font-normal text-[#4E6157]">kg CO₂e</span></div>
                    <p class="text-[11px] text-[#4E6157] mt-1">Estimasi 0.85 kg emisi per porsi makanan terbuang.</p>
                </div>

                <div class="glass-pill p-4 rounded-2xl">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-[#2A6F97]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span class="text-xs font-semibold text-[#4E6157]">Air Bersih Terhemat</span>
                    </div>
                    <div class="font-display font-bold text-2xl text-[#2A6F97]"><span x-text="metrics.waterSaved.toLocaleString('id-ID')"></span> <span class="text-xs font-normal text-[#4E6157]">Liter</span></div>
                    <p class="text-[11px] text-[#4E6157] mt-1">Estimasi 320L jejak air pada rantai pasok pangan.</p>
                </div>

                <div class="glass-pill p-4 rounded-2xl">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-[#B45309]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-semibold text-[#4E6157]">Nilai Ekonomi Pangan</span>
                    </div>
                    <div class="font-display font-bold text-2xl text-[#B45309]">Rp <span x-text="metrics.economicValue.toLocaleString('id-ID')"></span></div>
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
        <div class="glass-card rounded-3xl shadow-xs overflow-hidden">
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
            <div class="glass-card p-10 rounded-3xl text-center">
                <p class="text-xs text-[#4E6157]">Anda belum pernah mengajukan klaim makanan.</p>
                <a href="{{ route('home') }}" class="btn-secondary text-xs mt-3 inline-flex">Cari Makanan di Peta</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($myClaims as $claim)
                    <div class="glass-card glass-card-hover p-5 rounded-3xl shadow-xs space-y-4">
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
                            <!-- Smart QR Code & Pickup Code Handover Box -->
                            <div class="p-4 bg-[#D7F57C]/25 rounded-2xl border-2 border-[#C8EE63] flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                                <div class="w-28 h-28 shrink-0 bg-white p-1.5 rounded-2xl border border-[#C8EE63] shadow-2xs flex items-center justify-center cursor-pointer group hover:scale-105 transition-transform"
                                     @click="openQrEnlarged({{ $claim->id }}, '{{ $claim->pickup_code }}', '{{ addslashes($claim->listing->title) }}', {{ $claim->portions_approved ?? $claim->portions_requested }})"
                                     title="Ketuk untuk perbesar QR Code">
                                    <div data-lokawaste-qr="LOKAWASTE:CLAIM:{{ $claim->id }}:{{ $claim->pickup_code }}" data-locabite-qr="LOCABITE:CLAIM:{{ $claim->id }}:{{ $claim->pickup_code }}" class="w-full h-full flex items-center justify-center">
                                        <div class="w-5 h-5 border-2 border-[#16251D] border-t-transparent rounded-full animate-spin"></div>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0 space-y-1.5">
                                    <span class="text-[11px] font-bold text-[#14573A] block">Tunjukkan QR atau Kode kepada Pemberi:</span>
                                    <div class="flex items-center justify-center sm:justify-start gap-2">
                                        <div class="font-mono font-black text-2xl tracking-widest text-[#16251D] bg-white px-3 py-0.5 rounded-lg border border-[#C8EE63]">
                                            {{ $claim->pickup_code }}
                                        </div>
                                        <button type="button"
                                                x-data="{ copied: false }"
                                                @click="navigator.clipboard?.writeText('{{ $claim->pickup_code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="p-1.5 rounded-lg text-[#16251D] bg-white hover:bg-[#D7F57C]/40 border border-[#C8EE63] transition cursor-pointer flex items-center gap-1 text-[10px] font-bold"
                                                title="Salin kode">
                                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <span x-show="copied" class="text-[#1E7A4C]">Tersalin!</span>
                                        </button>
                                    </div>
                                    <button type="button"
                                            @click="openQrEnlarged({{ $claim->id }}, '{{ $claim->pickup_code }}', '{{ addslashes($claim->listing->title) }}', {{ $claim->portions_approved ?? $claim->portions_requested }})"
                                            class="text-xs font-bold text-[#2A6F97] hover:underline flex items-center justify-center sm:justify-start gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                        <span>Perbesar QR Layar Penuh</span>
                                    </button>
                                    <span class="text-[11px] text-[#4E6157] block truncate">{{ $claim->listing->pickup_address }}</span>
                                </div>
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
                    <dialog id="review-modal-{{ $claim->id }}"
                            onclick="if (event.target === this) this.close()"
                            class="glass-frosted rounded-3xl p-6 sm:p-7 w-[calc(100%-2rem)] max-w-md fixed inset-0 m-auto backdrop:bg-black/60 backdrop:backdrop-blur-sm border border-white/80 shadow-2xl max-h-[90vh] overflow-y-auto">
                        <form method="POST" action="{{ route('claims.review', $claim->id) }}" class="space-y-4">
                            @csrf
                            <div class="flex items-start justify-between gap-3 pb-1 border-b border-gray-100">
                                <div>
                                    <h3 class="font-display font-bold text-lg text-[#16251D]">Beri Ulasan Makanan</h3>
                                    <p class="text-xs text-[#4E6157] mt-0.5">Bagikan pengalaman serah terima Anda untuk menjaga kualitas komunitas pangan.</p>
                                </div>
                                <button type="button"
                                        onclick="document.getElementById('review-modal-{{ $claim->id }}').close()"
                                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-[#4E6157] hover:text-[#16251D] flex items-center justify-center shrink-0 transition active:scale-95 cursor-pointer"
                                        title="Tutup">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-[#16251D] block mb-1">Rating Kepuasan</label>
                                <select name="rating" class="input-field text-xs font-semibold" required>
                                    <option value="5">5 / 5 Bintang — Sangat Baik &amp; Bersih</option>
                                    <option value="4">4 / 5 Bintang — Baik &amp; Layak</option>
                                    <option value="3">3 / 5 Bintang — Cukup</option>
                                    <option value="2">2 / 5 Bintang — Kurang Memuaskan</option>
                                    <option value="1">1 / 5 Bintang — Buruk / Tidak Layak</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-[#16251D] block mb-1">Komentar Singkat</label>
                                <textarea name="comment" rows="3" class="input-field text-xs" placeholder="Ceritakan pengalaman serah terima Anda..."></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" onclick="document.getElementById('review-modal-{{ $claim->id }}').close()" class="px-4 py-2 text-xs font-bold text-[#4E6157] hover:bg-gray-100 rounded-xl transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="btn-primary text-xs px-5 py-2.5 rounded-full font-bold transition active:scale-95 shadow-xs cursor-pointer">
                                    Kirim Ulasan
                                </button>
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
                <div class="glass-card p-10 rounded-3xl text-center">
                    <p class="text-xs text-[#4E6157]">Anda belum memiliki artikel galang makanan.</p>
                    <a href="{{ route('volunteer.campaigns.create') }}" class="btn-primary text-xs mt-3 inline-flex">Buat Galang Pertama</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($myCampaigns as $camp)
                        <div class="glass-card glass-card-hover p-5 rounded-3xl shadow-xs space-y-3">
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
             class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]">
            
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
             class="glass-card w-full max-w-sm rounded-3xl shadow-2xl border border-white/80 p-6 text-center space-y-4">
            
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

</x-layouts.app>
