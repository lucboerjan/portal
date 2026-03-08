<?php

namespace App\Services\Utility;

use App\Models\UtilityReading;
use Illuminate\Support\Facades\Log;

class ElectricityStatsService
{
    public function buildMatrix(int $typeId): array
    {
        $readings = UtilityReading::query()
            ->selectRaw('
                YEAR(reading_date) as year,
                MONTH(reading_date) as month,
                MAX(meter_stand) as meter_end
            ')
            ->where('utility_type_id', $typeId)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $matrix = [];
        $previous = null;

        foreach ($readings as $row) {
            $year = $row->year;
            $month = $row->month;

            if (!isset($matrix[$year])) {
                $matrix[$year] = array_fill(1, 12, null);
            }

            if ($previous !== null) {
                $matrix[$year][$month] = $row->meter_end - $previous;
            }

            $previous = $row->meter_end;
        }
        return $matrix;
    }

    public function buildChartData(array $matrix): array
    {
        $labels = [];
        $values = [];

        foreach ($matrix as $year => $months) {
            foreach ($months as $month => $value) {
                $labels[] = sprintf('%d-%02d', $year, $month);
                $values[] = $value;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
