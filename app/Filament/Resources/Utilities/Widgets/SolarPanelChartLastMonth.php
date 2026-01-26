<?php

namespace App\Filament\Resources\Utilities\Widgets;

use Filament\Widgets\ChartWidget;


use Illuminate\Support\Facades\DB;


class SolarPanelChartLastMonth extends ChartWidget
{
    protected ?string $heading = 'Opbrengst zonnepanelen – laatste 35 dagen';
    protected static null|int $sort = 5;

    protected function getData(): array
    {
        // Ophalen van 36 dagen tellerstanden
        $raw = DB::table('utility_solar_panel_readings')
            ->select('date', 'counter_reading')
            ->orderBy('date', 'desc')
            ->limit(36)
            ->get()
            ->reverse()
            ->values();

        // Dagopbrengsten berekenen
        $rows = collect();

        for ($i = 1; $i < $raw->count(); $i++) {
            $rows->push([
                'date' => $raw[$i]->date,
                'daily' => $raw[$i]->counter_reading - $raw[$i - 1]->counter_reading,
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Dagopbrengst (kWh)',
                    'data' => $rows->pluck('daily'),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14,165,233,0.2)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $rows->pluck('date'),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }

}
