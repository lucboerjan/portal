<?php

namespace App\Filament\Resources\Utilities\Widgets;
use Filament\Forms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SolarPerYearChart extends ChartWidget
{
    protected ?string $heading = 'Opbrengst zonnepanelen per maand';
    protected static null|int $sort = 3;
    public ?int $year = null;

    protected function getFilters(): ?array
    {
        return $this->getYearOptions();
    }
    protected function getYearOptions(): array
    {
        return DB::table('utility_solar_panel_readings')
            ->selectRaw('YEAR(date) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')
            ->toArray([]);
    }

    protected function getData(): array
    {
        $year = $this->year ?? now()->year;
        $year = $this->filter ?? now()->year;

        // Dagelijkse productie berekenen
        $daily = DB::table('utility_solar_panel_readings as s1')
            ->selectRaw('
        s1.date,
        s1.counter_reading - (
            SELECT s2.counter_reading
            FROM utility_solar_panel_readings s2
            WHERE s2.date < s1.date
            ORDER BY s2.date DESC
            LIMIT 1
        ) AS production
    ')
            ->whereYear('s1.date', $year)
            ->orderBy('s1.date')
            ->get();

        // Per maand optellen
        $monthly = [];
        foreach ($daily as $row) {
            $month = Carbon::parse($row->date)->month;
            $monthly[$month] = ($monthly[$month] ?? 0) + max(0, $row->production);
        }

        return [
            'datasets' => [
                [
                    'label' => "Productie in $year",
                    'data' => array_values($monthly),
                ],
            ],
            'labels' => array_map(
                fn($m) => Carbon::create()->month($m)->format('M'),
                array_keys($monthly)
            ),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getChartHeight(): ?string
    {
        return '600px'; // of '350px', '400px', ...
    }

}
