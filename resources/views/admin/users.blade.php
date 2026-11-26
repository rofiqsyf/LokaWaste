<x-layouts.app :title="'Kelola Pengguna — Admin ' . site_setting('site_name', 'LokaWaste')">

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Top Admin Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-[#4E6157] mb-1">
                <span>Panel Administrasi</span>
                <span>/</span>
                <span class="text-[#16251D] font-bold">Pengguna</span>
            </div>
            <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-[#16251D]">
                Manajemen Pengguna
            </h1>
            <p class="text-xs sm:text-sm text-[#4E6157] mt-1">
                Kelola hak akses, verifikasi bisnis/komunitas pangan, dan moderasi pembatasan no-show.
            </p>
        </div>

        <x-admin-nav active="users" />
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Total Akun Terdaftar</span>
            <div class="font-display font-bold text-3xl text-[#16251D] mt-1">{{ number_format($stats['total'] ?? 0) }}</div>
            <span class="text-[11px] text-[#4E6157]">pengguna di platform</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Bisnis / Lembaga Terverifikasi</span>
            <div class="font-display font-bold text-3xl text-[#1E7A4C] mt-1">{{ number_format($stats['verified'] ?? 0) }}</div>
            <span class="text-[11px] text-[#4E6157]">memiliki badge terpercaya</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Relawan Aktif</span>
            <div class="font-display font-bold text-3xl text-[#2A6F97] mt-1">{{ number_format($stats['volunteers'] ?? 0) }}</div>
            <span class="text-[11px] text-[#4E6157]">terdaftar di kecamatan</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-[#D5E0D9] shadow-xs">
            <span class="text-xs font-semibold text-[#4E6157] block">Pengguna Dibatasi</span>
            <div class="font-display font-bold text-3xl text-[#C93A2E] mt-1">{{ number_format($stats['restricted'] ?? 0) }}</div>
            <span class="text-[11px] text-[#4E6157]">pelanggaran no-show</span>
        </div>
    </div>

    <!-- Filter and Search Toolbar -->
    <div class="bg-white rounded-3xl border border-[#D5E0D9] p-4 sm:p-5 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text"
                       name="q"
                       value="{{ $search ?? '' }}"
                       placeholder="Cari nama pengguna, email, atau nama lembaga/organisasi..."
                       class="w-full h-10 pl-9 pr-4 text-xs sm:text-sm rounded-full border border-gray-200 focus:border-[#A8DC2E] focus:outline-none transition">
                <svg class="w-4 h-4 text-[#4E6157] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="h-10 px-5 rounded-full text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] transition active:scale-95 shadow-2xs cursor-pointer">
                    Cari Pengguna
                </button>
                @if(($search ?? '') || ($filter ?? 'all') !== 'all')
                    <a href="{{ route('admin.users') }}"
                       class="h-10 px-4 rounded-full text-xs font-semibold text-[#4E6157] hover:text-[#16251D] hover:bg-gray-100 border border-gray-200 flex items-center justify-center transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none border-t border-gray-100 pt-3">
            <a href="{{ route('admin.users', ['filter' => 'all', 'q' => $search ?? '']) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ ($filter ?? 'all') === 'all' ? 'bg-[#16251D] text-white shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Semua Pengguna</span>
            </a>

            <a href="{{ route('admin.users', ['filter' => 'verified', 'q' => $search ?? '']) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ ($filter ?? 'all') === 'verified' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Terverifikasi</span>
            </a>

            <a href="{{ route('admin.users', ['filter' => 'unverified', 'q' => $search ?? '']) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ ($filter ?? 'all') === 'unverified' ? 'bg-[#16251D] text-white shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Belum Verifikasi</span>
            </a>

            <a href="{{ route('admin.users', ['filter' => 'restricted', 'q' => $search ?? '']) }}"
               class="h-8 px-3.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ ($filter ?? 'all') === 'restricted' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-gray-100 text-[#4E6157] hover:text-[#16251D]' }}">
                <span>Dibatasi / No-Show</span>
            </a>
        </div>
    </div>

    <!-- Table of Users -->
    <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#F2F6F3] text-[#4E6157] uppercase border-b border-[#D5E0D9]">
                    <tr>
                        <th class="p-4">Pengguna & Lembaga</th>
                        <th class="p-4">Peran & Wilayah</th>
                        <th class="p-4">Verifikasi Bisnis</th>
                        <th class="p-4">No-Show & Batasan</th>
                        <th class="p-4 text-right">Aksi & Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D5E0D9]">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-linear-to-br from-[#D7F57C] to-[#C8EE63] text-[#16251D] flex items-center justify-center text-xs font-black ring-2 ring-white shadow-2xs shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <strong class="text-sm font-bold text-[#16251D]">{{ $u->name }}</strong>
                                            @if(!$u->is_active)
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-black bg-rose-100 text-rose-700">Nonaktif</span>
                                            @endif
                                        </div>
                                        <span class="text-[#4E6157] block truncate">{{ $u->email }}</span>
                                        @if($u->phone)
                                            <span class="text-[11px] text-[#74877B] block">{{ $u->phone }}</span>
                                        @endif
                                        @if($u->organization_name)
                                            <span class="text-[11px] text-[#2A6F97] block font-semibold">{{ $u->organization_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($u->isAdmin())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300">Admin</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-[#4E6157]">Pengguna</span>
                                    @endif

                                    @if($u->isVolunteer())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">
                                            Relawan
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-[#4E6157] block mt-1">
                                    Kec. {{ $u->district?->name ?? 'Belum Diatur' }}
                                </span>
                            </td>

                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.users.toggle-verify', $u->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="px-2.5 py-1 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer {{ $u->is_verified ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] hover:bg-[#C8EE63]' : 'bg-gray-100 text-[#4E6157] hover:bg-gray-200 border border-gray-200' }}"
                                            title="Klik untuk mengubah status verifikasi">
                                        @if($u->is_verified)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Terverifikasi
                                            </span>
                                        @else
                                            Belum Verifikasi
                                        @endif
                                    </button>
                                </form>
                            </td>

                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold {{ $u->no_show_count > 0 ? 'text-[#C93A2E]' : 'text-[#4E6157]' }}">
                                        {{ $u->no_show_count }} kali
                                    </span>

                                    @if($u->no_show_count > 0 || $u->isRestrictedFromClaiming())
                                        <form method="POST" action="{{ route('admin.users.reset-penalties', $u->id) }}" onsubmit="return confirm('Reset no-show dan hapus pembatasan untuk pengguna ini?')">
                                            @csrf
                                            <button type="submit"
                                                    class="text-[10px] font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 px-2 py-0.5 rounded-lg border border-amber-200 transition cursor-pointer"
                                                    title="Reset pembatasan & no-show">
                                                Reset
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                @if($u->isRestrictedFromClaiming())
                                    <span class="text-[10px] text-[#C93A2E] font-bold block mt-0.5">
                                        Dibatasi s.d {{ $u->claim_restricted_until->format('d M H:i') }}
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('admin.users.toggle-active', $u->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="h-7.5 px-3 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer {{ $u->is_active ? 'bg-rose-50 text-[#C93A2E] hover:bg-rose-100 border border-rose-200' : 'bg-emerald-50 text-[#1E7A4C] hover:bg-emerald-100 border border-emerald-200' }}">
                                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan Akun' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-[#4E6157]">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-[#4E6157] flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <p class="font-bold text-sm text-[#16251D]">Tidak ada pengguna ditemukan</p>
                                <p class="text-xs text-[#74877B] mt-0.5">Coba gunakan kata kunci pencarian lain.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#D5E0D9] flex justify-center">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
</div>

</x-layouts.app>
