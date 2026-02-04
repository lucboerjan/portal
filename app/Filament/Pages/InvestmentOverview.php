<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Investments\Widgets\InvestmentOverzichtStat;

class InvestmentOverview extends Page
{
    protected string $view = 'filament.pages.investment-overview';

    protected static string | UnitEnum | null $navigationGroup = 'Investments';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;
    protected static ?int $navigationSort = 50;

    protected function getHeaderWidgets(): array
    {
         return [
            InvestmentOverzichtStat::class,
        ];
    }
}
