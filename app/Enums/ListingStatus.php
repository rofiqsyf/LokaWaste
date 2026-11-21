<?php

namespace App\Enums;

enum ListingStatus: string
{
    case DRAFT = 'draft';
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::AVAILABLE => 'Tersedia',
            self::RESERVED => 'Dipesan',
            self::COMPLETED => 'Selesai',
            self::EXPIRED => 'Kedaluwarsa',
            self::CANCELLED => 'Dibatalkan',
        };
    }
}
