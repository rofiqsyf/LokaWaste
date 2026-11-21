<x-layouts.app title="Kelola Laporan — Admin Locabite">

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] flex items-center gap-1 mb-2">
            &larr; Kembali ke Dashboard Admin
        </a>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Laporan Warga</h1>
        <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Laporan terkait makanan tidak layak, listing fiktif, galang makanan, atau pengguna bermasalah.</p>
    </div>

    @if($reports->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9]">
            <p class="text-xs text-[#4E6157]">Belum ada laporan dari warga.</p>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
            <div class="divide-y divide-[#D5E0D9]">
                @foreach($reports as $rep)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 text-xs">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px] uppercase bg-red-100 text-[#C93A2E]">
                                    {{ str_replace('_', ' ', $rep->reason) }}
                                </span>
                                <span class="text-[#4E6157]">Dilaporkan oleh <strong>{{ $rep->reporter->name }}</strong></span>
                                <span class="text-[#4E6157]">({{ $rep->created_at->format('d M Y, H:i') }})</span>
                            </div>

                            @if($rep->detail)
                                <p class="text-sm text-[#16251D] p-3 rounded-xl bg-[#F2F6F3]">{{ $rep->detail }}</p>
                            @endif

                            <div class="text-[11px] text-[#4E6157]">
                                @if($rep->listing)
                                    Listing terkait: <a href="{{ route('listings.show', $rep->listing->id) }}" class="text-[#1E7A4C] font-bold hover:underline">{{ $rep->listing->title }}</a>
                                @elseif($rep->campaign)
                                    Galang terkait: <a href="{{ route('campaigns.show', $rep->campaign->slug) }}" class="text-[#2A6F97] font-bold hover:underline">{{ $rep->campaign->title }}</a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $rep->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $rep->status === 'resolved' ? 'Selesai' : 'Terbuka' }}
                            </span>

                            @if($rep->status === 'open')
                                <form method="POST" action="{{ route('admin.reports.resolve', $rep->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary text-xs py-1 px-3">
                                        Tandai Selesai
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-[#D5E0D9]">
                {{ $reports->links() }}
            </div>
        </div>
    @endif

</div>

</x-layouts.app>
