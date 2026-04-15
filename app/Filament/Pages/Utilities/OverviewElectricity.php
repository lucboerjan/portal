<?php

namespace App\Filament\Pages\Utilities;

use App\Filament\Widgets\ElectricityStatsOverview;
use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;


class OverviewElectricity extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBolt;
    protected  string $view = 'filament.pages.overview-electricity';
    protected static ?string $navigationLabel = 'Overzicht Electriciteit';
    protected static ?string $title = 'Elektriciteitsverbruik';

            public static function getNavigationGroup(): ?string
    {
        return 'Utilities';
    }
        public static function getNavigationSort(): ?int
        {
            return 120;
        }
    protected function getHeaderWidgets(): array
    {
        return [
            ElectricityStatsOverview::class,
        ];
    }

}
