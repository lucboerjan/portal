<?php

namespace App\Filament\Widgets;

use App\Models\UtilityType;
use App\Services\UtilityConsumptionService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

class ElectricityStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.electricity-stats-overview';
    
    protected int | string | array $columnSpan = 'full';
    
    public ?int $selectedUtilityTypeId = null;
    
    public function mount(): void
    {
        // Selecteer standaard het eerste elektriciteitstype
        $this->selectedUtilityTypeId =  UtilityType::where('type', 'electricity')->first()?->id;
    }
    
    public function getUtilityTypes()
    {
        return UtilityType::where('type', 'like','electricity')->get();
    }
    
    public function getConsumptionData()
    {
        if (!$this->selectedUtilityTypeId) {
            return collect();
        }
        
        $service = app(UtilityConsumptionService::class);
        return $service->getMonthlyConsumption($this->selectedUtilityTypeId);
    }
    
    public function getTableData()
    {
        $data = $this->getConsumptionData();
        Log::info('Verwerkte data voor tabel:', ['data' => $data->toArray()]);
        
        // Groepeer per jaar en maand
        $years = $data->pluck('year')->unique()->sort()->values();
        $months = range(1, 12);
        
        $tableData = [];
        
        foreach ($years as $year) {
            $row = ['year' => $year];
            foreach ($months as $month) {
                $consumption = $data->firstWhere(function ($item) use ($year, $month) {
                    return $item['year'] == $year && $item['month'] == $month;
                });
                $row["month_$month"] = $consumption ? $consumption['consumption'] : null;
            }
            $tableData[] = $row;
        }
        
        return $tableData;
    }
    
    public function getChartData()
    {
        $data = $this->getConsumptionData();
        
        $years = $data->pluck('year')->unique()->sort();
        
        $datasets = [];
        foreach ($years as $year) {
            $yearData = $data->filter(fn($item) => $item['year'] == $year);
            
            $monthlyValues = [];
            for ($month = 1; $month <= 12; $month++) {
                $value = $yearData->firstWhere('month', $month);
                $monthlyValues[] = $value ? (float) $value['consumption'] : null;
            }
            
            $datasets[] = [
                'label' => (string) $year,
                'data' => $monthlyValues,
                'borderColor' => $this->getColorForYear($year),
                'backgroundColor' => $this->getColorForYear($year, 0.1),
                'tension' => 0.4,
            ];
        }
        
        return [
            'labels' => ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
            'datasets' => $datasets,
        ];
    }
    
    private function getColorForYear(int $year, float $alpha = 1)
    {
        $colors = [
            'rgb(59, 130, 246)',  // blue
            'rgb(16, 185, 129)',  // green
            'rgb(245, 158, 11)',  // amber
            'rgb(239, 68, 68)',   // red
            'rgb(168, 85, 247)',  // purple
            'rgb(236, 72, 153)',  // pink
        ];
        
        $index = $year % count($colors);
        $color = $colors[$index];
        
        if ($alpha < 1) {
            return str_replace('rgb', 'rgba', str_replace(')', ", $alpha)", $color));
        }
        
        return $color;
    }
}