<x-layouts.app title="Panel Admin — Locabite">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Admin Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-display font-bold text-3xl text-[#16251D]">Panel Administrasi Locabite</h1>
            <p class="text-sm text-[#4E6157] mt-1">Kelola persetujuan relawan, moderasi galang makanan, pengguna, dan laporan.</p>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1">
            <a href="{{ route('admin.volunteer-applications') }}" class="btn-secondary text-xs py-2 px-3 shrink-0">
                Pengajuan Relawan
            </a>
            <a href="{{ route('admin.campaigns') }}" class="btn-secondary text-xs py-2 px-3 shrink-0">
                Moderasi Galang
            </a>
            <a href="{{ route('admin.reports') }}" class="btn-secondary text-xs py-2 px-3 shrink-0">
                Laporan
            </a>
            <a href="{{ route('admin.users') }}" class="btn-secondary text-xs py-2 px-3 shrink-0">
                Pengguna
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs text-[#4E6157] block">Porsi Terselamatkan</span>
            <div class="font-display font-bold text-3xl text-[#1E7A4C] mt-1">{{ $totalPortionsSaved }}</div>
            <span class="text-[11px] text-[#4E6157]">porsi berhasil diambil</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs text-[#4E6157] block">Total Listing Makanan</span>
            <div class="font-display font-bold text-3xl text-[#16251D] mt-1">{{ $totalListings }}</div>
            <span class="text-[11px] text-[#4E6157]">surplus terpublikasi</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs text-[#4E6157] block">Relawan Aktif</span>
            <div class="font-display font-bold text-3xl text-[#2A6F97] mt-1">{{ $totalVolunteers }}</div>
            <span class="text-[11px] text-[#4E6157]">terdaftar di kecamatan</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs text-[#4E6157] block">Total Pengguna</span>
            <div class="font-display font-bold text-3xl text-[#16251D] mt-1">{{ $totalUsers }}</div>
            <span class="text-[11px] text-[#4E6157]">akun terdaftar</span>
        </div>
    </div>

    <!-- REVIEW QUEUE (SDD 4.5: Antrean tinjauan diurutkan dari yang terlama) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. Antrean Pengajuan Relawan -->
        <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card overflow-hidden flex flex-col">
            <div class="p-5 border-b border-[#D5E0D9] flex items-center justify-between bg-amber-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⏳</span>
                    <h2 class="font-display font-bold text-base text-[#7A4A00]">Antrean Pengajuan Relawan</h2>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-200 text-amber-900">
                    {{ $pendingApplications->count() }} menunggu
                </span>
            </div>

            <div class="p-5 flex-1 overflow-y-auto space-y-4 max-h-96">
                @if($pendingApplications->isEmpty())
                    <p class="text-xs text-[#4E6157] text-center py-6">Tidak ada antrean pengajuan relawan yang menunggu.</p>
                @else
                    @foreach($pendingApplications as $app)
                        <div class="p-4 rounded-2xl bg-[#F2F6F3] border border-[#D5E0D9] space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <strong class="text-sm text-[#16251D]">{{ $app->user->name }}</strong>
                                    <span class="text-[#4E6157] block">{{ $app->regency->name }}</span>
                                </div>
                                <span class="text-[#4E6157]">{{ $app->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="text-xs text-[#16251D] italic bg-white p-2.5 rounded-xl border border-gray-200">
                                "{{ $app->motivation }}"
                            </p>

                            <div class="space-y-1.5 pt-1">
                                <span class="text-[11px] font-bold text-[#4E6157] block">Keputusan per Kecamatan:</span>
                                @foreach($app->areas as $area)
                                    <div class="p-2 bg-white rounded-xl border border-gray-200 flex items-center justify-between gap-2 text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-[#16251D]">Kec. {{ $area->district->name }}</span>
                                            <x-status-badge :status="$area->status" />
                                        </div>

                                        @if($area->status === 'pending')
                                            <form method="POST" action="{{ route('admin.volunteer-areas.decide', $area->id) }}" class="flex items-center gap-1.5">
                                                @csrf
                                                <button type="submit" name="action" value="approve" class="btn-primary text-[11px] py-1 px-2.5">Setujui</button>
                                                <button type="submit" name="action" value="reject" class="btn-danger text-[11px] py-1 px-2">Tolak</button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- 2. Antrean Moderasi Galang Makanan -->
        <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card overflow-hidden flex flex-col">
            <div class="p-5 border-b border-[#D5E0D9] flex items-center justify-between bg-blue-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🏛️</span>
                    <h2 class="font-display font-bold text-base text-[#2A6F97]">Antrean Moderasi Galang Makanan</h2>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-200 text-blue-900">
                    {{ $pendingCampaigns->count() }} menunggu
                </span>
            </div>

            <div class="p-5 flex-1 overflow-y-auto space-y-4 max-h-96">
                @if($pendingCampaigns->isEmpty())
                    <p class="text-xs text-[#4E6157] text-center py-6">Tidak ada galang makanan yang menunggu moderasi.</p>
                @else
                    @foreach($pendingCampaigns as $camp)
                        <div class="p-4 rounded-2xl bg-[#F2F6F3] border border-[#D5E0D9] space-y-3">
                            <div>
                                <span class="text-[11px] text-[#2A6F97] font-bold block">Kec. {{ $camp->district->name }}</span>
                                <h3 class="font-display font-bold text-sm text-[#16251D]">{{ $camp->title }}</h3>
                                <p class="text-xs text-[#4E6157] mt-0.5">Oleh {{ $camp->user->name }} • Target: {{ $camp->target_portions }} porsi</p>
                            </div>

                            <div class="flex items-center gap-2 pt-2 border-t border-gray-200">
                                <a href="{{ route('campaigns.show', $camp->slug) }}" class="btn-secondary text-[11px] py-1 px-2.5">
                                    Pratinjau Artikel
                                </a>

                                <form method="POST" action="{{ route('admin.campaigns.decide', $camp->id) }}" class="flex items-center gap-1.5 ml-auto">
                                    @csrf
                                    <button type="submit" name="action" value="publish" class="btn-primary text-[11px] py-1 px-2.5">
                                        Terbitkan
                                    </button>
                                    <button type="submit" name="action" value="revision" class="px-2 py-1 bg-amber-500 text-white rounded-lg text-[11px] font-bold">
                                        Minta Revisi
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn-danger text-[11px] py-1 px-2">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

    <!-- 3. Laporan Masuk -->
    <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card overflow-hidden">
        <div class="p-5 border-b border-[#D5E0D9] flex items-center justify-between">
            <h2 class="font-display font-bold text-base text-[#16251D]">Laporan Warga Terbuka ({{ $openReports->count() }})</h2>
            <a href="{{ route('admin.reports') }}" class="text-xs font-bold text-[#1E7A4C] hover:underline">
                Lihat Semua Laporan &rarr;
            </a>
        </div>

        @if($openReports->isEmpty())
            <p class="text-xs text-[#4E6157] p-8 text-center">Tidak ada laporan warga yang belum ditangani.</p>
        @else
            <div class="divide-y divide-[#D5E0D9]">
                @foreach($openReports as $rep)
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <strong class="text-[#C93A2E] uppercase font-bold">{{ str_replace('_', ' ', $rep->reason) }}</strong>
                                <span class="text-[#4E6157]">oleh {{ $rep->reporter->name }}</span>
                            </div>
                            @if($rep->detail)
                                <p class="text-[#16251D] mt-1">{{ $rep->detail }}</p>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.reports.resolve', $rep->id) }}">
                            @csrf
                            <button type="submit" class="btn-primary text-xs py-1 px-3">
                                Tandai Selesai
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

</x-layouts.app>
