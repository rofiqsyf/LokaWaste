<x-layouts.app title="Kelola Pengajuan Relawan — Admin Locabite">

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] flex items-center gap-1 mb-2">
                &larr; Kembali ke Dashboard Admin
            </a>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Pengajuan Relawan Wilayah</h1>
            <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Admin menyetujui atau menolak per kecamatan. Sebagian pengajuan boleh disetujui (SDD 2.1).</p>
        </div>
    </div>

    @if($applications->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9]">
            <p class="text-xs text-[#4E6157]">Belum ada pengajuan relawan.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($applications as $app)
                <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#D5E0D9] pb-3">
                        <div>
                            <h2 class="font-display font-bold text-base text-[#16251D]">{{ $app->user->name }}</h2>
                            <p class="text-xs text-[#4E6157]">
                                Email: {{ $app->user->email }} • 
                                WhatsApp: 
                                @if($app->phone || $app->user->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $app->phone ?? $app->user->phone) }}" target="_blank" class="text-[#1E7A4C] font-bold hover:underline">
                                        {{ $app->phone ?? $app->user->phone }}
                                    </a>
                                @else
                                    -
                                @endif
                                • Domisili: {{ $app->user->district?->name ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-[#2A6F97]">
                                {{ $app->regency->name }}
                            </span>
                            <span class="text-[11px] text-[#4E6157] block mt-1">{{ $app->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <!-- Operational Profile Badges -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                        <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-[#4E6157] block">Transportasi:</span>
                            <span class="font-bold text-[#16251D]">{{ $app->transportation ?? 'Sepeda Motor' }}</span>
                        </div>
                        <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-[#4E6157] block">Waktu Siaga:</span>
                            <span class="font-bold text-[#16251D]">{{ $app->availability ?? 'Fleksibel' }}</span>
                        </div>
                        <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-[#4E6157] block">SOP Pangan:</span>
                            <span class="font-bold text-[#1E7A4C] flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Pakta Terverifikasi
                            </span>
                        </div>
                    </div>

                    @if($app->target_beneficiaries)
                        <div class="text-xs">
                            <span class="font-bold text-[#4E6157] block mb-1">Sasaran Penerima Manfaat:</span>
                            <p class="text-[#16251D] font-medium bg-[#F2F7F4] px-3 py-1.5 rounded-xl border border-emerald-100/60 inline-block">
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
                        <p class="bg-[#F2F6F3] p-3 rounded-xl text-[#16251D] leading-relaxed">{{ $app->motivation }}</p>
                    </div>

                    <!-- Per District Decision Section (SDD 4.5 / 8.3) -->
                    <div class="space-y-2">
                        <span class="font-bold text-xs text-[#16251D] block">Status & Keputusan per Kecamatan:</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($app->areas as $area)
                                <div class="p-3.5 rounded-2xl border border-[#D5E0D9] bg-white flex flex-col justify-between gap-3 text-xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <strong class="text-sm text-[#16251D]">Kec. {{ $area->district->name }}</strong>
                                            <x-status-badge :status="$area->status" />
                                        </div>

                                        @if($area->decidedByUser)
                                            <p class="text-[11px] text-[#4E6157]">
                                                Ditinjau oleh {{ $area->decidedByUser->name }} ({{ $area->decided_at?->format('d M Y') }})
                                            </p>
                                        @endif
                                        @if($area->decision_note)
                                            <p class="text-[11px] text-[#7A4A00] italic mt-0.5">Catatan: "{{ $area->decision_note }}"</p>
                                        @endif
                                    </div>

                                    <!-- Decision Form -->
                                    <form method="POST" action="{{ route('admin.volunteer-areas.decide', $area->id) }}" class="space-y-2 pt-2 border-t border-gray-100">
                                        @csrf
                                        <input type="text" name="decision_note" placeholder="Catatan untuk relawan (opsional)" class="input-field text-xs py-1">
                                        <div class="flex items-center gap-1.5">
                                            <button type="submit" name="action" value="approve" class="btn-primary text-xs py-1 px-3 flex-1">
                                                Setujui
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn-danger text-xs py-1 px-3 flex-1">
                                                Tolak
                                            </button>
                                            @if($area->status === 'approved')
                                                <button type="submit" name="action" value="revoke" class="bg-red-900 text-white text-xs py-1 px-2.5 rounded-lg font-bold" onclick="return confirm('Cabut status relawan dari kecamatan ini?')">
                                                    Cabut
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $applications->links() }}
        </div>
    @endif

</div>

</x-layouts.app>
