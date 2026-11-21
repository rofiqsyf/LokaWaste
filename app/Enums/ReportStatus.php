<?php

namespace App\Enums;

enum ReportStatus: string
{
    case OPEN = 'open';
    case REVIEWED = 'reviewed';
    case RESOLVED = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Menunggu Tinjauan',
            self::REVIEWED => 'Sedang Ditinjau',
            self::RESOLVED => 'Selesai Ditangani',
        };
    }
}
