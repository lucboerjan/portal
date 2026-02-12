<?php

// app/Filament/Widgets/ElectricityStatsOverview.php
namespace App\Filament\Widgets;

use App\Services\Utility\UtilityConsumptionService;

use Filament\Widgets\Widget;

class ElectricityStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.electricity-stats-overview';
    
    protected int | string | array $columnSpan = 'full';
    protected static bool $isDiscovered = false;
    
    // Alleen deze utility types (de nieuwe meters)
    public array $utilityTypeIds = [6, 7, 8, 9];
    
    // Vanaf deze datum
    public string $startDate = '2020-03-01';
    
    protected UtilityConsumptionService $service;
    
    public function boot(UtilityConsumptionService $service): void
    {
        $this->service = $service;
    }
    
    public function getTableData()
    {
        return $this->service->getTableStructureFiltered($this->utilityTypeIds, $this->startDate);
    }
    
    public function getChartData()
    {
        return $this->service->getChartDataFiltered($this->utilityTypeIds, $this->startDate);
    }
    
    private function getColor(int $index, float $alpha = 1)
    {
        $colors = [
            'rgb(59, 130, 246)',   // blue
            'rgb(16, 185, 129)',   // green
            'rgb(245, 158, 11)',   // amber
            'rgb(239, 68, 68)',    // red
        ];
        
        $color = $colors[$index % count($colors)];
        
        if ($alpha < 1) {
            return str_replace('rgb', 'rgba', str_replace(')', ", $alpha)", $color));
        }
        
        return $color;
    }
}