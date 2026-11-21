<x-layouts.app title="Daftar Akun — Locabite">

<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl border border-[#D5E0D9] shadow-card p-8 space-y-6">

        <div class="text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-[#1E7A4C] flex items-center justify-center text-white font-display font-bold text-2xl mb-3 shadow-sm">
                L
            </div>
            <h1 class="font-display font-bold text-2xl text-[#16251D]">Daftar Akun Baru</h1>
            <p class="text-xs text-[#4E6157] mt-1">Satu jenis akun untuk membagikan dan mengambil makanan.</p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-[#C93A2E] rounded-xl text-xs text-[#C93A2E] space-y-1">
                @foreach ($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="input-field text-sm" placeholder="Nama Anda" required autofocus>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="input-field text-sm" placeholder="nama@email.com" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Kata Sandi <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="input-field text-sm" placeholder="Min. 8 karakter" required>
                </div>

                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Ulangi Sandi <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="input-field text-sm" placeholder="Konfirmasi sandi" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Nomor WhatsApp/HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="input-field text-sm" placeholder="0812...">
                </div>

                <div>
                    <label class="font-bold text-xs text-[#16251D] block mb-1">Kecamatan Domisili</label>
                    <select name="district_id" class="input-field text-sm">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>
                                Kec. {{ $d->name }} ({{ $d->regency->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="font-bold text-xs text-[#16251D] block mb-1">Nama Usaha / Lembaga (Opsional)</label>
                <input type="text" name="organization_name" value="{{ old('organization_name') }}" class="input-field text-sm" placeholder="Isi jika Anda pemilik resto, katering, atau warung">
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-sm">
                Daftar Akun Sekarang
            </button>
        </form>

        <div class="text-center pt-2 text-xs text-[#4E6157]">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-[#1E7A4C] hover:underline">
                Masuk di sini
            </a>
        </div>

    </div>
</div>

</x-layouts.app>
