@props(['listing'])

<div x-data="timeRing('{{ $listing->expires_at->toIso8601String() }}', '{{ now()->toIso8601String() }}', {{ $listing->windowMinutes() }}, '{{ $listing->tzLabel() }}')"
     data-tz="{{ $listing->timezone() }}"
     class="relative h-14 w-14 shrink-0" role="img" :aria-label="label">
    <svg viewBox="0 0 36 36" class="h-full w-full -rotate-90" aria-hidden="true">
        <circle cx="18" cy="18" r="15.9" fill="none" stroke-width="3" style="stroke: var(--color-line)"/>
        <circle cx="18" cy="18" r="15.9" fill="none" stroke-width="3" stroke-linecap="round"
                :style="{ stroke: color }" :stroke-dasharray="`${percent} 100`"/>
    </svg>
    <span class="absolute inset-0 grid place-items-center font-display text-sm font-bold text-ink"
          x-text="short" aria-hidden="true"></span>
</div>
