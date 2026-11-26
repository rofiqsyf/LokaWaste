@props(['active' => 'dashboard'])

<div class="flex items-center gap-1.5 p-1 rounded-full glass-dock border border-white/80 shadow-xs overflow-x-auto scrollbar-none max-w-full">
    <a href="{{ route('admin.dashboard') }}"
       class="h-8.5 px-3.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 active:scale-95 {{ $active === 'dashboard' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/70' }}">
        <svg class="w-3.5 h-3.5 {{ $active === 'dashboard' ? 'text-[#16251D]' : 'text-[#4E6157]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
        </svg>
        <span>Ringkasan</span>
    </a>

    <a href="{{ route('admin.users') }}"
       class="h-8.5 px-3.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 active:scale-95 {{ $active === 'users' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/70' }}">
        <svg class="w-3.5 h-3.5 {{ $active === 'users' ? 'text-[#16251D]' : 'text-[#4E6157]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span>Pengguna</span>
    </a>

    <a href="{{ route('admin.listings') }}"
       class="h-8.5 px-3.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 active:scale-95 {{ $active === 'listings' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/70' }}">
        <svg class="w-3.5 h-3.5 {{ $active === 'listings' ? 'text-[#16251D]' : 'text-[#4E6157]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <span>Sumber Daya Pangan</span>
    </a>

    <a href="{{ route('admin.campaigns') }}"
       class="h-8.5 px-3.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 active:scale-95 {{ $active === 'campaigns' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/70' }}">
        <svg class="w-3.5 h-3.5 {{ $active === 'campaigns' ? 'text-[#16251D]' : 'text-[#4E6157]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <span>Galang Pangan</span>
    </a>

    <a href="{{ route('admin.volunteer-applications') }}"
       class="h-8.5 px-3.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 active:scale-95 {{ $active === 'volunteers' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/70' }}">
        <svg class="w-3.5 h-3.5 {{ $active === 'volunteers' ? 'text-[#16251D]' : 'text-[#4E6157]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
        <span>Relawan</span>
    </a>

    <a href="{{ route('admin.reports') }}"
       class="h-8.5 px-3.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 active:scale-95 {{ $active === 'reports' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/70' }}">
        <svg class="w-3.5 h-3.5 {{ $active === 'reports' ? 'text-[#16251D]' : 'text-[#4E6157]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>Laporan Masalah</span>
    </a>

    <a href="{{ route('admin.settings') }}"
       class="h-8.5 px-3.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 active:scale-95 {{ $active === 'settings' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/70' }}">
        <svg class="w-3.5 h-3.5 {{ $active === 'settings' ? 'text-[#16251D]' : 'text-[#4E6157]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span>Pengaturan Web</span>
    </a>
</div>
