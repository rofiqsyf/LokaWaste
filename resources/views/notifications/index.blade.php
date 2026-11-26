<x-layouts.app :title="'Notifikasi & Pembaruan — ' . site_setting('site_name', 'LokaWaste')">
    <div class="min-h-[calc(100vh-4rem)] bg-[#F8FAF8] py-8 sm:py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Breadcrumb & Top Bar -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <nav class="flex items-center gap-2 text-xs font-semibold text-[#4E6157]">
                    <a href="{{ route('home') }}" class="hover:text-[#16251D] transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Beranda</span>
                    </a>
                    <span>/</span>
                    <a href="{{ route('dashboard') }}" class="hover:text-[#16251D] transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-[#16251D] font-bold">Notifikasi</span>
                </nav>

                @if($unreadCount > 0 || $totalCount > 0)
                    <div class="flex items-center gap-2">
                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="h-8.5 px-3.5 rounded-full text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] active:bg-[#B4E634] text-[#16251D] border border-[#C8EE63] flex items-center gap-1.5 transition active:scale-95 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Tandai Semua Dibaca</span>
                                </button>
                            </form>
                        @endif

                        @if($totalCount > $unreadCount)
                            <form action="{{ route('notifications.clear-read') }}" method="POST" onsubmit="return confirm('Hapus semua notifikasi yang telah dibaca?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="h-8.5 px-3.5 rounded-full text-xs font-semibold text-[#4E6157] hover:text-[#C93A2E] hover:bg-rose-50 border border-transparent hover:border-rose-200 flex items-center gap-1.5 transition active:scale-95 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="hidden sm:inline">Bersihkan yang Dibaca</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Header Card -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 shadow-sm relative overflow-hidden bg-white/70">
                <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-[#D7F57C]/25 blur-3xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="font-display font-extrabold text-2xl sm:text-3xl tracking-tight text-[#16251D]">
                                Notifikasi & Pembaruan
                            </h1>
                            @if($unreadCount > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-2xs">
                                    {{ $unreadCount }} baru
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-[#4E6157] mt-1.5 max-w-xl">
                            Pantau permohonan klaim, konfirmasi titik pengambilan makanan, dan kabar aktivitas komunitas pangan Anda secara langsung.
                        </p>
                    </div>

                    <!-- Notification Bell Icon Graphic -->
                    <div class="hidden sm:flex w-14 h-14 rounded-2xl bg-[#D7F57C] text-[#16251D] border border-white items-center justify-center shrink-0 shadow-sm rotate-3">
                        <svg class="w-7 h-7 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                </div>

                <!-- Horizontal Filter Pill Dock -->
                <div class="mt-6 pt-5 border-t border-gray-100/80 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                    <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
                       class="h-9 px-4 rounded-full text-xs font-bold transition-all flex items-center gap-2 shrink-0 active:scale-95 {{ $filter === 'all' ? 'bg-[#16251D] text-white shadow-xs' : 'glass-pill text-[#4E6157] hover:text-[#16251D] hover:bg-white' }}">
                        <span>Semua</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-gray-100 text-[#4E6157]' }}">{{ $totalCount }}</span>
                    </a>

                    <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                       class="h-9 px-4 rounded-full text-xs font-bold transition-all flex items-center gap-2 shrink-0 active:scale-95 {{ $filter === 'unread' ? 'bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] shadow-xs' : 'glass-pill text-[#4E6157] hover:text-[#16251D] hover:bg-white' }}">
                        <span>Belum Dibaca</span>
                        @if($unreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'unread' ? 'bg-[#16251D] text-[#D7F57C]' : 'bg-[#D7F57C] text-[#16251D]' }}">{{ $unreadCount }}</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-gray-100 text-[#4E6157]">0</span>
                        @endif
                    </a>

                    <a href="{{ route('notifications.index', ['filter' => 'claim']) }}"
                       class="h-9 px-4 rounded-full text-xs font-bold transition-all flex items-center gap-2 shrink-0 active:scale-95 {{ $filter === 'claim' ? 'bg-[#16251D] text-white shadow-xs' : 'glass-pill text-[#4E6157] hover:text-[#16251D] hover:bg-white' }}">
                        <span>Klaim & Makanan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'claim' ? 'bg-white/20 text-white' : 'bg-gray-100 text-[#4E6157]' }}">{{ $claimCount }}</span>
                    </a>

                    <a href="{{ route('notifications.index', ['filter' => 'system']) }}"
                       class="h-9 px-4 rounded-full text-xs font-bold transition-all flex items-center gap-2 shrink-0 active:scale-95 {{ $filter === 'system' ? 'bg-[#16251D] text-white shadow-xs' : 'glass-pill text-[#4E6157] hover:text-[#16251D] hover:bg-white' }}">
                        <span>Sistem & Komunitas</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'system' ? 'bg-white/20 text-white' : 'bg-gray-100 text-[#4E6157]' }}">{{ $systemCount }}</span>
                    </a>
                </div>
            </div>

            <!-- Notification Items List -->
            <div class="space-y-3">
                @forelse($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                        $category = $data['category'] ?? 'system';
                        $icon = $data['icon'] ?? 'bell';
                        $actionUrl = $data['action_url'] ?? null;
                        $actionLabel = $data['action_label'] ?? 'Lihat Detail';
                    @endphp

                    <div class="group relative rounded-2xl transition-all duration-200 {{ $isUnread ? 'bg-white border-l-4 border-l-[#84CC16] border-y border-r border-[#C8EE63]/70 shadow-sm' : 'bg-white/80 border border-gray-100 hover:border-gray-200 hover:bg-white shadow-2xs' }} p-4 sm:p-5">
                        <div class="flex items-start gap-3.5 sm:gap-4">
                            <!-- Category Icon Squircle -->
                            <div class="shrink-0 mt-0.5">
                                @if($icon === 'shopping-bag')
                                    <div class="w-10 h-10 rounded-2xl bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63] flex items-center justify-center shadow-2xs">
                                        <svg class="w-5 h-5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                @elseif($icon === 'check-circle')
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shadow-2xs">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @elseif($icon === 'clock')
                                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shadow-2xs">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @elseif($icon === 'sparkles')
                                    <div class="w-10 h-10 rounded-2xl bg-[#D7F57C] text-[#16251D] border border-[#C8EE63] flex items-center justify-center shadow-2xs">
                                        <svg class="w-5 h-5 text-[#16251D]" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2L14.4 9.6L22 12L14.4 14.4L12 22L9.6 14.4L2 12L9.6 9.6L12 2Z"/>
                                        </svg>
                                    </div>
                                @elseif($icon === 'heart')
                                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shadow-2xs">
                                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-2xl bg-[#F2F6F3] text-[#4E6157] border border-[#D5E0D9] flex items-center justify-center shadow-2xs">
                                        <svg class="w-5 h-5 text-[#4E6157]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-display font-bold text-sm sm:text-base text-[#16251D]">
                                            {{ $data['title'] ?? 'Notifikasi' }}
                                        </h3>
                                        @if($isUnread)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-[#D7F57C] text-[#16251D] border border-[#C8EE63]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#16251D]"></span>
                                                Baru
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Relative Timestamp -->
                                    <span class="text-[11px] font-medium text-[#74877B] shrink-0 whitespace-nowrap flex items-center gap-1">
                                        <svg class="w-3 h-3 text-[#74877B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Message Body -->
                                <p class="text-xs sm:text-sm text-[#4E6157] mt-1 leading-relaxed">
                                    {{ $data['message'] ?? '' }}
                                </p>

                                <!-- Action Buttons Row -->
                                <div class="mt-3.5 flex items-center gap-2.5 flex-wrap">
                                    @if($actionUrl)
                                        <a href="{{ $actionUrl }}"
                                           class="h-8 px-4 rounded-full text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] active:bg-[#B4E634] text-[#16251D] border border-[#C8EE63] inline-flex items-center gap-1.5 transition active:scale-95 shadow-2xs">
                                            <span>{{ $actionLabel }}</span>
                                            <svg class="w-3.5 h-3.5 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif

                                    @if($isUnread)
                                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="h-8 px-3 rounded-full text-xs font-semibold text-[#4E6157] hover:text-[#16251D] hover:bg-gray-100 border border-gray-200 transition active:scale-95 cursor-pointer">
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Button -->
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-full text-gray-400 hover:text-[#C93A2E] hover:bg-rose-50 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                                title="Hapus notifikasi ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="glass-card rounded-3xl p-10 text-center border border-white/80 shadow-xs bg-white/70">
                        <div class="w-16 h-16 rounded-3xl bg-[#D7F57C]/40 text-[#16251D] border border-[#C8EE63] flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <svg class="w-8 h-8 text-[#16251D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-lg text-[#16251D] mb-1">
                            @if($filter === 'unread')
                                Tidak Ada Notifikasi Belum Dibaca
                            @else
                                Belum Ada Notifikasi di Sini
                            @endif
                        </h3>
                        <p class="text-xs sm:text-sm text-[#4E6157] max-w-md mx-auto mb-5">
                            @if($filter === 'unread')
                                Semua kabar dan pemberitahuan telah Anda baca. Kami akan memberi tahu jika ada klaim atau aktivitas baru!
                            @else
                                Setiap kali ada orang yang mengajukan klaim, menyetujui makanan, atau update dari komunitas, notifikasi akan muncul di sini.
                            @endif
                        </p>
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('home') }}"
                               class="h-9 px-4 rounded-full text-xs font-bold bg-[#D7F57C] hover:bg-[#C8EE63] text-[#16251D] border border-[#C8EE63] inline-flex items-center gap-1.5 transition active:scale-95 shadow-2xs">
                                <span>Jelajah Peta</span>
                            </a>
                            <a href="{{ route('listings.create') }}"
                               class="h-9 px-4 rounded-full text-xs font-bold bg-[#16251D] hover:bg-[#1E7A4C] text-white inline-flex items-center gap-1.5 transition active:scale-95 shadow-2xs">
                                <span>Bagikan Makanan</span>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if($notifications->hasPages())
                <div class="pt-4 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>
