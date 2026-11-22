<x-layouts.app title="Moderasi Galang Makanan — Admin LokaWaste">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] flex items-center gap-1 mb-2">
                &larr; Kembali ke Dashboard Admin
            </a>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Moderasi Galang Makanan</h1>
            <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Admin memverifikasi artikel kebutuhan sebelum terbit dan dapat mengalihkan galang yang dijeda.</p>
        </div>
    </div>

    @if($campaigns->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-[#D5E0D9]">
            <p class="text-xs text-[#4E6157]">Belum ada galang makanan terdaftar.</p>
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
                            <tr class="hover:bg-gray-50/50">
                                <td class="p-4">
                                    <strong class="font-bold text-sm text-[#16251D] block">{{ $camp->title }}</strong>
                                    <span class="text-[#4E6157]">Kec. {{ $camp->district->name }}, {{ $camp->district->regency->name }}</span>
                                    <a href="{{ route('campaigns.show', $camp->slug) }}" target="_blank" class="text-[#2A6F97] hover:underline block mt-0.5 font-semibold">
                                        Lihat Pratinjau &rarr;
                                    </a>
                                </td>

                                <td class="p-4">
                                    <span class="font-bold text-[#16251D] block">{{ $camp->beneficiary_name }}</span>
                                    <span class="text-[#4E6157]">Relawan: {{ $camp->user->name }}</span>
                                </td>

                                <td class="p-4">
                                    <strong class="text-[#1E7A4C]">{{ $camp->collected_portions }}</strong> / {{ $camp->target_portions }} porsi
                                    <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-[#1E7A4C] h-1.5 rounded-full" style="width: {{ $camp->progressPercent() }}%"></div>
                                    </div>
                                </td>

                                <td class="p-4">
                                    <x-status-badge :status="$camp->status" />
                                    @if($camp->paused_reason)
                                        <p class="text-[10px] text-amber-800 italic mt-1 max-w-xs">{{ $camp->paused_reason }}</p>
                                    @endif
                                </td>

                                <td class="p-4 text-right space-y-1">
                                    <form method="POST" action="{{ route('admin.campaigns.decide', $camp->id) }}" class="inline-flex items-center gap-1 justify-end">
                                        @csrf
                                        @if(in_array($camp->status, ['draft', 'pending_review', 'paused']))
                                            <button type="submit" name="action" value="publish" class="btn-primary text-[11px] py-1 px-2.5">
                                                Terbitkan
                                            </button>
                                        @endif
                                        @if($camp->status === 'pending_review')
                                            <button type="submit" name="action" value="revision" class="px-2 py-1 bg-amber-500 text-white rounded-lg text-[11px] font-bold">
                                                Minta Revisi
                                            </button>
                                        @endif
                                        @if($camp->status === 'published')
                                            <button type="submit" name="action" value="takedown" class="btn-danger text-[11px] py-1 px-2.5" onclick="return confirm('Turunkan galang ini?')">
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

            <div class="p-4 border-t border-[#D5E0D9]">
                {{ $campaigns->links() }}
            </div>
        </div>
    @endif

</div>

</x-layouts.app>
