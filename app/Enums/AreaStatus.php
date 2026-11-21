<?php

namespace App\Enums;

enum AreaStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REVOKED = 'revoked';
    case RESIGNED = 'resigned';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::REVOKED => 'Dicabut',
            self::RESIGNED => 'Mundur',
        };
    }
}
