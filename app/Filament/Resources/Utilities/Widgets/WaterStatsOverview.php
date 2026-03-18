<?php

namespace App\Filament\Resources\Utilities\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WaterStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected function getStats(): array
    {
        return [
            //
        ];
    }
}
