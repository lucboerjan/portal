<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategorieRichting: string implements HasLabel
 {
    case Inkomst = 'inkomst';
    case Uitgave = 'uitgave';

    public function getLabel(): string
    {
        return match($this) {
            self::Inkomst => 'Inkomst',
            self::Uitgave => 'Uitgave',
        };
    }
}