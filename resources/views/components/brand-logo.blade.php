@props([
    'size' => 'md', // sm, md, lg
    'interactive' => true,
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'w-8 h-8',
        'lg' => 'w-14 h-14',
        default => 'w-10 h-10',
    };
    $uid = 'lw-logo-' . uniqid();
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex items-center justify-center shrink-0 {$sizeClasses} lokawaste-logo-wrap " . ($interactive ? 'group/logo' : '')]) }}>
    <!-- Subtle Dynamic GPS Signal Ripple -->
    <span class="lokawaste-logo-ripple absolute inset-1 rounded-full bg-[#D7F57C]/50 pointer-events-none"></span>

    <!-- Glassmorphic Squircle Framing so Green + Dark Ink Pin Pops on Any Surface -->
    <div class="relative w-full h-full rounded-2xl bg-white/95 border border-[#D5E0D9]/90 shadow-xs flex items-center justify-center p-1 transition-all duration-300 group-hover/logo:shadow-md group-hover/logo:border-[#A8DC2E] group-hover/logo:scale-105 group-active/logo:scale-95 overflow-visible">
        <svg viewBox="0 0 100 108" class="w-full h-full overflow-visible lokawaste-logo-svg" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
                <linearGradient id="{{ $uid }}-lime" x1="18" y1="10" x2="82" y2="80" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#D7F57C"/>
                    <stop offset="52%" stop-color="#A8DC2E"/>
                    <stop offset="100%" stop-color="#84CC16"/>
                </linearGradient>
                <linearGradient id="{{ $uid }}-ink" x1="30" y1="36" x2="68" y2="98" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#22372C"/>
                    <stop offset="100%" stop-color="#121E18"/>
                </linearGradient>
            </defs>

            <!-- Soft Ground GPS Shadow Dot -->
            <ellipse cx="50" cy="102" rx="11" ry="2.8" fill="#16251D" opacity="0.16" class="lokawaste-logo-shadow"/>

            <!-- Floating GPS Pin Body -->
            <g class="lokawaste-logo-pin">
                <!-- Left-Top GPS Arc Shell (Lime) -->
                <path d="M29 68 C15 52, 16 27, 31 16 C44 6, 62 7, 72 19 C60 14, 44 17, 33 29 C25 38, 24 53, 29 68 Z"
                      fill="url(#{{ $uid }}-lime)"/>

                <!-- Inner Organic Leaf (Dynamic Sway) -->
                <path class="lokawaste-logo-leaf"
                      d="M32 46 C33 31, 45 20, 62 19 C66 19, 69 20, 70 21 C62 28, 57 38, 46 42 C39 45, 35 46, 32 46 Z"
                      fill="url(#{{ $uid }}-lime)"/>

                <!-- Right GPS Pin Sweeping Arc Cradling Under Wrist -->
                <path d="M64 25 C79 31, 85 48, 78 64 C72 77, 56 82, 42 78 L39 72 C53 74, 67 69, 71 58 C76 46, 71 33, 62 27 Z"
                      fill="url(#{{ $uid }}-lime)"/>

                <!-- Bottom GPS Point + Lower Receiving Hand (Deep Ink) -->
                <path class="lokawaste-logo-hand-receive"
                      d="M50 98 L30 68 C29 64, 33 58, 39 55 C44 53, 49 52, 54 49 C56 48, 58 50, 56 52 C53 55, 48 56, 47 57 C52 57, 59 53, 64 49 C66 47, 68 49, 66 52 C61 59, 53 64, 43 65 C39 66, 38 69, 40 72 C44 77, 56 79, 68 72 L50 98 Z"
                      fill="url(#{{ $uid }}-ink)"/>

                <!-- Upper Giving Hand Reaching Down (Deep Ink) -->
                <path class="lokawaste-logo-hand-give"
                      d="M66 34 C61 39, 55 43, 47 47 C45 48, 46 51, 49 50 C52 49, 54 48, 55 48 C52 51, 50 53, 52 54 C54 55, 58 52, 62 49 C65 47, 68 45, 71 43 C70 39, 68 36, 66 34 Z"
                      fill="url(#{{ $uid }}-ink)"/>
            </g>
        </svg>
    </div>
</div>
