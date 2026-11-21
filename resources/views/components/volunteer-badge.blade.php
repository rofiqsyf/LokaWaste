@props(['label' => 'Relawan'])

<span class="badge-volunteer inline-flex items-center gap-1 text-xs px-2.5 py-0.5" title="Relawan Terdaftar">
    <svg class="w-3.5 h-3.5 text-[#14573A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    {{ $label }}
</span>
