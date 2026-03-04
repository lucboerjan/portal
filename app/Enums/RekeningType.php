<?php

namespace App\Enums;

enum RekeningType: string
{
    case Zichtrekening = 'zichtrekening';
    case Spaarrekening = 'spaarrekening';
    case Kredietkaart = 'kredietkaart';
    case Cash = 'cash';
    case Pensioenspaarrekening = 'pensioenspaarrekening';
    case Beleggingsrekening = 'beleggingsrekening';

    public function label(): string
    {
        return match($this) {
            self::Zichtrekening => 'Zichtrekening',
            self::Spaarrekening => 'Spaarrekening',
            self::Kredietkaart  => 'Kredietkaart',
            self::Cash          => 'Cash',
            self::Pensioenspaarrekening => 'Pensioenspaarrekening',
            self::Beleggingsrekening => 'Beleggingsrekening',
        };
    }
}