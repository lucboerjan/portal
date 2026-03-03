<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ElectricityStatsOverview;
use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Illuminate\Support\Facades\Log;


class OverviewElectricity extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bolt';
    protected  string $view = 'filament.pages.overview-electricity';
    protected static string | UnitEnum | null $navigationGroup = 'Utilities';
    protected static ?string $navigationLabel = 'Overzicht Electriciteit';
    protected static ?string $title = 'Elektriciteitsverbruik';
    
    protected static ?int $navigationSort = 100;
    protected function getHeaderWidgets(): array
    {
        return [
            ElectricityStatsOverview::class,
        ];
    }

}
