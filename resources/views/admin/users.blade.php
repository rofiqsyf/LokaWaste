<x-layouts.app title="Kelola Pengguna — Admin Locabite">

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] flex items-center gap-1 mb-2">
            &larr; Kembali ke Dashboard Admin
        </a>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">Manajemen Pengguna</h1>
        <p class="text-xs sm:text-sm text-[#4E6157] mt-1">Kelola status aktif akun dan verifikasi bisnis/lembaga pangan.</p>
    </div>

    <div class="bg-white rounded-3xl border border-[#D5E0D9] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#F2F6F3] text-[#4E6157] uppercase border-b border-[#D5E0D9]">
                    <tr>
                        <th class="p-4">Nama & Email</th>
                        <th class="p-4">Peran & Relawan</th>
                        <th class="p-4">Verifikasi Bisnis</th>
                        <th class="p-4">No-Show & Batasan</th>
                        <th class="p-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D5E0D9]">
                    @foreach($users as $u)
                        <tr class="hover:bg-gray-50/50">
                            <td class="p-4">
                                <strong class="text-sm font-bold text-[#16251D] block">{{ $u->name }}</strong>
                                <span class="text-[#4E6157]">{{ $u->email }}</span>
                                @if($u->organization_name)
                                    <span class="text-[11px] text-[#2A6F97] block font-semibold">{{ $u->organization_name }}</span>
                                @endif
                            </td>

                            <td class="p-4">
                                @if($u->isAdmin())
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-[#7A4A00]">Admin</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-[#4E6157]">Pengguna</span>
                                @endif

                                @if($u->isVolunteer())
                                    <div class="mt-1">
                                        <x-volunteer-badge label="Relawan" />
                                    </div>
                                @endif
                            </td>

                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.users.toggle-verify', $u->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-xl text-xs font-bold transition {{ $u->is_verified ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $u->is_verified ? '✓ Terverifikasi' : 'Belum Verifikasi' }}
                                    </button>
                                </form>
                            </td>

                            <td class="p-4">
                                <span class="font-bold {{ $u->no_show_count > 0 ? 'text-[#C93A2E]' : 'text-[#4E6157]' }}">
                                    {{ $u->no_show_count }} kali
                                </span>
                                @if($u->isRestrictedFromClaiming())
                                    <span class="text-[10px] text-[#C93A2E] font-bold block">
                                        Dibatasi s.d {{ $u->claim_restricted_until->format('d M') }}
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('admin.users.toggle-active', $u->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold {{ $u->is_active ? 'text-[#C93A2E] hover:underline' : 'text-[#1E7A4C] hover:underline' }}">
                                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#D5E0D9]">
            {{ $users->links() }}
        </div>
    </div>

</div>

</x-layouts.app>
