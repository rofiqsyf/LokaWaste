<!DOCTYPE html>
<html lang="id" class="overflow-x-clip">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Locabite — Cegah Food Waste, Salurkan Makanan Berlebih' }}</title>

    <!-- Google Fonts for Bricolage Grotesque & Atkinson Hyperlegible -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CDN fallback/direct for instant reliability -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @stack('styles')
</head>
<body class="bg-[#F2F6F3] text-[#16251D] min-h-screen flex flex-col font-sans antialiased pb-20 md:pb-0 overflow-x-clip">

    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-[#1E7A4C] focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#2A6F97]">
        Ke konten utama
    </a>

    <!-- Top Navigation Bar -->
    <header class="bg-white/95 backdrop-blur-xl border-b border-[#D5E0D9]/80 sticky top-0 z-40 shadow-[0_1px_3px_rgba(22,37,29,0.03)] transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Brand -->
                <div class="flex items-center gap-5 sm:gap-6 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-2xl bg-linear-to-br from-[#1E7A4C] to-[#14573A] flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a5 5 0 0 1 5 5c0 3-5 7-5 7s-5-4-5-7a5 5 0 0 1 5-5z"/>
                                <circle cx="12" cy="7" r="1.5" fill="currentColor"/>
                                <path d="M3 14c0 4.418 4.03 8 9 8s9-3.582 9-8"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-display font-extrabold text-xl tracking-tight text-[#16251D] group-hover:text-[#1E7A4C] transition-colors leading-none">Locabite</span>
                            <span class="text-[10px] text-[#4E6157] font-medium tracking-wide mt-1 hidden sm:inline">Penyelamat Pangan</span>
                        </div>
                    </a>

                    <!-- Nav Links Dock (Minimalist Segments) -->
                    <nav class="hidden md:inline-flex items-center p-1 rounded-full bg-gray-100/80 border border-gray-200/60 backdrop-blur-xs gap-0.5">
                        <a href="{{ route('home') }}"
                           class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('home') ? 'bg-white text-[#1E7A4C] shadow-2xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/60' }}">
                            <svg class="w-3.5 h-3.5 {{ request()->routeIs('home') ? 'text-[#1E7A4C]' : 'text-[#74877B]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <span>Peta Interaktif</span>
                        </a>

                        <a href="{{ route('catalog.index') }}"
                           class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('catalog.*') || request()->routeIs('listings.index') ? 'bg-white text-[#1E7A4C] shadow-2xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/60' }}">
                            <svg class="w-3.5 h-3.5 {{ request()->routeIs('catalog.*') || request()->routeIs('listings.index') ? 'text-[#1E7A4C]' : 'text-[#74877B]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Katalog Makanan</span>
                        </a>

                        <a href="{{ route('campaigns.index') }}"
                           class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('campaigns.*') ? 'bg-white text-[#1E7A4C] shadow-2xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/60' }}">
                            <svg class="w-3.5 h-3.5 {{ request()->routeIs('campaigns.*') ? 'text-[#1E7A4C]' : 'text-[#74877B]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Galang</span>
                        </a>

                        <a href="{{ route('volunteer.apply') }}"
                           class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('volunteer.*') ? 'bg-white text-[#1E7A4C] shadow-2xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/60' }}">
                            <svg class="w-3.5 h-3.5 {{ request()->routeIs('volunteer.*') ? 'text-[#1E7A4C]' : 'text-[#74877B]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Relawan</span>
                        </a>
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2.5">
                    @auth
                        <!-- Share Food Primary Button (Desktop) -->
                        <a href="{{ route('listings.create') }}" class="btn-primary text-xs py-2 px-3.5 hidden sm:inline-flex items-center gap-1.5 shadow-xs active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Bagikan Makanan</span>
                        </a>

                        <!-- Dashboard / Activity -->
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-[#14573A] border border-emerald-200/70' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-gray-100' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-[#7A4A00]/10 text-[#7A4A00] hover:bg-[#7A4A00]/20 border border-[#B45309]/30 transition">
                                Admin
                            </a>
                        @endif

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-[#4E6157] hover:text-[#C93A2E] px-2 py-1.5 rounded-lg hover:bg-gray-100 transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold text-[#4E6157] hover:text-[#16251D] px-3 py-1.5 rounded-xl hover:bg-gray-100 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary text-xs py-2 px-3.5 shadow-xs active:scale-95 transition-all">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts / Toasts (WCAG accessible) -->
    @if (session('success') || session('error') || session('warning') || session('info'))
        <div aria-live="polite" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-3">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-3 p-3.5 bg-[#1E7A4C]/10 border border-[#1E7A4C] text-[#14573A] rounded-xl flex items-center justify-between text-sm shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1E7A4C] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#14573A] hover:text-black font-bold text-lg">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-3 p-3.5 bg-[#C93A2E]/10 border border-[#C93A2E] text-[#C93A2E] rounded-xl flex items-center justify-between text-sm shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C93A2E] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#C93A2E] hover:text-black font-bold text-lg">&times;</button>
                </div>
            @endif

            @if (session('warning'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-3 p-3.5 bg-[#FBEFC9] border border-[#B45309] text-[#7A4A00] rounded-xl flex items-center justify-between text-sm shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#B45309] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#7A4A00] hover:text-black font-bold text-lg">&times;</button>
                </div>
            @endif

            @if (session('info'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-3 p-3.5 bg-[#2A6F97]/10 border border-[#2A6F97] text-[#2A6F97] rounded-xl flex items-center justify-between text-sm shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#2A6F97] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#2A6F97] hover:text-black font-bold text-lg">&times;</button>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content -->
    <main id="main-content" class="flex-1 w-full max-w-full overflow-hidden">
        {{ $slot }}
    </main>

    <!-- Mobile Bottom Navigation Bar (SDD 4.1 & design.md 8) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-[#D5E0D9] z-40 flex items-center justify-around h-16 px-2 shadow-lg">
        <!-- Peta -->
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('home') ? 'text-[#1E7A4C]' : 'text-[#4E6157]' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <span class="text-[10px] font-medium mt-0.5">Peta</span>
        </a>

        <!-- Katalog -->
        <a href="{{ route('catalog.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('catalog.*') || request()->routeIs('listings.index') ? 'text-[#1E7A4C]' : 'text-[#4E6157]' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span class="text-[10px] font-medium mt-0.5">Katalog</span>
        </a>

        <!-- Center Action: Bagikan makanan -->
        <a href="{{ route('listings.create') }}" class="flex flex-col items-center justify-center -mt-5">
            <div class="w-12 h-12 rounded-full bg-[#1E7A4C] text-white flex items-center justify-center shadow-md active:bg-[#14573A]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="text-[10px] font-bold text-[#1E7A4C] mt-1">Bagikan</span>
        </a>

        <!-- Galang -->
        <a href="{{ route('campaigns.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('campaigns.*') ? 'text-[#1E7A4C]' : 'text-[#4E6157]' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="text-[10px] font-medium mt-0.5">Galang</span>
        </a>

        @auth
            <!-- Aktivitas / Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('dashboard') ? 'text-[#1E7A4C]' : 'text-[#4E6157]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="text-[10px] font-medium mt-0.5">Aktivitas</span>
            </a>
        @else
            <!-- Masuk -->
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('login') ? 'text-[#1E7A4C]' : 'text-[#4E6157]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span class="text-[10px] font-medium mt-0.5">Masuk</span>
            </a>
        @endauth
    </nav>

    @stack('scripts')
</body>
</html>
