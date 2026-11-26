@props(['status'])

@php
    $config = match($status) {
        'available' => ['bg' => 'bg-[#D7F57C]/40 text-[#16251D] border-[#C8EE63]', 'text' => 'Tersedia', 'icon' => 'check-circle'],
        'reserved' => ['bg' => 'bg-[#2A6F97]/10 text-[#2A6F97] border-[#2A6F97]/30', 'text' => 'Dipesan', 'icon' => 'clock'],
        'completed' => ['bg' => 'bg-[#4E6157]/10 text-[#4E6157] border-[#4E6157]/30', 'text' => 'Selesai', 'icon' => 'check'],
        'expired' => ['bg' => 'bg-[#C93A2E]/10 text-[#C93A2E] border-[#C93A2E]/30', 'text' => 'Kedaluwarsa', 'icon' => 'x-circle'],
        'cancelled' => ['bg' => 'bg-gray-100 text-gray-600 border-gray-300', 'text' => 'Dibatalkan', 'icon' => 'slash'],
        'pending' => ['bg' => 'bg-[#FBEFC9] text-[#7A4A00] border-[#B45309]/30', 'text' => 'Menunggu persetujuan', 'icon' => 'clock'],
        'approved' => ['bg' => 'bg-[#D7F57C]/40 text-[#16251D] border-[#C8EE63]', 'text' => 'Disetujui', 'icon' => 'check-circle'],
        'rejected' => ['bg' => 'bg-[#C93A2E]/10 text-[#C93A2E] border-[#C93A2E]/30', 'text' => 'Ditolak', 'icon' => 'x'],
        'picked_up' => ['bg' => 'bg-[#D7F57C] text-[#16251D] border-[#A8DC2E] font-bold', 'text' => 'Sudah Diambil', 'icon' => 'gift'],
        'no_show' => ['bg' => 'bg-[#C93A2E]/15 text-[#C93A2E] border-[#C93A2E]', 'text' => 'Tidak Diambil (No Show)', 'icon' => 'alert-triangle'],
        'published' => ['bg' => 'bg-[#D7F57C]/40 text-[#16251D] border-[#C8EE63]', 'text' => 'Terbit', 'icon' => 'globe'],
        'pending_review' => ['bg' => 'bg-[#FBEFC9] text-[#7A4A00] border-[#B45309]/30', 'text' => 'Menunggu Tinjauan', 'icon' => 'file-text'],
        'paused' => ['bg' => 'bg-[#FBEFC9] text-[#7A4A00] border-[#B45309]/40', 'text' => 'Dijeda', 'icon' => 'pause'],
        'closed' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-300', 'text' => 'Ditutup', 'icon' => 'lock'],
        'revoked' => ['bg' => 'bg-[#C93A2E]/10 text-[#C93A2E] border-[#C93A2E]/30', 'text' => 'Dicabut', 'icon' => 'slash'],
        'resigned' => ['bg' => 'bg-gray-100 text-gray-600 border-gray-300', 'text' => 'Mundur', 'icon' => 'user-minus'],
        default => ['bg' => 'bg-gray-100 text-gray-600 border-gray-200', 'text' => ucfirst($status), 'icon' => 'info'],
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $config['bg'] }}">
    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    {{ $config['text'] }}
</span>
