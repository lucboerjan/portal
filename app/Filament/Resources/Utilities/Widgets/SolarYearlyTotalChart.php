<?php

namespace App\Filament\Resources\Utilities\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SolarYearlyTotalChart extends ChartWidget
{
    protected ?string $heading = 'Opbrengst zonnepanelen per jaar';
    protected static null|int $sort = 3;

    protected function getData(): array
    {
        // Stap 1: haal alle tellerstanden op, gesorteerd per datum
        $rows = DB::table('utility_solar_panel_readings')
            ->select('date', 'counter_reading')
            ->orderBy('date')
            ->get();

        // Stap 2: bereken dagelijkse productie
        $dailyProduction = [];
        $previous = null;

        foreach ($rows as $row) {
            if ($previous) {
                $production = max(0, $row->counter_reading - $previous->counter_reading);
                $year = Carbon::parse($row->date)->year;
                $dailyProduction[$year] = ($dailyProduction[$year] ?? 0) + $production;
            }
            $previous = $row;
        }

        // Stap 3: prepareer chart data
        ksort($dailyProduction); // sorteer op jaar

        return [
            'datasets' => [
                [
                    'label' => 'Productie per jaar',
                    'data' => array_values($dailyProduction),
                ],
            ],
            'labels' => array_keys($dailyProduction),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getChartHeight(): ?string
    {
        return '300px'; // of '350px', '400px', ...
    }


}
