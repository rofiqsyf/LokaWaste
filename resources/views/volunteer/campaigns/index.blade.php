<x-layouts.app title="Kelola Galang Makanan Saya — LokaWaste">

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Kelola Galang Makanan Saya</h1>
            <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Daftar inisiatif pangan untuk panti dan warga rentan yang Anda kelola sebagai relawan.</p>
        </div>

        <a href="{{ route('volunteer.campaigns.create') }}" class="btn-primary text-xs shrink-0">
            + Buat Galang Baru
        </a>
    </div>

    @if($campaigns->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9]">
            <p class="text-xs text-[#4E6157]">Anda belum memiliki artikel galang makanan.</p>
            <a href="{{ route('volunteer.campaigns.create') }}" class="btn-primary text-xs mt-3 inline-flex">
                Buat Galang Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($campaigns as $camp)
                <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs p-6 space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-[#2A6F97]">
                                Kec. {{ $camp->district->name }}
                            </span>
                            <x-status-badge :status="$camp->status" />
                        </div>

                        <h2 class="font-display font-bold text-lg text-[#16251D]">{{ $camp->title }}</h2>
                        <p class="text-xs text-[#4E6157] mt-1">Penerima: {{ $camp->beneficiary_name }} ({{ $camp->address }})</p>

                        <!-- Progress Bar -->
                        <div class="mt-4 space-y-1.5">
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-[#1E7A4C] h-2.5 rounded-full" style="width: {{ $camp->progressPercent() }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-[#4E6157]">
                                <span><strong class="text-[#16251D]">{{ $camp->collected_portions }}</strong> / {{ $camp->target_portions }} porsi</span>
                                <span class="font-bold text-[#1E7A4C]">{{ $camp->progressPercent() }}%</span>
                            </div>
                        </div>

                        @if($camp->review_note)
                            <div class="mt-3 p-3 rounded-xl bg-amber-50 text-xs text-[#7A4A00] border border-amber-200">
                                <strong>Catatan Admin:</strong> "{{ $camp->review_note }}"
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-[#D5E0D9] flex items-center justify-between gap-2">
                        <a href="{{ route('campaigns.show', $camp->slug) }}" class="text-xs font-bold text-[#2A6F97] hover:underline">
                            Halaman Publik &rarr;
                        </a>

                        <div class="flex items-center gap-2">
                            @if($camp->status === 'draft')
                                <form method="POST" action="{{ route('volunteer.campaigns.submit', $camp->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary text-xs py-1.5 px-3">
                                        Kirim untuk Tinjauan
                                    </button>
                                </form>
                            @elseif($camp->status === 'published')
                                <form method="POST" action="{{ route('volunteer.campaigns.close', $camp->id) }}" onsubmit="return confirm('Tutup galang makanan ini? Target tercapai atau periode selesai.')">
                                    @csrf
                                    <button type="submit" class="text-xs text-[#4E6157] hover:text-[#C93A2E] underline">
                                        Tutup Galang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

</x-layouts.app>
