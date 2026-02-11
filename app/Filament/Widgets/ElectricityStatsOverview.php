<?php

// app/Filament/Widgets/ElectricityStatsOverview.php
namespace App\Filament\Widgets;

use App\Services\Utility\UtilityConsumptionService;

use Filament\Widgets\Widget;

class ElectricityStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.electricity-stats-overview';
    
    protected int | string | array $columnSpan = 'full';
    
    public string $typeFilter = 'electricity';
    
    protected UtilityConsumptionService $service;
    
    public function boot(UtilityConsumptionService $service): void
    {
        $this->service = $service;
    }
    
    public function getTableData()
    {
        return $this->service->getTableStructure($this->typeFilter);
    }
    
    public function getChartData()
    {
        $allData = $this->service->getMonthlyConsumptionAllTypes($this->typeFilter);
        
        if ($allData->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
        
        // Verzamel alle periodes
        $allPeriods = collect();
        foreach ($allData as $typeData) {
            $allPeriods = $allPeriods->merge($typeData['data']->pluck('period'));
        }
        $allPeriods = $allPeriods->unique()->sort()->values();
        
        // Bouw datasets per utility type
        $datasets = [];
        $colorIndex = 0;
        
        foreach ($allData as $utilityTypeId => $typeData) {
            $values = [];
            
            foreach ($allPeriods as $period) {
                $consumption = $typeData['data']->get($period);
                $values[] = $consumption ? (float) $consumption['consumption'] : null;
            }
            
            $datasets[] = [
                'label' => $typeData['type']->name,
                'data' => $values,
                'borderColor' => $this->getColor($colorIndex),
                'backgroundColor' => $this->getColor($colorIndex, 0.1),
                'tension' => 0.4,
                'spanGaps' => true, // Verbind lijnen ook bij ontbrekende data
            ];
            
            $colorIndex++;
        }
        
        // Format labels (bijv. "2025-01" -> "Jan 2025")
        $labels = $allPeriods->map(function($period) {
            $date = \Carbon\Carbon::parse($period . '-01');
            return $date->format('M Y');
        })->toArray();
        
        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
    
    private function getColor(int $index, float $alpha = 1)
    {
        $colors = [
            'rgb(59, 130, 246)',   // blue
            'rgb(16, 185, 129)',   // green
            'rgb(245, 158, 11)',   // amber
            'rgb(239, 68, 68)',    // red
            'rgb(168, 85, 247)',   // purple
            'rgb(236, 72, 153)',   // pink
            'rgb(14, 165, 233)',   // sky
            'rgb(34, 197, 94)',    // emerald
        ];
        
        $color = $colors[$index % count($colors)];
        
        if ($alpha < 1) {
            return str_replace('rgb', 'rgba', str_replace(')', ", $alpha)", $color));
        }
        
        return $color;
    }
}