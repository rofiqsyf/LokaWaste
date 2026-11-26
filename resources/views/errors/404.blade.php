<x-layouts.app :title="'Halaman Tidak Ditemukan (404) — ' . site_setting('site_name', 'LokaWaste')">
    <div class="min-h-[calc(100vh-10rem)] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full glass-frosted rounded-3xl p-8 text-center space-y-5 shadow-card border border-white/85">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#D7F57C]/50 text-[#16251D] border border-[#C8EE63]">Kode 404</span>
                <h1 class="font-display font-bold text-2xl text-[#16251D] mt-2">Halaman Tidak Ditemukan</h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1.5 leading-relaxed">
                    Tautan mungkin sudah kedaluwarsa, makanan sudah diambil, atau alamat halaman keliru.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-2.5 pt-2">
                <a href="{{ route('home') }}" class="btn-primary text-xs px-5 py-2.5">Buka Peta Utama</a>
                <a href="{{ route('catalog.index') }}" class="btn-secondary text-xs px-4 py-2.5">Katalog Makanan</a>
            </div>
        </div>
    </div>
</x-layouts.app>
