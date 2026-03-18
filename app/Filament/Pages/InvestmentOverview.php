<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class InvestmentOverview extends Page
{
    protected string $view = 'filament.pages.investment-overview';

    protected static string | UnitEnum | null $navigationGroup = 'Beleggingen';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;
    protected static ?int $navigationSort = 50;
    protected static ?string $navigationLabel = 'Overzicht Beleggingen';
    protected static ?string $title = 'Overzicht Beleggingen';

    protected function getHeaderWidgets(): array
    {
         return [
            //\App\Filament\Resources\Investments\Widgets\InvestMentOverzichtWidget::class,
            \App\Filament\Resources\Investments\Widgets\InvestmentOverzichtStat::class,
            \App\Filament\Resources\Investments\Widgets\InvestmentDagverschilStat::class,
            \App\Filament\Resources\Investments\Widgets\InvestmentMaandverschilStat::class,
            \App\Filament\Resources\Investments\Widgets\InvestmentJaarverschilStat::class,
            \App\Filament\Resources\Investments\Widgets\FondsKoersontwikkelingChart::class

         ];
    }
}
