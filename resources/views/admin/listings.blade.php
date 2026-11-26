<x-layouts.app title="Kelola Sumber Daya Pangan — Admin LokaWaste">

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Top Admin Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-[#4E6157] mb-1">
                <span>Panel Administrasi</span>
                <span>/</span>
                <span class="text-[#16251D] font-bold">Sumber Daya Pangan</span>
            </div>
            <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-[#16251D]">
                Manajemen Sumber Daya Pangan
            </h1>
            <p class="text-xs sm:text-sm text-[#4E6157] mt-1">
                Pantau surplus makanan berlebih, porsi aktif, moderasi kelayakan, dan turunkan listing bermasalah.
            </p>
        </div>

        <x-admin-nav active="listings" />
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Total Listing Masuk</span>
            <div class="font-display font-bold text-3xl text-[#16251D] mt-1">{{ number_format($stats['total']) }}</div>
            <span class="text-[11px] text-[#4E6157]">seluruh surplus terdaftar</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Tersedia di Peta</span>
            <div class="font-display font-bold text-3xl text-[#1E7A4C] mt-1">{{ number_format($stats['available']) }}</div>
            <span class="text-[11px] text-[#4E6157]">dapat diklaim saat ini</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Porsi Terselamatkan</span>
            <div class="font-display font-bold text-3xl text-[#2A6F97] mt-1">{{ number_format($stats['portions_saved']) }}</div>
            <span class="text-[11px] text-[#4E6157]">porsi berhasil diambil</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Listing Diturunkan</span>
            <div class="font-display font-bold text-3xl text-[#C93A2E] mt-1">{{ number_format($stats['taken_down']) }}</div>
            <span class="text-[11px] text-[#4E6157]">dimoderasi oleh admin</span>
        </div>
    </div>

    <!-- Filter and Search Toolbar -->
    <div class="bg-white rounded-3xl border border-[#D5E0D9] p-4 sm:p-5 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.listings') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Cari judul makanan, deskripsi, alamat, atau nama pemberi..."
                       class="w-full h-10 pl-9 pr-4 text-xs sm:text-sm rounded-full border border-gray-200 focus:border-[#A8DC2E] focus:outline-none transition">
                <svg class="w-4 h-4 text-[#4E6157] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="h-10 px-5 rounded-full text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 shadow-2xs cursor-pointer">
                    Cari Listing
                </button>
                @if($search || $status !== 'all')
                    <a href="{{ route('admin.listings') }}"
                       class="h-10 px-4 rounded-full text-xs font-semibold text-[#4E6157] hover:text-[#16251D] hover:bg-gray-100 border border-gray-200 flex items-center justify-center transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none border-t border-gray-100 pt-3">
            <a href="{{ route('admin.listings', ['status' => 'all', 'q' => $search]) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $status === 'all' ? 'bg-[#16251D] text-white shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Semua Status</span>
            </a>

            <a href="{{ route('admin.listings', ['status' => 'available', 'q' => $search]) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $status === 'available' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Tersedia Aktif</span>
            </a>

            <a href="{{ route('admin.listings', ['status' => 'completed', 'q' => $search]) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $status === 'completed' ? 'bg-[#16251D] text-white shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Habis / Selesai</span>
            </a>

            <a href="{{ route('admin.listings', ['status' => 'expired', 'q' => $search]) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $status === 'expired' ? 'bg-[#16251D] text-white shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Kedaluwarsa</span>
            </a>

            <a href="{{ route('admin.listings', ['status' => 'taken_down', 'q' => $search]) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $status === 'taken_down' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Diturunkan Admin</span>
            </a>
        </div>
    </div>

    <!-- Table of Food Resources -->
    <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#F2F6F3] text-[#4E6157] uppercase border-b border-[#D5E0D9]">
                    <tr>
                        <th class="p-4">Makanan & Pemberi</th>
                        <th class="p-4">Kategori & Wilayah</th>
                        <th class="p-4">Porsi & Batas Waktu</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Moderasi & Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D5E0D9]">
                    @forelse($listings as $listing)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-4">
                                <div class="flex items-start gap-3">
                                    @if($listing->photo_path)
                                        <img src="{{ asset('storage/' . $listing->photo_path) }}" alt="{{ $listing->title }}" class="w-12 h-12 rounded-xl object-cover shrink-0 border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-[#D7F57C]/40 border border-[#C8EE63] text-[#16251D] flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <a href="{{ route('listings.show', $listing->id) }}" class="font-display font-bold text-sm text-[#16251D] hover:text-[#1E7A4C] transition truncate block" target="_blank" title="Buka detail publik">
                                            {{ $listing->title }} &nearr;
                                        </a>
                                        <div class="text-[11px] text-[#4E6157] mt-0.5">
                                            Oleh: <strong class="text-[#16251D]">{{ $listing->user->name }}</strong>
                                            @if($listing->user->organization_name)
                                                <span class="text-[#2A6F97] font-semibold">({{ $listing->user->organization_name }})</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-[#74877B] truncate max-w-xs mt-0.5">
                                            {{ $listing->pickup_address }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#F2F6F3] text-[#16251D] border border-gray-200">
                                    {{ $listing->category?->name ?? 'Lainnya' }}
                                </span>
                                <span class="text-[11px] text-[#4E6157] block mt-1">
                                    Kec. {{ $listing->district?->name ?? '-' }}, {{ $listing->district?->regency?->name ?? '' }}
                                </span>
                                <div class="flex items-center gap-1 mt-1">
                                    @if($listing->halal_claim)
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Halal</span>
                                    @endif
                                    @if($listing->is_vegetarian)
                                        <span class="text-[10px] font-bold text-lime-700 bg-lime-50 px-1.5 py-0.2 rounded border border-lime-200">Veggie</span>
                                    @endif
                                </div>
                            </td>

                            <td class="p-4">
                                <div class="font-bold text-[#16251D]">
                                    {{ $listing->portions_left }} / {{ $listing->portions_total }} porsi
                                </div>
                                <div class="text-[11px] mt-0.5 {{ $listing->expires_at->isPast() ? 'text-[#C93A2E] font-bold' : ($listing->expires_at->diffInHours(now()) <= 2 ? 'text-amber-700 font-bold' : 'text-[#4E6157]') }}">
                                    @if($listing->expires_at->isPast())
                                        Kedaluwarsa {{ $listing->expires_at->diffForHumans() }}
                                    @else
                                        Batas: {{ $listing->expires_at->format('d M H:i') }} ({{ $listing->expires_at->diffForHumans() }})
                                    @endif
                                </div>
                                <span class="text-[10px] text-[#74877B]">Max/klaim: {{ $listing->max_portions_per_claim }} porsi</span>
                            </td>

                            <td class="p-4">
                                @if($listing->status === 'taken_down')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                        Diturunkan
                                    </span>
                                @elseif($listing->status === 'available' && $listing->expires_at->isFuture())
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">
                                        Tersedia Aktif
                                    </span>
                                @elseif($listing->status === 'completed' || $listing->portions_left <= 0)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Habis / Selesai
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                        Kedaluwarsa
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Toggle Takedown Button -->
                                    <form method="POST" action="{{ route('admin.listings.toggle-takedown', $listing->id) }}">
                                        @csrf
                                        @if($listing->status === 'taken_down')
                                            <button type="submit"
                                                    class="h-7.5 px-3 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300 transition active:scale-95 cursor-pointer"
                                                    title="Pulihkan listing ke website">
                                                Pulihkan
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="h-7.5 px-3 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition active:scale-95 cursor-pointer"
                                                    onclick="return confirm('Turunkan listing ini dari tampilan publik?')"
                                                    title="Sembunyikan listing dari publik">
                                                Turunkan
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Hard Delete Button -->
                                    <form method="POST" action="{{ route('admin.listings.destroy', $listing->id) }}" onsubmit="return confirm('Hapus permanen listing ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-7.5 h-7.5 rounded-xl text-gray-400 hover:text-[#C93A2E] hover:bg-rose-50 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                                title="Hapus permanen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-[#4E6157]">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-[#4E6157] flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <p class="font-bold text-sm text-[#16251D]">Tidak ada sumber daya makanan ditemukan</p>
                                <p class="text-xs text-[#74877B] mt-0.5">Coba ubah kata kunci pencarian atau filter status listing.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($listings->hasPages())
            <div class="p-4 border-t border-[#D5E0D9] flex justify-center">
                {{ $listings->links() }}
            </div>
        @endif
    </div>

</div>
</div>

</x-layouts.app>
