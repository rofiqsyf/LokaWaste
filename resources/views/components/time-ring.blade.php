@props(['listing'])

<div x-data="timeRing('{{ $listing->expires_at->toIso8601String() }}', '{{ now()->toIso8601String() }}', {{ $listing->windowMinutes() }}, '{{ $listing->tzLabel() }}')"
     data-tz="{{ $listing->timezone() }}"
     class="relative h-14 w-14 shrink-0 flex items-center justify-center" role="img" :aria-label="label">
    <svg viewBox="0 0 36 36" class="absolute inset-0 h-full w-full -rotate-90 pointer-events-none" aria-hidden="true">
        <circle cx="18" cy="18" r="15.8" fill="none" stroke-width="2.6" style="stroke: rgba(22, 37, 29, 0.08)"/>
        <circle cx="18" cy="18" r="15.8" fill="none" stroke-width="2.6" stroke-linecap="round"
                :style="{ stroke: color }" :stroke-dasharray="`${percent} 100`"/>
    </svg>
    <div class="relative z-10 flex flex-col items-center justify-center text-center select-none leading-none px-1">
        <template x-if="!expired && hours >= 1">
            <div class="flex flex-col items-center justify-center">
                <span class="font-display font-black text-xs text-[#16251D] tracking-tight" x-text="`${hours}j`"></span>
                <span class="text-[9.5px] font-bold text-[#4E6157] mt-0.5 tracking-tight" x-text="mins > 0 ? `${mins}m` : ''"></span>
            </div>
        </template>
        <template x-if="!expired && hours === 0">
            <div class="flex flex-col items-center justify-center">
                <span class="font-display font-black text-xs text-[#16251D] tracking-tight" x-text="`${minutes}m`"></span>
                <span class="text-[8.5px] font-extrabold uppercase text-[#4E6157] mt-0.5 tracking-wider">sisa</span>
            </div>
        </template>
        <template x-if="expired">
            <span class="text-[9.5px] font-black uppercase text-[#88998F] tracking-wider">Habis</span>
        </template>
    </div>
</div>
