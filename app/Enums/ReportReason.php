<?php

namespace App\Enums;

enum ReportReason: string
{
    case EXPIRED_FOOD = 'expired_food';
    case FAKE_LISTING = 'fake_listing';
    case FAKE_CAMPAIGN = 'fake_campaign';
    case UNSAFE_FOOD = 'unsafe_food';
    case HARASSMENT = 'harassment';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::EXPIRED_FOOD => 'Makanan sudah kedaluwarsa/tidak layak',
            self::FAKE_LISTING => 'Listing palsu/fiktif',
            self::FAKE_CAMPAIGN => 'Galang makanan fiktif',
            self::UNSAFE_FOOD => 'Makanan berbahaya/tidak higienis',
            self::HARASSMENT => 'Perilaku mengganggu/pelecehan',
            self::OTHER => 'Lainnya',
        };
    }
}
