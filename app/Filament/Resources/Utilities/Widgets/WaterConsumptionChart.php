<?php

namespace App\Filament\Resources\Utilities\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\UtilityReading;
use App\Models\UtilityType;
use App\Models\UtilityCorrection;

class WaterConsumptionChart extends ChartWidget
{
    protected ?string $heading = 'Water Verbruik (laatste 12 maanden)';
    
    protected function getData(): array
    {
        // Haal alle benodigde utility types op
        $watertellerId = UtilityType::where('name', 'Water')->value('id');
        
        $months = collect();
        $consumptionData = [];
        
        // Loop door laatste 12 maanden
        for ($i = 12; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months->push($month->format('M Y'));
            
            $consumptionData[] = $this->calculateMonthlyConsumption(
                $month,
                $watertellerId,
            );
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Totaal Verbruik (m³)',
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
        $watertellerId,
    ): float {
        $startOfMonth = $month->copy()->startOfMonth()->toDateString();
        $endOfMonth = $month->copy()->endOfMonth()->toDateString();
        
        // Bereken verschil voor elke meter
        $waterIn = $this->getMeterDifference($watertellerId, $startOfMonth, $endOfMonth);
        
        // Formule: (Dagteller In + Nachteller In) - (Dagteller Uit + Nachteller Uit) + Zonnepanelen
        $consumption = $waterIn;
        
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
}