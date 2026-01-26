<?php

namespace App\Filament\Resources\Utilities\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\UtilityReading;
use App\Models\UtilityType;
use App\Models\UtilityCorrection;

class ElectricityConsumptionChart extends ChartWidget
{
    protected ?string $heading = 'Elektriciteit Verbruik (laatste 12 maanden)';
    
    protected function getData(): array
    {
        $electricityAfnameDag = UtilityType::where('name', 'Dagteller In')->first();
        $electricityAfnameNacht = UtilityType::where('name', 'Nachtteller In')->first();
        
        $months = collect();
        $afnameDagData = [];
        $afnameNachtData = [];
        
        // Loop door laatste 12 maanden
        for ($i = 60; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months->push($month->format('M Y')); // bijv. "Jan 2026"
            
            $afnameDagData[] = $this->getMonthlyConsumption($electricityAfnameDag, $month);
            $afnameNachtData[] = $this->getMonthlyConsumption($electricityAfnameNacht, $month);
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Afname Dag (kWh)',
                    'data' => $afnameDagData,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                ],
                [
                    'label' => 'Afname Nacht (kWh)',
                    'data' => $afnameNachtData,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
    
    private function getMonthlyConsumption(?UtilityType $utilityType, $month): float
    {
        if (!$utilityType) {
            return 0;
        }

        $startOfMonth = $month->copy()->startOfMonth()->toDateString();
        $endOfMonth = $month->copy()->endOfMonth()->toDateString();

        // Haal de reading van deze maand
        $currentReading = UtilityReading::where('utility_type_id', $utilityType->id)
            ->whereBetween('reading_date', [$startOfMonth, $endOfMonth])
            ->orderBy('reading_date', 'desc')
            ->first();
            
        // Haal de reading van de vorige maand
        $previousReading = UtilityReading::where('utility_type_id', $utilityType->id)
            ->where('reading_date', '<', $startOfMonth)
            ->orderBy('reading_date', 'desc')
            ->first();
        
        if (!$currentReading || !$previousReading) {
            return 0;
        }
        
        // Bereken verbruik = huidige stand - vorige stand
        $consumption = $currentReading->meter_stand - $previousReading->meter_stand;
        
        // Tel correcties van deze maand op
        $correction = UtilityCorrection::where('utility_type_id', $utilityType->id)
            ->whereBetween('correction_date', [$startOfMonth, $endOfMonth])
            ->sum('old_meter_final_reading');
        
            return round($consumption, 2);
        //return round($consumption - $correction, 2);
    }
}