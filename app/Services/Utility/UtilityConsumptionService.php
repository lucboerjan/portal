<?php
namespace App\Services\Utility;

use App\Models\UtilityReading;
use App\Models\UtilityType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class UtilityConsumptionService
{
    public function getMonthlyConsumptionAllTypes(string $typeFilter = 'electricity')
    {
        // Haal alle utility types op
        $utilityTypes = UtilityType::where('type', 'like', "%{$typeFilter}%")
            ->orderBy('name')
            ->get();
        
        if ($utilityTypes->isEmpty()) {
            return collect();
        }
        
        $allData = [];
        
        foreach ($utilityTypes as $type) {
            $consumption = $this->getMonthlyConsumptionForType($type->id);
            $allData[$type->id] = [
                'type' => $type,
                'data' => $consumption
            ];
        }
        
        return collect($allData);
    }
    
    public function getMonthlyConsumptionForType(int $utilityTypeId)
    {
        $readings = UtilityReading::where('utility_type_id', $utilityTypeId)
            ->orderBy('reading_date')
            ->get();
        
        if ($readings->count() < 2) {
            return collect();
        }
        
        $monthlyData = [];
        $previousReading = null;
        
        foreach ($readings as $reading) {
            $date = Carbon::parse($reading->reading_date);
            $yearMonth = $date->format('Y-m');
            
            if ($previousReading) {
                $previousDate = Carbon::parse($previousReading->reading_date);
                $consumption = $reading->meter_stand - $previousReading->meter_stand;
                
                // Detecteer meterwisseling (als huidige stand lager is dan vorige)
                if ($consumption < 0) {
                    // Bij meterwisseling: gebruik alleen de huidige stand
                    $consumption = $reading->meter_stand;
                }
                
                // Als readings in verschillende maanden zijn, sla op
                if ($date->format('Y-m') !== $previousDate->format('Y-m')) {
                    $monthlyData[$yearMonth] = [
                        'year' => $date->year,
                        'month' => $date->month,
                        'consumption' => $consumption,
                        'period' => $yearMonth,
                        'date' => $date,
                    ];
                }
            }
            
            $previousReading = $reading;
        }
        
        return collect($monthlyData);
    }
    
    public function getTableStructure(string $typeFilter = 'electricity')
    {
        $allData = $this->getMonthlyConsumptionAllTypes($typeFilter);
        
        if ($allData->isEmpty()) {
            return [
                'headers' => [],
                'rows' => [],
            ];
        }
        
        // Verzamel alle unieke jaar-maand combinaties
        $allPeriods = collect();
        foreach ($allData as $typeData) {
            $allPeriods = $allPeriods->merge($typeData['data']->pluck('period'));
        }
        $allPeriods = $allPeriods->unique()->sortDesc()->values();
        
        // Bouw headers
        $headers = $allData->map(fn($typeData) => $typeData['type'])->values();
        
        // Bouw rows
        $rows = [];
        foreach ($allPeriods as $period) {
            $row = [
                'period' => $period,
                'date' => Carbon::parse($period . '-01'),
            ];
            
            foreach ($allData as $utilityTypeId => $typeData) {
                $consumption = $typeData['data']->get($period);
                $row[$utilityTypeId] = $consumption ? $consumption['consumption'] : null;
            }
            
            $rows[] = $row;
        }
        
        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }
}