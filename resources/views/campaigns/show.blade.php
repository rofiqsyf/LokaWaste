<x-layouts.app :title="$campaign->title . ' — Galang Makanan ' . site_setting('site_name', 'LokaWaste')">

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('campaigns.index') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#1E7A4C] flex items-center gap-1">
            &larr; Kembali ke Daftar Galang
        </a>
        <x-status-badge :status="$campaign->status" />
    </div>

    <!-- MAIN CAMPAIGN CARD -->
    <div class="glass-card rounded-3xl shadow-card overflow-hidden">
        <!-- Cover -->
        <div class="relative h-64 sm:h-80 bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($campaign->cover_path)
                <img src="{{ asset('storage/' . $campaign->cover_path) }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover">
            @else
                <div class="text-center p-6 text-[#4E6157]">
                    <div class="w-16 h-16 mx-auto mb-2 rounded-2xl bg-white/80 flex items-center justify-center text-[#2A6F97] shadow-xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="font-bold text-sm">{{ $campaign->beneficiary_name }}</span>
                </div>
            @endif

            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-white/95 backdrop-blur-xs text-xs font-bold text-[#2A6F97] shadow-sm">
                {{ match($campaign->beneficiary_type) { 'panti_asuhan' => 'Panti Asuhan', 'ponpes' => 'Pondok Pesantren', 'panti_jompo' => 'Panti Jompo', 'tunawisma' => 'Tunawisma', default => 'Lainnya' } }}
            </span>
        </div>

        <!-- Details -->
        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <span class="text-xs text-[#4E6157] font-semibold block mb-1">
                    Kec. {{ $campaign->district->name }}, {{ $campaign->district->regency->name }}
                </span>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-[#16251D]">{{ $campaign->title }}</h1>
                <p class="text-sm text-[#4E6157] mt-1.5 flex items-center gap-1.5">
                    Dikelola oleh <strong class="text-[#16251D]">{{ $campaign->user->name }}</strong>
                    <x-volunteer-badge label="Relawan Kecamatan" />
                </p>
            </div>

            <!-- Progress Section (SDD 3.8 / 4.8) -->
            <div class="p-5 rounded-2xl glass-dock space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#4E6157]">Porsi Makanan Terkumpul</span>
                    <strong class="font-display font-bold text-lg text-[#16251D]">{{ $campaign->collected_portions }} / {{ $campaign->target_portions }} porsi</strong>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-[#D7F57C] h-3 rounded-full border border-[#C8EE63] transition-all duration-500"
                         style="width: {{ $campaign->progressPercent() }}%"></div>
                </div>
                <div class="flex items-center justify-between text-xs text-[#4E6157]">
                    <span>Target: {{ $campaign->target_portions }} porsi</span>
                    <span>Periode: {{ $campaign->starts_at->format('d M') }} – {{ $campaign->ends_at->format('d M Y') }}</span>
                </div>
            </div>

            <!-- Target Beneficiary Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-xl glass-pill">
                    <span class="text-[#4E6157] block mb-1">Penerima Manfaat:</span>
                    <strong class="text-[#16251D] text-sm block">{{ $campaign->beneficiary_name }}</strong>
                    <span class="text-[#4E6157] block mt-1">{{ $campaign->address }}</span>
                </div>

                @if($campaign->daily_need)
                <div class="p-4 rounded-xl glass-pill">
                    <span class="text-[#4E6157] block mb-1">Kebutuhan Harian:</span>
                    <strong class="text-[#16251D] text-sm block">{{ $campaign->daily_need }}</strong>
                    <span class="text-[#4E6157] block mt-1">Siap dijemput oleh relawan untuk penyaluran langsung.</span>
                </div>
                @endif
            </div>

            <!-- Story / Body -->
            <div>
                <h2 class="font-display font-bold text-lg text-[#16251D] mb-3">Tentang Kebutuhan Ini</h2>
                <div class="prose prose-sm text-[#16251D] leading-relaxed max-w-none">
                    {!! nl2br(e(strip_tags($campaign->body))) !!}
                </div>
            </div>

            <!-- Action: Bagikan makanan ke galang ini (SDD 4.8) -->
            @if($campaign->status === 'published')
                <div class="p-6 rounded-2xl bg-linear-to-r from-[#D7F57C]/30 to-teal-50/40 border border-[#C8EE63] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-display font-bold text-base text-[#14573A]">Punya Makanan Berlebih untuk Galang Ini?</h3>
                        <p class="text-xs text-[#4E6157] mt-0.5">Listing Anda akan diprioritaskan bagi relawan pengelola galang ini selama 2 jam pertama.</p>
                    </div>
                    <a href="{{ route('listings.create') }}?campaign_id={{ $campaign->id }}" class="btn-primary text-xs shrink-0 py-3 px-5">
                        Bagikan makanan ke galang ini
                    </a>
                </div>
            @endif

            <!-- Updates Timeline (SDD 4.8) -->
            <div class="space-y-4 pt-4 border-t border-[#D5E0D9]">
                <div class="flex items-center justify-between">
                    <h2 class="font-display font-bold text-lg text-[#16251D]">Pembaruan Penyaluran</h2>
                    @auth
                        @if(Auth::id() === $campaign->user_id && $campaign->status === 'published')
                            <button type="button" onclick="document.getElementById('update-modal').showModal()" class="btn-secondary text-xs px-3 py-1.5">
                                + Posting Pembaruan
                            </button>
                        @endif
                    @endauth
                </div>

                @if($campaign->updates->isEmpty())
                    <p class="text-xs text-[#4E6157]">Belum ada pembaruan penyaluran.</p>
                @else
                    <div class="space-y-4">
                        @foreach($campaign->updates as $up)
                            <div class="p-4 rounded-2xl bg-[#F2F6F3] border border-[#D5E0D9] space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <strong class="text-[#16251D]">{{ $up->user->name }} (Relawan)</strong>
                                    <span class="text-[#4E6157]">{{ $up->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-sm text-[#16251D] whitespace-pre-line">{{ $up->body }}</p>
                                @if($up->photo_path)
                                    <img src="{{ asset('storage/' . $up->photo_path) }}" class="rounded-xl max-h-64 object-cover mt-2">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>

<!-- POST UPDATE DIALOG (Relawan) -->
@auth
    @if(Auth::id() === $campaign->user_id)
        <dialog id="update-modal"
                onclick="if (event.target === this) this.close()"
                class="glass-frosted rounded-3xl p-6 sm:p-7 w-[calc(100%-2rem)] max-w-md fixed inset-0 m-auto backdrop:bg-black/60 backdrop:backdrop-blur-sm border border-white/80 shadow-2xl max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('volunteer.campaigns.updates', $campaign->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="flex items-start justify-between gap-3 pb-1 border-b border-gray-100">
                    <div>
                        <h3 class="font-display font-bold text-lg text-[#16251D]">Posting Pembaruan Penyaluran</h3>
                        <p class="text-xs text-[#4E6157] mt-0.5">Bagikan dokumentasi porsi yang berhasil disalurkan ke penerima manfaat.</p>
                    </div>
                    <button type="button"
                            onclick="document.getElementById('update-modal').close()"
                            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-[#4E6157] hover:text-[#16251D] flex items-center justify-center shrink-0 transition active:scale-95 cursor-pointer"
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div>
                    <label class="text-xs font-bold text-[#16251D] block mb-1">Keterangan / Cerita Penyaluran</label>
                    <textarea name="body" rows="3" class="input-field text-xs" placeholder="Contoh: Berhasil menyalurkan 40 porsi nasi ayam bakar ke anak-anak panti..." required></textarea>
                </div>

                <div>
                    <label class="text-xs font-bold text-[#16251D] block mb-1">Foto Penyaluran (Opsional)</label>
                    <input type="file" name="photo" accept="image/*" class="text-xs w-full">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('update-modal').close()" class="px-4 py-2 text-xs font-bold text-[#4E6157] hover:bg-gray-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs px-5 py-2.5 rounded-full font-bold transition active:scale-95 shadow-xs cursor-pointer">
                        Publikasikan
                    </button>
                </div>
            </form>
        </dialog>
    @endif
@endauth

</x-layouts.app>
