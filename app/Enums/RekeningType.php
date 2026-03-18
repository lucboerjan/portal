<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;

enum RekeningType: string implements HasLabel
{
    case Zichtrekening = 'zichtrekening';
    case Spaarrekening = 'spaarrekening';
    case Maaltijdcheques = 'maaltijdcheques';
    case Pensioenspaarrekening = 'pensioenspaarrekening';
    case Beleggingsrekening = 'beleggingsrekening';

    public function getLabel(): string
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
