<?php

namespace App\Enums;

enum FoodType: string
{
    case COOKED = 'cooked';
    case PACKAGED = 'packaged';
    case RAW = 'raw';

    public function label(): string
    {
        return match ($this) {
            self::COOKED => 'Makanan Dimasak',
            self::PACKAGED => 'Makanan Kemasan',
            self::RAW => 'Bahan Mentah',
        };
    }
}
