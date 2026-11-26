<x-layouts.app title="Kelola Laporan — Admin LokaWaste">

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-[#4E6157] mb-1">
                    <span>Panel Administrasi</span>
                    <span>/</span>
                    <span class="text-[#16251D] font-bold">Laporan Komunitas</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-[#16251D]">Laporan Masalah Komunitas</h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Laporan terkait makanan tidak layak, listing fiktif, galang makanan, atau pengguna bermasalah.</p>
            </div>

            <x-admin-nav active="reports" />
        </div>

        @if($reports->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9] shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-[#4E6157] flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <p class="font-bold text-sm text-[#16251D]">Belum ada laporan dari warga</p>
                <p class="text-xs text-[#74877B] mt-0.5">Komunitas berjalan tertib dan aman.</p>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
                <div class="divide-y divide-[#D5E0D9]">
                    @foreach($reports as $rep)
                        <div class="p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 text-xs hover:bg-gray-50/50 transition">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase bg-rose-100 text-rose-800">
                                        {{ str_replace('_', ' ', $rep->reason) }}
                                    </span>
                                    <x-status-badge :status="$rep->status" />
                                    <span class="text-[#74877B] text-[11px]">{{ $rep->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-[#4E6157]">
                                    Pelapor: <strong class="text-[#16251D]">{{ $rep->reporter->name }}</strong>
                                    @if($rep->reportedUser)
                                        • Terlapor: <strong class="text-[#C93A2E]">{{ $rep->reportedUser->name }}</strong>
                                    @endif
                                </p>

                                @if($rep->listing)
                                    <p class="text-[#4E6157]">
                                        Terkait Listing: 
                                        <a href="{{ route('listings.show', $rep->listing->id) }}" target="_blank" class="text-[#16251D] font-bold hover:underline">
                                            {{ $rep->listing->title }} &nearr;
                                        </a>
                                    </p>
                                @endif

                                @if($rep->campaign)
                                    <p class="text-[#4E6157]">
                                        Terkait Galang: 
                                        <a href="{{ route('campaigns.show', $rep->campaign->slug) }}" target="_blank" class="text-[#16251D] font-bold hover:underline">
                                            {{ $rep->campaign->title }} &nearr;
                                        </a>
                                    </p>
                                @endif

                                @if($rep->detail)
                                    <div class="bg-[#F8FAF8] p-3 rounded-xl border border-gray-100 text-[#16251D] leading-relaxed">
                                        "{{ $rep->detail }}"
                                    </div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.reports.resolve', $rep->id) }}" class="flex items-center gap-2 self-start sm:self-center">
                                @csrf
                                @if($rep->status !== 'resolved')
                                    <button type="submit" name="status" value="resolved"
                                            class="h-7.5 px-3.5 rounded-xl text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 cursor-pointer shadow-2xs">
                                        Tandai Selesai
                                    </button>
                                @else
                                    <button type="submit" name="status" value="open"
                                            class="h-7.5 px-3 rounded-xl text-xs font-bold bg-gray-100 text-[#4E6157] hover:bg-gray-200 transition active:scale-95 cursor-pointer">
                                        Buka Kembali
                                    </button>
                                @endif
                            </form>
                        </div>
                    @endforeach
                </div>

                @if($reports->hasPages())
                    <div class="p-4 border-t border-[#D5E0D9] flex justify-center">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

</x-layouts.app>
