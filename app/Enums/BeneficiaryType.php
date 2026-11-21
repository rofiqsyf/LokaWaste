<?php

namespace App\Enums;

enum BeneficiaryType: string
{
    case PANTI_ASUHAN = 'panti_asuhan';
    case PONPES = 'ponpes';
    case PANTI_JOMPO = 'panti_jompo';
    case TUNAWISMA = 'tunawisma';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::PANTI_ASUHAN => 'Panti Asuhan',
            self::PONPES => 'Pondok Pesantren',
            self::PANTI_JOMPO => 'Panti Jompo',
            self::TUNAWISMA => 'Komunitas Tunawisma',
            self::LAINNYA => 'Lainnya',
        };
    }
}
