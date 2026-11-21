<x-layouts.app title="Masuk — Locabite">

<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-8 space-y-6">

        <div class="text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-[#1E7A4C] flex items-center justify-center text-white font-display font-bold text-2xl mb-3 shadow-sm">
                L
            </div>
            <h1 class="font-display font-bold text-2xl text-[#16251D]">Masuk ke Locabite</h1>
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

        <!-- Quick Demo Accounts (for evaluation & testing) -->
        <div class="pt-4 border-t border-[#D5E0D9] space-y-2">
            <span class="text-[11px] font-bold text-[#4E6157] block uppercase tracking-wider text-center">Akun Demo (Klik untuk Isi Cepat):</span>
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                <button type="button" onclick="fillLogin('admin@locabite.test', 'password')" class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-left font-semibold text-[#16251D] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Admin</span>
                </button>
                <button type="button" onclick="fillLogin('warung@locabite.test', 'password')" class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-left font-semibold text-[#16251D] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Warung Donatur</span>
                </button>
                <button type="button" onclick="fillLogin('relawan@locabite.test', 'password')" class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-left font-semibold text-[#16251D] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Relawan</span>
                </button>
                <button type="button" onclick="fillLogin('warga@locabite.test', 'password')" class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-left font-semibold text-[#16251D] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Warga Penerima</span>
                </button>
            </div>
        </div>

        <div class="text-center pt-2 text-xs text-[#4E6157]">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-[#1E7A4C] hover:underline">
                Daftar sekarang
            </a>
        </div>

    </div>
</div>

<script>
function fillLogin(email, password) {
    document.getElementById('login-email').value = email;
    document.getElementById('login-password').value = password;
}
</script>

</x-layouts.app>
