<x-layouts.app :title="'Masuk — ' . site_setting('site_name', 'LokaWaste')">

<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full glass-frosted rounded-3xl p-8 sm:p-10 space-y-6 shadow-2xl relative">

        <div class="text-center">
            <div class="inline-flex items-center justify-center gap-2 mb-3">
                <x-brand-logo size="lg" :interactive="true" />
            </div>
            <h1 class="font-display font-bold text-2xl text-[#16251D]">Masuk ke {{ site_setting('site_name', 'LokaWaste') }}</h1>
            <p class="text-xs text-[#4E6157] mt-1">Satu akun untuk berbagi dan mengambil surplus makanan.</p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-[#C93A2E] rounded-xl text-xs text-[#C93A2E]">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Alamat Email</label>
                <input type="email" name="email" id="login-email" value="{{ old('email') }}" class="input-field text-sm" placeholder="nama@email.com" required autofocus>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Kata Sandi</label>
                <input type="password" name="password" id="login-password" class="input-field text-sm" placeholder="••••••••" required>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-[#74877B] text-[#16251D] focus:ring-[#B4E634]">
                    <span class="text-[#4E6157]">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-sm">
                Masuk
            </button>
        </form>

        <div class="text-center pt-2 text-xs text-[#4E6157]">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-[#16251D] hover:underline">
                Daftar sekarang
            </a>
        </div>

    </div>
</div>

</x-layouts.app>
