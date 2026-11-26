<x-layouts.app :title="'Akses Ditolak (403) — ' . site_setting('site_name', 'LokaWaste')">
    <div class="min-h-[calc(100vh-10rem)] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full glass-frosted rounded-3xl p-8 text-center space-y-5 shadow-card border border-white/85">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-[#FBEFC9] text-[#7A4A00] border border-[#B45309]/30 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#FBEFC9] text-[#7A4A00]">Kode 403</span>
                <h1 class="font-display font-bold text-2xl text-[#16251D] mt-2">Akses Tidak Diizinkan</h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1.5 leading-relaxed">
                    {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman atau sumber daya ini.' }}
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-2.5 pt-2">
                <a href="{{ route('home') }}" class="btn-primary text-xs px-5 py-2.5">Kembali ke Peta</a>
                <a href="{{ route('catalog.index') }}" class="btn-secondary text-xs px-4 py-2.5">Jelajah Katalog</a>
            </div>
        </div>
    </div>
</x-layouts.app>
