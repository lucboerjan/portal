<?php

namespace App\Filament\Resources\Utilities\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\UtilityReading;
use App\Models\UtilityType;
use App\Models\UtilityCorrection;
use App\Models\UtilitySolarPanelReading;
use Illuminate\Support\Facades\Log;

class ElectricityConsumptionChart extends ChartWidget
{
    protected ?string $heading = 'Elektriciteit Verbruik (laatste 12 maanden)';
    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Haal alle benodigde utility types op
        $dagtellerId = UtilityType::where('name', 'Dagteller In')->value('id');
        $nachttellerId = UtilityType::where('name', 'Nachtteller In')->value('id');
        $dagtellerUitId = UtilityType::where('name', 'Dagteller Uit')->value('id');
        $nachttellerUitId = UtilityType::where('name', 'Nachtteller Uit')->value('id');
        $zonnepanelenId = UtilityType::where('name', 'Zonnepanelen')->value('id');

        $months = collect();
        $consumptionData = [];

        // Loop door laatste 12 maanden
        for ($i = 12; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $months->push($month->format('M Y'));

            $consumptionData[] = $this->calculateMonthlyConsumption(
                $month,
                $dagtellerId,
                $nachttellerId,
                $dagtellerUitId,
                $nachttellerUitId,
                $zonnepanelenId
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Totaal Verbruik (kWh)',
                    'data' => $consumptionData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function calculateMonthlyConsumption(
        $month,
        $dagtellerId,
        $nachttellerId,
        $dagtellerUitId,
        $nachttellerUitId,
        $zonnepanelenId
    ): float {
        $startOfMonth = $month->copy()->startOfMonth()->toDateString();
        $endOfMonth = $month->copy()->endOfMonth()->toDateString();

        // Bereken verschil voor elke meter
        $dagIn = $this->getMeterDifference($dagtellerId, $startOfMonth, $endOfMonth);
        $nachtIn = $this->getMeterDifference($nachttellerId, $startOfMonth, $endOfMonth);
        $dagUit = $this->getMeterDifference($dagtellerUitId, $startOfMonth, $endOfMonth);
        $nachtUit = $this->getMeterDifference($nachttellerUitId, $startOfMonth, $endOfMonth);
        $zonnepanelen = $this->getZonnepanelenDifference($startOfMonth, $endOfMonth);

        // Formule: (Dagteller In + Nachteller In) - (Dagteller Uit + Nachteller Uit) + Zonnepanelen
        $consumption = ($dagIn + $nachtIn + $zonnepanelen) - ($dagUit + $nachtUit);

        return round($consumption, 2);
    }

    private function getMeterDifference(?int $utilityTypeId, string $startOfMonth, string $endOfMonth): float
    {
        if (!$utilityTypeId) {
            return 0;
        }

        // Haal de reading van deze maand
        $currentReading = UtilityReading::where('utility_type_id', $utilityTypeId)
            ->whereBetween('reading_date', [$startOfMonth, $endOfMonth])
            ->orderBy('reading_date', 'desc')
            ->first();

        // Haal de reading van de vorige maand
        $previousReading = UtilityReading::where('utility_type_id', $utilityTypeId)
            ->where('reading_date', '<', $startOfMonth)
            ->orderBy('reading_date', 'desc')
            ->first();

        if (!$currentReading || !$previousReading) {
            return 0;
        }

        // Bereken basis verschil = huidige stand - vorige stand
        $difference = $currentReading->meter_stand - $previousReading->meter_stand;

        // Check of er een correctie is in deze periode (metervervanging)
        // Als de nieuwe meterstand lager is dan de oude, is er waarschijnlijk een metervervanging
        if ($difference < 0) {
            // Haal de correctie op (de eindwaarde van de oude meter)
            $correction = UtilityCorrection::where('utility_type_id', $utilityTypeId)
                ->whereBetween('correction_date', [$startOfMonth, $endOfMonth])
                ->sum('old_meter_final_reading');

            // Bereken: (oude meter eindstand - vorige reading) + (huidige reading - 0)
            // Dit is gelijk aan: correctie - vorige reading + huidige reading
            $difference = $correction - $previousReading->meter_stand + $currentReading->meter_stand;
        }

        return $difference;
    }



    private function getZonnepanelenDifference(string $startOfMonth, string $endOfMonth): float
    {
        // Haal de eerste reading van de maand uit de DAGELIJKSE zonnepanelen tabel
        $startReading = UtilitySolarPanelReading::where('date', '>=', $startOfMonth)
            ->orderBy('date', 'asc')
            ->first();

        // Haal de laatste reading van de maand uit de DAGELIJKSE zonnepanelen tabel
        $endReading = UtilitySolarPanelReading::where('date', '<=', $endOfMonth)
            ->orderBy('date', 'desc')
            ->first();         

        if (!$startReading || !$endReading) {
            return 0;
        }

        return $endReading->counter_reading - $startReading->counter_reading;
    }
}
