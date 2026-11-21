<?php

namespace App\Enums;

enum ClaimStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case PICKED_UP = 'picked_up';
    case EXPIRED = 'expired';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu persetujuan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::CANCELLED => 'Dibatalkan',
            self::PICKED_UP => 'Sudah diambil',
            self::EXPIRED => 'Kedaluwarsa',
            self::NO_SHOW => 'Tidak diambil',
        };
    }
}
