<?php

namespace App\Enums;

enum CategorieRichting: string
{
    case Inkomst = 'inkomst';
    case Uitgave = 'uitgave';

    public function label(): string
    {
        return match($this) {
            self::Inkomst => 'Inkomst',
            self::Uitgave => 'Uitgave',
        };
    }
}