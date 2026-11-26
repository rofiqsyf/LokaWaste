<x-layouts.app title="Panel Admin — LokaWaste">

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Admin Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-[#4E6157] mb-1">
                    <span>Panel Administrasi</span>
                    <span>/</span>
                    <span class="text-[#16251D] font-bold">Ringkasan Sistem</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-[#16251D]">
                    Panel Administrasi LokaWaste
                </h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1">
                    Kelola persetujuan relawan, moderasi galang makanan, pengguna, dan laporan komunitas.
                </p>
            </div>

            <x-admin-nav active="dashboard" />
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs font-semibold text-[#4E6157] block">Porsi Terselamatkan</span>
                <div class="font-display font-bold text-3xl text-[#16251D] mt-1">
                    {{ number_format($totalPortionsSaved) }}
                    <span class="text-sm font-normal text-[#84CC16]">&bull; porsi</span>
                </div>
                <span class="text-[11px] text-[#4E6157]">berhasil diambil warga</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs font-semibold text-[#4E6157] block">Total Listing Makanan</span>
                <div class="font-display font-bold text-3xl text-[#16251D] mt-1">{{ number_format($totalListings) }}</div>
                <span class="text-[11px] text-[#4E6157]">surplus terpublikasi</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs font-semibold text-[#4E6157] block">Relawan Terverifikasi</span>
                <div class="font-display font-bold text-3xl text-[#16251D] mt-1">{{ number_format($totalVolunteers) }}</div>
                <span class="text-[11px] text-[#4E6157]">terdaftar di kecamatan</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
                <span class="text-xs font-semibold text-[#4E6157] block">Total Pengguna</span>
                <div class="font-display font-bold text-3xl text-[#16251D] mt-1">{{ number_format($totalUsers) }}</div>
                <span class="text-[11px] text-[#4E6157]">akun terdaftar di sistem</span>
            </div>
        </div>

        <!-- REVIEW QUEUE (SDD 4.5: Antrean tinjauan diurutkan dari yang terlama) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 1. Antrean Pengajuan Relawan -->
            <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-white/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h2 class="font-display font-bold text-base text-[#16251D]">Antrean Pengajuan Relawan</h2>
                    </div>
                    <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">
                        {{ $pendingApplications->count() }} menunggu
                    </span>
                </div>

                <div class="p-5 flex-1 overflow-y-auto space-y-3.5 max-h-96">
                    @if($pendingApplications->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-xs font-semibold text-[#4E6157]">Tidak ada antrean pengajuan relawan yang menunggu.</p>
                        </div>
                    @else
                        @foreach($pendingApplications as $app)
                            <div class="p-4 rounded-2xl bg-[#F8FAF8] border border-gray-200/80 space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <div>
                                        <strong class="text-sm font-bold text-[#16251D]">{{ $app->user->name }}</strong>
                                        <span class="text-[#4E6157] block">{{ $app->regency->name }}</span>
                                    </div>
                                    <span class="text-[#74877B] text-[11px]">{{ $app->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-xs text-[#16251D] italic bg-white p-2.5 rounded-xl border border-gray-200/70">
                                    "{{ $app->motivation }}"
                                </p>

                                <div class="space-y-1.5 pt-1">
                                    <span class="text-[11px] font-bold text-[#4E6157] block">Keputusan per Kecamatan:</span>
                                    @foreach($app->areas as $area)
                                        <div class="p-2.5 bg-white rounded-xl border border-gray-200/70 flex items-center justify-between gap-2 text-xs">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-[#16251D]">Kec. {{ $area->district->name }}</span>
                                                <x-status-badge :status="$area->status" />
                                            </div>

                                            @if($area->status === 'pending')
                                                <form method="POST" action="{{ route('admin.volunteer-areas.decide', $area->id) }}" class="flex items-center gap-1.5">
                                                    @csrf
                                                    <button type="submit" name="action" value="approve"
                                                            class="h-7 px-3 rounded-lg text-[11px] font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 cursor-pointer shadow-2xs">
                                                        Setujui
                                                    </button>
                                                    <button type="submit" name="action" value="reject"
                                                            class="h-7 px-2.5 rounded-lg text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-[#C93A2E] border border-rose-200 transition active:scale-95 cursor-pointer">
                                                        Tolak
                                                    </button>
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
            <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-white/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h2 class="font-display font-bold text-base text-[#16251D]">Antrean Moderasi Galang Makanan</h2>
                    </div>
                    <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">
                        {{ $pendingCampaigns->count() }} menunggu
                    </span>
                </div>

                <div class="p-5 flex-1 overflow-y-auto space-y-3.5 max-h-96">
                    @if($pendingCampaigns->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-xs font-semibold text-[#4E6157]">Tidak ada galang makanan yang menunggu moderasi.</p>
                        </div>
                    @else
                        @foreach($pendingCampaigns as $camp)
                            <div class="p-4 rounded-2xl bg-[#F8FAF8] border border-gray-200/80 space-y-3">
                                <div>
                                    <span class="text-[11px] text-[#2A6F97] font-bold block">Kec. {{ $camp->district->name }}</span>
                                    <h3 class="font-display font-bold text-sm text-[#16251D]">{{ $camp->title }}</h3>
                                    <p class="text-xs text-[#4E6157] mt-0.5">Oleh {{ $camp->user->name }} • Target: {{ $camp->target_portions }} porsi</p>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-gray-200/70">
                                    <a href="{{ route('campaigns.show', $camp->slug) }}" target="_blank"
                                       class="h-7 px-3 rounded-lg text-[11px] font-semibold text-[#4E6157] hover:text-[#16251D] hover:bg-gray-200 bg-gray-100 flex items-center transition">
                                        Pratinjau Artikel
                                    </a>

                                    <form method="POST" action="{{ route('admin.campaigns.decide', $camp->id) }}" class="flex items-center gap-1.5 ml-auto">
                                        @csrf
                                        <button type="submit" name="action" value="publish"
                                                class="h-7 px-3 rounded-lg text-[11px] font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 cursor-pointer shadow-2xs">
                                            Terbitkan
                                        </button>
                                        <button type="submit" name="action" value="revision"
                                                class="h-7 px-2.5 rounded-lg text-[11px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition active:scale-95 cursor-pointer">
                                            Minta Revisi
                                        </button>
                                        <button type="submit" name="action" value="reject"
                                                class="h-7 px-2.5 rounded-lg text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-[#C93A2E] border border-rose-200 transition active:scale-95 cursor-pointer">
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
        <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="font-display font-bold text-base text-[#16251D]">Laporan Komunitas Terbuka ({{ $openReports->count() }})</h2>
                </div>
                <a href="{{ route('admin.reports') }}" class="text-xs font-bold text-[#16251D] hover:underline flex items-center gap-1">
                    <span>Lihat Semua Laporan</span>
                    <span>&rarr;</span>
                </a>
            </div>

            @if($openReports->isEmpty())
                <p class="text-xs text-[#4E6157] p-8 text-center font-medium">Tidak ada laporan komunitas yang belum ditangani.</p>
            @else
                <div class="divide-y divide-[#D5E0D9]">
                    @foreach($openReports as $rep)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs hover:bg-gray-50/50 transition">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-800">
                                        {{ str_replace('_', ' ', $rep->reason) }}
                                    </span>
                                    <span class="text-[#4E6157]">oleh <strong class="text-[#16251D]">{{ $rep->reporter->name }}</strong></span>
                                </div>
                                @if($rep->detail)
                                    <p class="text-[#16251D] mt-1 text-xs">{{ $rep->detail }}</p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.reports.resolve', $rep->id) }}">
                                @csrf
                                <button type="submit"
                                        class="h-7.5 px-3.5 rounded-xl text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 cursor-pointer shadow-2xs">
                                    Tandai Selesai
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

</x-layouts.app>
