<?php

namespace App\Filament\Pages\Finances;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;

class FinJaaroverzicht extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::TableCells;
    protected static ?string $navigationLabel = 'Jaaroverzicht';
    protected static ?string $title = 'Jaaroverzicht';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.finances.fin-jaaroverzicht';
    protected string | Width | null $maxContentWidth = Width::Full;
    public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }
}
