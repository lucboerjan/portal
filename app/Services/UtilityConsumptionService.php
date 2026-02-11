<?php
// app/Services/UtilityConsumptionService.php
namespace App\Services;

use App\Models\UtilityReading;
use Illuminate\Support\Carbon;

class UtilityConsumptionService
{
    public function getMonthlyConsumption(int $utilityTypeId)
    {
        $readings = UtilityReading::where('utility_type_id', $utilityTypeId)
            ->orderBy('reading_date', 'desc')
            ->get();
        
        if ($readings->isEmpty()) {
            return collect();
        }
        
        $monthlyData = [];
        $previousReading = null;
        
        // Groepeer per maand en neem de laatste reading van elke maand
        $monthlyReadings = $readings->groupBy(function($reading) {
            $date = $reading->reading_date instanceof Carbon 
                ? $reading->reading_date 
                : Carbon::parse($reading->reading_date);
            return $date->format('Y-m');
        })->map(function($monthGroup) {
            // Sorteer en pak de laatste reading van de maand
            return $monthGroup->sortByDesc('reading_date')->first();
        });
        
        foreach ($monthlyReadings as $period => $reading) {
            $date = $reading->reading_date instanceof Carbon 
                ? $reading->reading_date 
                : Carbon::parse($reading->reading_date);
            
            $consumption = 0;
            
            if ($previousReading) {
                $consumption = $reading->meter_stand - $previousReading->meter_stand;
            }
            
            $monthlyData[] = [
                'year' => $date->year,
                'month' => $date->month,
                'consumption' => max(0, $consumption), // Voorkom negatieve waarden
                'period' => $period,
                'meter_stand' => $reading->meter_stand,
            ];
            
            $previousReading = $reading;
        }
        
        return collect($monthlyData);
    }
}