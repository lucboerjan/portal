<?php

namespace App\Filament\Pages\Finances;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;

class FinRekeningStanden extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::TableCells;
    protected static ?string $navigationLabel = 'Rekeningsstanden';
    protected static ?string $title = 'Rekeningsstanden';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.finances.fin-rekening-standen';
    protected string | Width | null $maxContentWidth = Width::Full;

    public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }
}