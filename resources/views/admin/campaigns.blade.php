<x-layouts.app title="Moderasi Galang Makanan — Admin LokaWaste">

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-[#4E6157] mb-1">
                    <span>Panel Administrasi</span>
                    <span>/</span>
                    <span class="text-[#16251D] font-bold">Galang Pangan</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-[#16251D]">Moderasi Galang Makanan</h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Admin memverifikasi artikel kebutuhan sebelum terbit dan dapat mengalihkan galang yang dijeda.</p>
            </div>

            <x-admin-nav active="campaigns" />
        </div>

        @if($campaigns->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9] shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-[#4E6157] flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <p class="font-bold text-sm text-[#16251D]">Belum ada galang makanan terdaftar</p>
                <p class="text-xs text-[#74877B] mt-0.5">Pengajuan kampanye dari relawan akan muncul di sini.</p>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#F2F6F3] text-[#4E6157] uppercase border-b border-[#D5E0D9]">
                            <tr>
                                <th class="p-4">Galang & Wilayah</th>
                                <th class="p-4">Penerima & Pengelola</th>
                                <th class="p-4">Target & Progres</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Tindakan Moderasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#D5E0D9]">
                            @foreach($campaigns as $camp)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4">
                                        <strong class="font-bold text-sm text-[#16251D] block">{{ $camp->title }}</strong>
                                        <span class="text-[#4E6157]">Kec. {{ $camp->district->name }}, {{ $camp->district->regency->name }}</span>
                                        <a href="{{ route('campaigns.show', $camp->slug) }}" target="_blank" class="text-[#16251D] hover:underline font-bold inline-flex items-center gap-1 mt-1">
                                            <span>Lihat Pratinjau</span>
                                            <span>&nearr;</span>
                                        </a>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-bold text-[#16251D] block">{{ $camp->beneficiary_name }}</span>
                                        <span class="text-[#4E6157]">Relawan: {{ $camp->user->name }}</span>
                                    </td>

                                    <td class="p-4">
                                        <div class="font-bold text-[#16251D]">
                                            {{ $camp->collected_portions }} / {{ $camp->target_portions }} porsi
                                        </div>
                                        <div class="w-28 bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                            <div class="bg-[#84CC16] h-1.5 rounded-full" style="width: {{ $camp->progressPercent() }}%"></div>
                                        </div>
                                        <span class="text-[10px] text-[#74877B] mt-0.5 block">{{ $camp->progressPercent() }}% terkumpul</span>
                                    </td>

                                    <td class="p-4">
                                        <x-status-badge :status="$camp->status" />
                                        @if($camp->paused_reason)
                                            <p class="text-[10px] text-amber-800 italic mt-1 max-w-xs">{{ $camp->paused_reason }}</p>
                                        @endif
                                    </td>

                                    <td class="p-4 text-right space-y-1">
                                        <form method="POST" action="{{ route('admin.campaigns.decide', $camp->id) }}" class="inline-flex items-center gap-1.5 justify-end flex-wrap">
                                            @csrf
                                            @if(in_array($camp->status, ['draft', 'pending_review', 'paused']))
                                                <button type="submit" name="action" value="publish"
                                                        class="h-7 px-3 rounded-lg text-[11px] font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 cursor-pointer shadow-2xs">
                                                    Terbitkan
                                                </button>
                                            @endif
                                            @if($camp->status === 'pending_review')
                                                <button type="submit" name="action" value="revision"
                                                        class="h-7 px-2.5 rounded-lg text-[11px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition active:scale-95 cursor-pointer">
                                                    Minta Revisi
                                                </button>
                                            @endif
                                            @if($camp->status === 'published')
                                                <button type="submit" name="action" value="takedown"
                                                        class="h-7 px-2.5 rounded-lg text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-[#C93A2E] border border-rose-200 transition active:scale-95 cursor-pointer"
                                                        onclick="return confirm('Turunkan galang ini?')">
                                                    Turunkan
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($campaigns->hasPages())
                    <div class="p-4 border-t border-[#D5E0D9] flex justify-center">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

</x-layouts.app>
