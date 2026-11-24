<!DOCTYPE html>
<html lang="id" class="overflow-x-clip">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LokaWaste — Cegah Food Waste, Salurkan Makanan Berlebih' }}</title>

    <!-- Google Fonts for Bricolage Grotesque & Atkinson Hyperlegible -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CDN fallback/direct for instant reliability -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @stack('styles')
</head>
<body class="bg-glass-ambient text-[#16251D] min-h-screen flex flex-col font-sans antialiased pb-24 md:pb-0 overflow-x-clip selection:bg-[#D7F57C] selection:text-[#16251D]">

    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-[#1E7A4C] focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#2A6F97]">
        Ke konten utama
    </a>

    <!-- Top Navigation Bar (Huddly Glassmorphism Aesthetic) -->
    <header class="glass-nav sticky top-0 z-40 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-3 sm:gap-4">
                <!-- Brand (Soft-lime Squircle + Sparkle Icon + Modern Sans) -->
                <div class="flex items-center gap-4 sm:gap-6 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-2xl bg-[#D7F57C] text-[#16251D] flex items-center justify-center shadow-xs group-hover:scale-105 group-hover:rotate-6 transition-all duration-200 border border-white/70">
                            <svg class="w-5 h-5 text-[#16251D]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L14.4 9.6L22 12L14.4 14.4L12 22L9.6 14.4L2 12L9.6 9.6L12 2Z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-display font-extrabold text-xl tracking-tight text-[#16251D] group-hover:text-[#1E7A4C] transition-colors leading-none lowercase">lokawaste</span>
                            <span class="text-[9px] uppercase tracking-wider text-[#4E6157] font-bold mt-0.5 hidden sm:inline">Penyelamat Pangan</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Segmented Pill Dock (Exact Reference Aesthetic) -->
                <nav class="hidden md:inline-flex items-center p-1 rounded-full glass-dock gap-0.5 shadow-xs">
                    <a href="{{ route('home') }}"
                       class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('home') ? 'bg-[#16251D] text-white shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80' }}">
                        <span>Peta</span>
                    </a>

                    <a href="{{ route('catalog.index') }}"
                       class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('catalog.*') || request()->routeIs('listings.index') ? 'bg-[#16251D] text-white shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80' }}">
                        <span>Katalog</span>
                    </a>

                    <a href="{{ route('campaigns.index') }}"
                       class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('campaigns.*') ? 'bg-[#16251D] text-white shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80' }}">
                        <span>Galang</span>
                    </a>

                    <a href="{{ route('volunteer.apply') }}"
                       class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('volunteer.*') ? 'bg-[#16251D] text-white shadow-xs' : 'text-[#4E6157] hover:text-[#16251D] hover:bg-white/80' }}">
                        <span>Relawan</span>
                    </a>
                </nav>

                <!-- Right Controls: Search Pill + Notification Bell + User Profile -->
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <!-- Quick Search Pill (Like Reference Image) -->
                    <form action="{{ route('catalog.index') }}" method="GET" class="relative hidden lg:flex items-center" x-data @keydown.window.prevent.cmd.k="$refs.navSearch.focus()" @keydown.window.prevent.ctrl.k="$refs.navSearch.focus()">
                        <svg class="w-3.5 h-3.5 text-[#74877B] absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input x-ref="navSearch" type="text" name="q" placeholder="Cari makanan..."
                               class="w-40 xl:w-52 pl-9 pr-9 py-1.5 rounded-full text-xs glass-input focus:w-60 focus:bg-white text-[#16251D] placeholder:text-[#74877B] transition-all">
                        <span class="absolute right-2.5 px-1.5 py-0.5 rounded text-[9px] font-mono font-bold text-[#74877B] bg-white/90 border border-gray-200/80 shadow-2xs pointer-events-none">⌘K</span>
                    </form>

                    <!-- Notification Bell (Activity Hub) -->
                    <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-full glass-pill hover:bg-white flex items-center justify-center text-[#4E6157] hover:text-[#16251D] relative transition-all active:scale-95" title="Aktivitas & Notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#1E7A4C] ring-2 ring-white"></span>
                    </a>

                    @auth
                        <!-- Share Food Button (Desktop) -->
                        <a href="{{ route('listings.create') }}" class="h-9 px-4 rounded-full text-xs font-bold text-white bg-[#1E7A4C] hover:bg-[#14573A] shadow-xs hover:shadow-md hidden sm:inline-flex items-center gap-1.5 active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Bagikan</span>
                        </a>

                        <!-- User Profile Avatar Pill Dropdown (Like Reference Image Avatar) -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-[#1E7A4C]/30 transition-all focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-linear-to-br from-[#1E7A4C] to-[#14573A] text-white flex items-center justify-center text-xs font-bold ring-2 ring-[#D7F57C] hover:ring-[#1E7A4C] shadow-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <svg class="w-3.5 h-3.5 text-[#4E6157] hidden sm:inline transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu Glass Card -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 class="absolute right-0 mt-2 w-56 rounded-2xl glass-card p-2 shadow-2xl z-50 border border-white/80"
                                 style="display: none;">
                                <div class="px-3 py-2 border-b border-gray-100/80">
                                    <p class="text-xs font-bold text-[#16251D] truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-[#4E6157] truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-[#1E7A4C] border border-emerald-200/60">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </span>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-[#16251D] hover:text-[#1E7A4C] hover:bg-emerald-50/60 rounded-xl transition">
                                        <svg class="w-4 h-4 text-[#4E6157]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                        <span>Dashboard Aktivitas</span>
                                    </a>

                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-[#7A4A00] hover:bg-[#FBEFC9]/60 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                            <span>Panel Admin</span>
                                        </a>
                                    @endif
                                </div>

                                <div class="pt-1 border-t border-gray-100/80">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-[#C93A2E] hover:bg-red-50/60 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3h4a3 3 0 013 3v1"/></svg>
                                            <span>Keluar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Guest Masuk & Daftar Glass Pills -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}"
                               class="h-9 px-4 rounded-full text-xs font-bold text-[#16251D] hover:text-[#1E7A4C] glass-pill hover:bg-white active:scale-95 transition-all duration-150 inline-flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#4E6157]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span>Masuk</span>
                            </a>

                            <a href="{{ route('register') }}"
                               class="h-9 px-5 rounded-full text-xs font-bold text-white bg-[#1E7A4C] hover:bg-[#14573A] shadow-xs hover:shadow-md active:scale-95 transition-all duration-150 inline-flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <span>Daftar</span>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts / Toasts (Glassmorphism accessible cards) -->
    @if (session('success') || session('error') || session('warning') || session('info'))
        <div aria-live="polite" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-3">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-3 p-3.5 glass-card bg-emerald-50/80 border border-emerald-300/80 text-[#14573A] rounded-2xl flex items-center justify-between text-sm shadow-md">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#1E7A4C] text-white flex items-center justify-center shrink-0 text-xs shadow-xs">
                            ✓
                        </div>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#14573A] hover:text-black font-bold text-lg px-1">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-3 p-3.5 glass-card bg-red-50/80 border border-red-300/80 text-[#C93A2E] rounded-2xl flex items-center justify-between text-sm shadow-md">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#C93A2E] text-white flex items-center justify-center shrink-0 text-xs shadow-xs">
                            ✕
                        </div>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#C93A2E] hover:text-black font-bold text-lg px-1">&times;</button>
                </div>
            @endif

            @if (session('warning'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-3 p-3.5 glass-card bg-[#FBEFC9]/85 border border-[#B45309]/50 text-[#7A4A00] rounded-2xl flex items-center justify-between text-sm shadow-md">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#B45309] text-white flex items-center justify-center shrink-0 text-xs shadow-xs">
                            !
                        </div>
                        <span class="font-medium">{{ session('warning') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#7A4A00] hover:text-black font-bold text-lg px-1">&times;</button>
                </div>
            @endif

            @if (session('info'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-3 p-3.5 glass-card bg-sky-50/80 border border-sky-300/80 text-[#2A6F97] rounded-2xl flex items-center justify-between text-sm shadow-md">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#2A6F97] text-white flex items-center justify-center shrink-0 text-xs shadow-xs">
                            i
                        </div>
                        <span class="font-medium">{{ session('info') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#2A6F97] hover:text-black font-bold text-lg px-1">&times;</button>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content -->
    <main id="main-content" class="flex-1 w-full max-w-full overflow-hidden">
        {{ $slot }}
    </main>

    <!-- Mobile Floating Bottom Navigation Bar (Glassmorphic Floating Dock) -->
    <nav class="md:hidden fixed bottom-3 inset-x-3 glass-dock rounded-3xl z-40 flex items-center justify-around h-16 px-2 shadow-2xl border border-white/90">
        <!-- Peta -->
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('home') ? 'text-[#1E7A4C] font-bold' : 'text-[#4E6157]' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <span class="text-[10px] mt-0.5">Peta</span>
        </a>

        <!-- Katalog -->
        <a href="{{ route('catalog.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('catalog.*') || request()->routeIs('listings.index') ? 'text-[#1E7A4C] font-bold' : 'text-[#4E6157]' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span class="text-[10px] mt-0.5">Katalog</span>
        </a>

        <!-- Center Action: Bagikan makanan (Floating Jewel) -->
        <a href="{{ route('listings.create') }}" class="flex flex-col items-center justify-center -mt-6 group">
            <div class="w-13 h-13 rounded-2xl bg-linear-to-br from-[#1E7A4C] to-[#14573A] text-white flex items-center justify-center shadow-lg group-hover:scale-105 active:scale-95 transition-all border-2 border-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="text-[9px] font-extrabold text-[#1E7A4C] mt-0.5 tracking-tight">Bagikan</span>
        </a>

        <!-- Galang -->
        <a href="{{ route('campaigns.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('campaigns.*') ? 'text-[#1E7A4C] font-bold' : 'text-[#4E6157]' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="text-[10px] mt-0.5">Galang</span>
        </a>

        @auth
            <!-- Aktivitas / Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('dashboard') ? 'text-[#1E7A4C] font-bold' : 'text-[#4E6157]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="text-[10px] mt-0.5">Aktivitas</span>
            </a>
        @else
            <!-- Masuk -->
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('login') ? 'text-[#1E7A4C] font-bold' : 'text-[#4E6157]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span class="text-[10px] mt-0.5">Masuk</span>
            </a>
        @endauth
    </nav>

    @stack('scripts')
</body>
</html>
