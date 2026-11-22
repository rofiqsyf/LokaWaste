<x-layouts.app title="Masuk — LokaWaste">

<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-8 space-y-6">

        <div class="text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-[#1E7A4C] flex items-center justify-center text-white font-display font-bold text-2xl mb-3 shadow-sm">
                L
            </div>
            <h1 class="font-display font-bold text-2xl text-[#16251D]">Masuk ke LokaWaste</h1>
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
                    <input type="checkbox" name="remember" class="rounded border-[#74877B] text-[#1E7A4C]">
                    <span class="text-[#4E6157]">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-sm">
                Masuk
            </button>
        </form>

        <div class="text-center pt-2 text-xs text-[#4E6157]">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-[#1E7A4C] hover:underline">
                Daftar sekarang
            </a>
        </div>

    </div>
</div>

</x-layouts.app>
