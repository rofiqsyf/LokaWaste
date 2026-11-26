<x-layouts.app :title="'Gangguan Server (500) — ' . site_setting('site_name', 'LokaWaste')">
    <div class="min-h-[calc(100vh-10rem)] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full glass-frosted rounded-3xl p-8 text-center space-y-5 shadow-card border border-white/85">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-[#C93A2E] border border-red-200 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-red-50 text-[#C93A2E] border border-red-200">Kode 500</span>
                <h1 class="font-display font-bold text-2xl text-[#16251D] mt-2">Terjadi Gangguan Sistem</h1>
                <p class="text-xs sm:text-sm text-[#4E6157] mt-1.5 leading-relaxed">
                    Sistem sedang mengalami kendala sementara. Silakan muat ulang halaman atau kembali ke peta utama.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-2.5 pt-2">
                <a href="{{ route('home') }}" class="btn-primary text-xs px-5 py-2.5">Kembali ke Peta</a>
                <button type="button" onclick="window.location.reload()" class="btn-secondary text-xs px-4 py-2.5">Muat Ulang</button>
            </div>
        </div>
    </div>
</x-layouts.app>
