<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class UtilityOverview extends Page
{
    protected string $view = 'filament.pages.utility-overview';

    protected static string | UnitEnum | null $navigationGroup = 'Utilities';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;
    protected static ?int $navigationSort = 50;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Utilities\Widgets\SolarPanelChartLastMonth::class,
            \App\Filament\Resources\Utilities\Widgets\SolarYearlyTotalChart::class,
            \App\Filament\Resources\Utilities\Widgets\SolarPerYearChart::class,

            \App\Filament\Resources\Utilities\Widgets\ElectricityConsumptionChart::class,
        ];
    }
}
