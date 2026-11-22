<x-layouts.app title="Wilayah Relawan Saya — LokaWaste">

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Wilayah Relawan Saya</h1>
            <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Daftar kecamatan yang Anda ajukan atau ampu untuk penyaluran pangan.</p>
        </div>

        <a href="{{ route('volunteer.apply') }}" class="btn-primary text-xs shrink-0">
            + Tambah Wilayah Baru
        </a>
    </div>

    @if($areas->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9]">
            <p class="text-sm text-[#4E6157]">Anda belum memiliki wilayah relawan yang diajukan.</p>
            <a href="{{ route('volunteer.apply') }}" class="btn-primary text-xs mt-3 inline-flex">
                Ajukan Diri Sebagai Relawan
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($areas as $regencyName => $group)
                <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-card overflow-hidden">
                    <div class="p-5 border-b border-[#D5E0D9] bg-[#F2F6F3]">
                        <h2 class="font-display font-bold text-base text-[#16251D]">{{ $regencyName }}</h2>
                    </div>

                    <div class="divide-y divide-[#D5E0D9]">
                        @foreach($group as $area)
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-bold text-sm text-[#16251D]">Kec. {{ $area->district->name }}</h3>
                                        <x-status-badge :status="$area->status" />
                                    </div>

                                    @if($area->decision_note)
                                        <p class="text-xs text-[#4E6157] mt-0.5">
                                            Catatan admin: <em>"{{ $area->decision_note }}"</em>
                                        </p>
                                    @endif

                                    @if($area->cooldown_until && $area->cooldown_until->isFuture())
                                        <p class="text-[11px] text-[#B45309] font-bold mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Masa tunggu pengajuan ulang hingga {{ $area->cooldown_until->format('d M Y') }}.
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-auto">
                                    @if($area->status === 'approved')
                                        <!-- Resign Button (SDD 4.7) -->
                                        <form method="POST" action="{{ route('volunteer.areas.resign', $area->id) }}"
                                              onsubmit="return confirm('Mundur dari relawan Kec. {{ $area->district->name }}? Galang makanan aktif Anda di kecamatan ini akan otomatis dijeda.')">
                                            @csrf
                                            <button type="submit" class="text-xs text-[#C93A2E] hover:underline px-2 py-1 font-bold">
                                                Mundur
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

</x-layouts.app>
