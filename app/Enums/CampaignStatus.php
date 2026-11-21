<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case PUBLISHED = 'published';
    case PAUSED = 'paused';
    case CLOSED = 'closed';
    case REJECTED = 'rejected';
    case TAKEN_DOWN = 'taken_down';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::PENDING_REVIEW => 'Menunggu tinjauan',
            self::PUBLISHED => 'Terbit',
            self::PAUSED => 'Dijeda',
            self::CLOSED => 'Ditutup',
            self::REJECTED => 'Ditolak',
            self::TAKEN_DOWN => 'Diturunkan',
        };
    }
}
