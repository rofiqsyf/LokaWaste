<x-layouts.app :title="'Kelola Pengajuan Relawan — Admin ' . site_setting('site_name', 'LokaWaste')">

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-[#4E6157] mb-1">
                    <span>Panel Administrasi</span>
                    <span>/</span>
                    <span class="text-[#16251D] font-bold">Relawan</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-[#16251D]">Pengajuan Relawan Wilayah</h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Admin menyetujui atau menolak per kecamatan. Sebagian pengajuan boleh disetujui (SDD 2.1).</p>
            </div>

            <x-admin-nav active="volunteers" />
        </div>

        @if($applications->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9] shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-[#4E6157] flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <p class="font-bold text-sm text-[#16251D]">Belum ada pengajuan relawan</p>
                <p class="text-xs text-[#74877B] mt-0.5">Pengguna yang mendaftar menjadi relawan kecamatan akan muncul di sini.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($applications as $app)
                    <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                            <div>
                                <h2 class="font-display font-bold text-base text-[#16251D]">{{ $app->user->name }}</h2>
                                <p class="text-xs text-[#4E6157] mt-0.5">
                                    Email: {{ $app->user->email }} • 
                                    WhatsApp: 
                                    @if($app->phone || $app->user->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $app->phone ?? $app->user->phone) }}" target="_blank" class="text-[#16251D] font-bold hover:underline">
                                            {{ $app->phone ?? $app->user->phone }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                    • Domisili: {{ $app->user->district?->name ?? '-' }}
                                </p>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">
                                    {{ $app->regency->name }}
                                </span>
                                <span class="text-[11px] text-[#74877B] block mt-1">{{ $app->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>

                        <!-- Operational Profile Badges -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                            <div class="bg-[#F8FAF8] p-3 rounded-2xl border border-gray-100 space-y-0.5">
                                <span class="text-[10px] uppercase font-bold text-[#4E6157] block">Transportasi:</span>
                                <span class="font-bold text-[#16251D]">{{ $app->transportation ?? 'Sepeda Motor' }}</span>
                            </div>
                            <div class="bg-[#F8FAF8] p-3 rounded-2xl border border-gray-100 space-y-0.5">
                                <span class="text-[10px] uppercase font-bold text-[#4E6157] block">Waktu Siaga:</span>
                                <span class="font-bold text-[#16251D]">{{ $app->availability ?? 'Fleksibel' }}</span>
                            </div>
                            <div class="bg-[#F8FAF8] p-3 rounded-2xl border border-gray-100 space-y-0.5">
                                <span class="text-[10px] uppercase font-bold text-[#4E6157] block">SOP Pangan:</span>
                                <span class="font-bold text-[#16251D] flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#84CC16]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Pakta Terverifikasi
                                </span>
                            </div>
                        </div>

                        @if($app->target_beneficiaries)
                            <div class="text-xs">
                                <span class="font-bold text-[#4E6157] block mb-1">Sasaran Penerima Manfaat:</span>
                                <p class="text-[#16251D] font-medium bg-[#F8FAF8] px-3 py-1.5 rounded-xl border border-gray-200/70 inline-block">
                                    {{ $app->target_beneficiaries }}
                                </p>
                            </div>
                        @endif

                        @if($app->experience)
                            <div class="text-xs">
                                <span class="font-bold text-[#4E6157] block mb-0.5">Pengalaman Komunitas / Kerelawanan:</span>
                                <p class="text-[#4E6157] italic">{{ $app->experience }}</p>
                            </div>
                        @endif

                        <!-- Motivation -->
                        <div class="text-xs">
                            <span class="font-bold text-[#4E6157] block mb-1">Motivasi & Rencana Aksi:</span>
                            <p class="bg-[#F8FAF8] p-3 rounded-xl text-[#16251D] leading-relaxed border border-gray-100">{{ $app->motivation }}</p>
                        </div>

                        <!-- Area Decisions -->
                        <div class="border-t border-gray-100 pt-3 space-y-2">
                            <h3 class="text-xs font-bold text-[#16251D]">Keputusan Penugasan per Kecamatan:</h3>
                            <div class="space-y-2">
                                @foreach($app->areas as $area)
                                    <div class="p-3 bg-[#F8FAF8] rounded-2xl border border-gray-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-2">
                                            <strong class="text-[#16251D]">Kec. {{ $area->district->name }}</strong>
                                            <x-status-badge :status="$area->status" />
                                            @if($area->decided_at)
                                                <span class="text-[11px] text-[#74877B]">
                                                    oleh {{ $area->decidedByUser->name ?? 'Admin' }} ({{ $area->decided_at->format('d M Y') }})
                                                </span>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('admin.volunteer-areas.decide', $area->id) }}" class="flex items-center gap-2">
                                            @csrf
                                            <input type="text" name="decision_note" placeholder="Catatan (opsional)..." class="text-xs px-2.5 py-1 rounded-xl border border-gray-200 w-36 sm:w-48 bg-white focus:outline-none">
                                            @if($area->status === 'pending')
                                                <button type="submit" name="action" value="approve"
                                                        class="h-7 px-3 rounded-xl text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 cursor-pointer shadow-2xs">
                                                    Setujui
                                                </button>
                                                <button type="submit" name="action" value="reject"
                                                        class="h-7 px-2.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-[#C93A2E] border border-rose-200 transition active:scale-95 cursor-pointer">
                                                    Tolak
                                                </button>
                                            @elseif($area->status === 'approved')
                                                <button type="submit" name="action" value="revoke"
                                                        class="h-7 px-2.5 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 text-[#4E6157] transition active:scale-95 cursor-pointer"
                                                        onclick="return confirm('Cabut izin relawan di kecamatan ini?')">
                                                    Cabut Izin
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($applications->hasPages())
                    <div class="p-4 flex justify-center">
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

</x-layouts.app>
