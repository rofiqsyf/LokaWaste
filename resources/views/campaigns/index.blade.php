<x-layouts.app :title="site_setting('campaigns_hero_title', 'Galang Makanan') . ' — ' . site_setting('site_name', 'LokaWaste')">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="font-display font-bold text-3xl text-[#16251D]">{{ site_setting('campaigns_hero_title', 'Galang Makanan Komunitas') }}</h1>
            <p class="text-sm text-[#4E6157] mt-1 max-w-xl">
                {{ site_setting('campaigns_hero_subtitle', 'Bantu panti asuhan, pondok pesantren, dan warga rentan mendapatkan suplai makanan layak melalui relawan terverifikasi di setiap kecamatan.') }}
            </p>
        </div>

        @auth
            @if(Auth::user()->isVolunteer())
                <a href="{{ route('volunteer.campaigns.create') }}" class="btn-primary text-xs shrink-0">
                    + Buat Galang Baru (Relawan)
                </a>
            @else
                <a href="{{ route('volunteer.apply') }}" class="btn-secondary text-xs shrink-0">
                    Daftar Jadi Relawan Wilayah
                </a>
            @endif
        @endauth
    </div>

    <!-- Campaigns Grid -->
    @if($campaigns->isEmpty())
        <div class="glass-card rounded-3xl p-12 text-center shadow-card">
            <p class="text-sm text-[#4E6157]">Belum ada galang makanan aktif saat ini.</p>
            <a href="{{ route('volunteer.apply') }}" class="btn-primary text-xs mt-3 inline-flex">Daftar Jadi Relawan untuk Memulai</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($campaigns as $camp)
                <div class="glass-card glass-card-hover rounded-3xl overflow-hidden flex flex-col">
                    <!-- Cover or Placeholder -->
                    <div class="h-44 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                        @if($camp->cover_path)
                            <img src="{{ asset('storage/' . $camp->cover_path) }}" alt="{{ $camp->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-center text-[#4E6157]">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-white/80 flex items-center justify-center text-[#2A6F97] shadow-xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold">{{ $camp->beneficiary_name }}</span>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-xs text-[11px] font-bold text-[#2A6F97] shadow-xs">
                            {{ match($camp->beneficiary_type) { 'panti_asuhan' => 'Panti Asuhan', 'ponpes' => 'Pondok Pesantren', 'panti_jompo' => 'Panti Jompo', 'tunawisma' => 'Tunawisma', default => 'Lainnya' } }}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <span class="text-xs text-[#4E6157] font-semibold block mb-1">Kec. {{ $camp->district->name }}, {{ $camp->district->regency->name }}</span>
                            <h2 class="font-display font-bold text-lg text-[#16251D] line-clamp-2">{{ $camp->title }}</h2>
                            <p class="text-xs text-[#4E6157] mt-1">Oleh <strong>{{ $camp->user->name }}</strong> (Relawan)</p>
                        </div>

                        <div class="space-y-2">
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#D7F57C] h-2 rounded-full border border-[#C8EE63] transition-all duration-500"
                                     style="width: {{ $camp->progressPercent() }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-[#4E6157]">
                                <span><strong class="text-[#16251D]">{{ $camp->collected_portions }}</strong> dari {{ $camp->target_portions }} porsi</span>
                                <span class="font-bold text-[#16251D]">{{ $camp->progressPercent() }}%</span>
                            </div>

                            <a href="{{ route('campaigns.show', $camp->slug) }}" class="btn-primary w-full text-xs py-2.5 text-center mt-3">
                                Baca Galang Makanan &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $campaigns->links() }}
        </div>
    @endif

</div>

</x-layouts.app>
