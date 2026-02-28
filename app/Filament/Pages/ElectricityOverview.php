<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ElectricityStatsOverview;
use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;

class ElectricityOverview extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bolt';
    protected  string $view = 'filament.pages.ElectricityOverview';

    protected static string | UnitEnum | null $navigationGroup ='Utilities';

    protected function getHeaderWidgets(): array
    {
        return [
            ElectricityStatsOverview::class,
        ];
    }
}
