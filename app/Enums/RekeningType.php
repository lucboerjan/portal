<?php

namespace App\Enums;

enum RekeningType: string
{
    case Zichtrekening = 'zichtrekening';
    case Spaarrekening = 'spaarrekening';
    case Maaltijdcheques = 'maaltijdcheques';
    case Pensioenspaarrekening = 'pensioenspaarrekening';
    case Beleggingsrekening = 'beleggingsrekening';

    public function label(): string
    {
        return match ($this) {
            self::Zichtrekening => 'Zichtrekening',
            self::Spaarrekening => 'Spaarrekening',
            self::Maaltijdcheques => 'Maaltijdcheques',
            self::Pensioenspaarrekening => 'Pensioenspaarrekening',
            self::Beleggingsrekening => 'Beleggingsrekening',
        };
    }
}
