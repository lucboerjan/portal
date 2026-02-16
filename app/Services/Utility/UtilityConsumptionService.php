<?php
// app/Services/UtilityConsumptionService.php

namespace App\Services\Utility;

use App\Models\UtilityReading;
use App\Models\UtilityType;
use App\Models\UtilityCorrection;
use Illuminate\Support\Carbon;

class UtilityConsumptionService
{
    /**
     * Haal tabelstructuur op gefilterd op utility type IDs en startdatum
     */
    public function getTableStructureFiltered(array $utilityTypeIds, string $startDate)
    {
        // Haal alleen de specifieke utility types op
        $utilityTypes = UtilityType::whereIn('id', $utilityTypeIds)
            ->orderBy('id')
            ->get();
        
        if ($utilityTypes->isEmpty()) {
            return [
                'headers' => [],
                'rows' => [],
                'calculated_columns' => [],
            ];
        }
        
        $allData = [];
        foreach ($utilityTypes as $type) {
            $consumption = $this->getMonthlyConsumptionForTypeFiltered($type->id, $startDate);
            $allData[$type->id] = [
                'type' => $type,
                'data' => $consumption
            ];
        }
        
        // Verzamel alle unieke periodes
        $allPeriods = collect();
        foreach ($allData as $typeData) {
            $allPeriods = $allPeriods->merge($typeData['data']->pluck('period'));
        }
        $allPeriods = $allPeriods->unique()->sortDesc()->values();
        
        // Bouw headers
        $headers = collect($allData)->map(fn($typeData) => $typeData['type'])->values();
        
        // Voeg berekende kolommen toe
        $calculatedColumns = [
            [
                'id' => 'afgenomen',
                'name' => 'Afgenomen',
                'description' => 'Dagteller In + Nachtteller In',
                'unit' => 'kWh',
            ],
            [
                'id' => 'geïnjecteerd',
                'name' => 'Geïnjecteerd',
                'description' => 'Dagteller Uit + Nachtteller Uit',
                'unit' => 'kWh',
            ],
        ];
        
        // Bouw rows
        $rows = [];
        foreach ($allPeriods as $period) {
            $row = [
                'period' => $period,
                'date' => Carbon::parse($period . '-01'),
            ];
            
            // Voeg individuele meter waarden toe
            foreach ($allData as $utilityTypeId => $typeData) {
                $consumption = $typeData['data']->get($period);
                $row[$utilityTypeId] = $consumption ? $consumption['consumption'] : null;
            }
            
            // Bereken afgenomen (ID 6 + ID 7)
            $dagtellerIn = isset($row[6]) ? $row[6] : 0;
            $nachttellerIn = isset($row[7]) ? $row[7] : 0;
            $row['afgenomen'] = ($dagtellerIn !== null && $nachttellerIn !== null) 
                ? $dagtellerIn + $nachttellerIn 
                : null;
            
            // Bereken geïnjecteerd (ID 8 + ID 9)
            $dagtellerUit = isset($row[8]) ? $row[8] : 0;
            $nachttellerUit = isset($row[9]) ? $row[9] : 0;
            $row['geïnjecteerd'] = ($dagtellerUit !== null && $nachttellerUit !== null) 
                ? $dagtellerUit + $nachttellerUit 
                : null;
            
            $rows[] = $row;
        }
        
        return [
            'headers' => $headers,
            'rows' => $rows,
            'calculated_columns' => $calculatedColumns,
        ];
    }
    
    /**
     * Bereken maandelijks verbruik voor een specifiek utility type met correcties
     */
    public function getMonthlyConsumptionForTypeFiltered(int $utilityTypeId, string $startDate)
    {
        $readings = UtilityReading::where('utility_type_id', $utilityTypeId)
            ->where('reading_date', '>=', $startDate)
            ->orderBy('reading_date')
            ->get();
        
        // Haal correcties op voor deze utility type
        $corrections = UtilityCorrection::where('utility_type_id', $utilityTypeId)
            ->where('correction_date', '>=', $startDate)
            ->orderBy('correction_date')
            ->get();
        
        if ($readings->count() < 2) {
            return collect();
        }
        
        $monthlyData = [];
        
        for ($i = 1; $i < $readings->count(); $i++) {
            $current = $readings[$i];
            $previous = $readings[$i - 1];
            
            $currentDate = Carbon::parse($current->reading_date);
            $previousDate = Carbon::parse($previous->reading_date);
            
            // Check of er een correctie (meterwisseling) tussen deze twee readings zit
            $correctionBetween = $corrections->first(function($correction) use ($previousDate, $currentDate) {
                $corrDate = Carbon::parse($correction->correction_date);
                return $corrDate->greaterThan($previousDate) && $corrDate->lessThanOrEqualTo($currentDate);
            });
            
            if ($correctionBetween) {
                // Meterwisseling gevonden
                // Verbruik = (oude meter final - vorige reading) + (huidige reading - nieuwe meter start)
                $oldMeterConsumption = $correctionBetween->old_meter_final_reading - $previous->meter_stand;
                $newMeterConsumption = $current->meter_stand - $correctionBetween->new_meter_start_reading;
                $consumption = $oldMeterConsumption + $newMeterConsumption;
            } else {
                // Normale berekening zonder meterwisseling
                $consumption = $current->meter_stand - $previous->meter_stand;
            }
            
            // Voorkom negatieve waarden (zou niet moeten voorkomen met correcte data)
            if ($consumption < 0) {
                $consumption = 0;
            }
            
            $yearMonth = $currentDate->format('Y-m');
            
            // Als readings in verschillende maanden zijn, voeg toe aan maanddata
            if ($currentDate->format('Y-m') !== $previousDate->format('Y-m')) {
                $monthlyData[$yearMonth] = [
                    'year' => $currentDate->year,
                    'month' => $currentDate->month,
                    'consumption' => $consumption,
                    'period' => $yearMonth,
                    'date' => $currentDate,
                ];
            }
        }
        
        return collect($monthlyData);
    }
    
    /**
     * Genereer chart data gefilterd op utility type IDs en startdatum
     * Inclusief berekende kolommen
     */
    public function getChartDataFiltered(array $utilityTypeIds, string $startDate)
    {
        // Haal utility types op
        $utilityTypes = UtilityType::whereIn('id', $utilityTypeIds)
            ->orderBy('id')
            ->get();
        
        if ($utilityTypes->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
        
        $allData = [];
        foreach ($utilityTypes as $type) {
            $consumption = $this->getMonthlyConsumptionForTypeFiltered($type->id, $startDate);
            $allData[$type->id] = [
                'type' => $type,
                'data' => $consumption
            ];
        }
        
        // Verzamel alle periodes en sorteer oplopend voor de grafiek
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
                'spanGaps' => true,
            ];
            
            $colorIndex++;
        }
        
        // Voeg berekende datasets toe
        // Afgenomen (ID 6 + ID 7)
        $afgenomenValues = [];
        foreach ($allPeriods as $period) {
            $dag = $allData[6]['data']->get($period);
            $nacht = $allData[7]['data']->get($period);
            
            if ($dag && $nacht) {
                $afgenomenValues[] = (float) ($dag['consumption'] + $nacht['consumption']);
            } else {
                $afgenomenValues[] = null;
            }
        }
        
        $datasets[] = [
            'label' => 'Afgenomen',
            'data' => $afgenomenValues,
            'borderColor' => $this->getColor(4),
            'backgroundColor' => $this->getColor(4, 0.1),
            'tension' => 0.4,
            'spanGaps' => true,
            'borderWidth' => 3,
            'borderDash' => [5, 5], // Stippellijn
        ];
        
        // Geïnjecteerd (ID 8 + ID 9)
        $geinjicteerdValues = [];
        foreach ($allPeriods as $period) {
            $dag = $allData[8]['data']->get($period);
            $nacht = $allData[9]['data']->get($period);
            
            if ($dag && $nacht) {
                $geinjicteerdValues[] = (float) ($dag['consumption'] + $nacht['consumption']);
            } else {
                $geinjicteerdValues[] = null;
            }
        }
        
        $datasets[] = [
            'label' => 'Geïnjecteerd',
            'data' => $geinjicteerdValues,
            'borderColor' => $this->getColor(5),
            'backgroundColor' => $this->getColor(5, 0.1),
            'tension' => 0.4,
            'spanGaps' => true,
            'borderWidth' => 3,
            'borderDash' => [5, 5], // Stippellijn
        ];
        
        // Format labels (bijv. "2025-01" -> "Jan 2025")
        $labels = $allPeriods->map(function($period) {
            $date = Carbon::parse($period . '-01');
            return $date->format('M Y');
        })->toArray();
        
        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
    
    // ===================================================================
    // JAARLIJKSE VERSIES VAN DE METHODES
    // ===================================================================
    
    /**
     * Haal tabelstructuur op gefilterd op utility type IDs en startdatum - JAARLIJKS
     */
    public function getTableStructureFilteredYearly(array $utilityTypeIds, string $startDate)
    {
        // Haal alleen de specifieke utility types op
        $utilityTypes = UtilityType::whereIn('id', $utilityTypeIds)
            ->orderBy('id')
            ->get();
        
        if ($utilityTypes->isEmpty()) {
            return [
                'headers' => [],
                'rows' => [],
                'calculated_columns' => [],
            ];
        }
        
        $allData = [];
        foreach ($utilityTypes as $type) {
            $consumption = $this->getYearlyConsumptionForTypeFiltered($type->id, $startDate);
            $allData[$type->id] = [
                'type' => $type,
                'data' => $consumption
            ];
        }
        
        // Verzamel alle unieke jaren
        $allYears = collect();
        foreach ($allData as $typeData) {
            $allYears = $allYears->merge($typeData['data']->pluck('year'));
        }
        $allYears = $allYears->unique()->sortDesc()->values();
        
        // Bouw headers
        $headers = collect($allData)->map(fn($typeData) => $typeData['type'])->values();
        
        // Voeg berekende kolommen toe
        $calculatedColumns = [
            [
                'id' => 'afgenomen',
                'name' => 'Afgenomen',
                'description' => 'Dagteller In + Nachtteller In',
                'unit' => 'kWh',
            ],
            [
                'id' => 'geïnjecteerd',
                'name' => 'Geïnjecteerd',
                'description' => 'Dagteller Uit + Nachtteller Uit',
                'unit' => 'kWh',
            ],
        ];
        
        // Bouw rows
        $rows = [];
        foreach ($allYears as $year) {
            $row = [
                'period' => $year,
                'date' => Carbon::parse($year . '-01-01'),
            ];
            
            // Voeg individuele meter waarden toe
            foreach ($allData as $utilityTypeId => $typeData) {
                $consumption = $typeData['data']->get($year);
                $row[$utilityTypeId] = $consumption ? $consumption['consumption'] : null;
            }
            
            // Bereken afgenomen (ID 6 + ID 7)
            $dagtellerIn = isset($row[6]) ? $row[6] : 0;
            $nachttellerIn = isset($row[7]) ? $row[7] : 0;
            $row['afgenomen'] = ($dagtellerIn !== null && $nachttellerIn !== null) 
                ? $dagtellerIn + $nachttellerIn 
                : null;
            
            // Bereken geïnjecteerd (ID 8 + ID 9)
            $dagtellerUit = isset($row[8]) ? $row[8] : 0;
            $nachttellerUit = isset($row[9]) ? $row[9] : 0;
            $row['geïnjecteerd'] = ($dagtellerUit !== null && $nachttellerUit !== null) 
                ? $dagtellerUit + $nachttellerUit 
                : null;
            
            $rows[] = $row;
        }
        
        return [
            'headers' => $headers,
            'rows' => $rows,
            'calculated_columns' => $calculatedColumns,
        ];
    }
    
    /**
     * Bereken jaarlijks verbruik voor een specifiek utility type met correcties
     */
    public function getYearlyConsumptionForTypeFiltered(int $utilityTypeId, string $startDate)
    {
        $readings = UtilityReading::where('utility_type_id', $utilityTypeId)
            ->where('reading_date', '>=', $startDate)
            ->orderBy('reading_date')
            ->get();
        
        // Haal correcties op voor deze utility type
        $corrections = UtilityCorrection::where('utility_type_id', $utilityTypeId)
            ->where('correction_date', '>=', $startDate)
            ->orderBy('correction_date')
            ->get();
        
        if ($readings->count() < 2) {
            return collect();
        }
        
        $yearlyData = [];
        
        for ($i = 1; $i < $readings->count(); $i++) {
            $current = $readings[$i];
            $previous = $readings[$i - 1];
            
            $currentDate = Carbon::parse($current->reading_date);
            $previousDate = Carbon::parse($previous->reading_date);
            
            // Check of er een correctie (meterwisseling) tussen deze twee readings zit
            $correctionBetween = $corrections->first(function($correction) use ($previousDate, $currentDate) {
                $corrDate = Carbon::parse($correction->correction_date);
                return $corrDate->greaterThan($previousDate) && $corrDate->lessThanOrEqualTo($currentDate);
            });
            
            if ($correctionBetween) {
                // Meterwisseling gevonden
                $oldMeterConsumption = $correctionBetween->old_meter_final_reading - $previous->meter_stand;
                $newMeterConsumption = $current->meter_stand - $correctionBetween->new_meter_start_reading;
                $consumption = $oldMeterConsumption + $newMeterConsumption;
            } else {
                // Normale berekening zonder meterwisseling
                $consumption = $current->meter_stand - $previous->meter_stand;
            }
            
            // Voorkom negatieve waarden
            if ($consumption < 0) {
                $consumption = 0;
            }
            
            $year = $currentDate->year;
            
            // Tel verbruik op per jaar
            if (!isset($yearlyData[$year])) {
                $yearlyData[$year] = [
                    'year' => $year,
                    'consumption' => 0,
                    'date' => Carbon::parse($year . '-01-01'),
                ];
            }
            
            $yearlyData[$year]['consumption'] += $consumption;
        }
        
        return collect($yearlyData);
    }
    
    /**
     * Genereer chart data gefilterd op utility type IDs en startdatum - JAARLIJKS
     * Inclusief berekende kolommen
     */
    public function getChartDataFilteredYearly(array $utilityTypeIds, string $startDate)
    {
        // Haal utility types op
        $utilityTypes = UtilityType::whereIn('id', $utilityTypeIds)
            ->orderBy('id')
            ->get();
        
        if ($utilityTypes->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
        
        $allData = [];
        foreach ($utilityTypes as $type) {
            $consumption = $this->getYearlyConsumptionForTypeFiltered($type->id, $startDate);
            $allData[$type->id] = [
                'type' => $type,
                'data' => $consumption
            ];
        }
        
        // Verzamel alle jaren en sorteer oplopend voor de grafiek
        $allYears = collect();
        foreach ($allData as $typeData) {
            $allYears = $allYears->merge($typeData['data']->pluck('year'));
        }
        $allYears = $allYears->unique()->sort()->values();
        
        // Bouw datasets per utility type
        $datasets = [];
        $colorIndex = 0;
        
        foreach ($allData as $utilityTypeId => $typeData) {
            $values = [];
            
            foreach ($allYears as $year) {
                $consumption = $typeData['data']->get($year);
                $values[] = $consumption ? (float) $consumption['consumption'] : null;
            }
            
            $datasets[] = [
                'label' => $typeData['type']->name,
                'data' => $values,
                'borderColor' => $this->getColor($colorIndex),
                'backgroundColor' => $this->getColor($colorIndex, 0.1),
                'tension' => 0.4,
                'spanGaps' => true,
            ];
            
            $colorIndex++;
        }
        
        // Voeg berekende datasets toe
        // Afgenomen (ID 6 + ID 7)
        $afgenomenValues = [];
        foreach ($allYears as $year) {
            $dag = $allData[6]['data']->get($year);
            $nacht = $allData[7]['data']->get($year);
            
            if ($dag && $nacht) {
                $afgenomenValues[] = (float) ($dag['consumption'] + $nacht['consumption']);
            } else {
                $afgenomenValues[] = null;
            }
        }
        
        $datasets[] = [
            'label' => 'Afgenomen',
            'data' => $afgenomenValues,
            'borderColor' => $this->getColor(4),
            'backgroundColor' => $this->getColor(4, 0.1),
            'tension' => 0.4,
            'spanGaps' => true,
            'borderWidth' => 3,
            'borderDash' => [5, 5], // Stippellijn
        ];
        
        // Geïnjecteerd (ID 8 + ID 9)
        $geinjicteerdValues = [];
        foreach ($allYears as $year) {
            $dag = $allData[8]['data']->get($year);
            $nacht = $allData[9]['data']->get($year);
            
            if ($dag && $nacht) {
                $geinjicteerdValues[] = (float) ($dag['consumption'] + $nacht['consumption']);
            } else {
                $geinjicteerdValues[] = null;
            }
        }
        
        $datasets[] = [
            'label' => 'Geïnjecteerd',
            'data' => $geinjicteerdValues,
            'borderColor' => $this->getColor(5),
            'backgroundColor' => $this->getColor(5, 0.1),
            'tension' => 0.4,
            'spanGaps' => true,
            'borderWidth' => 3,
            'borderDash' => [5, 5], // Stippellijn
        ];
        
        // Format labels (alleen het jaar)
        $labels = $allYears->map(function($year) {
            return (string) $year;
        })->toArray();
        
        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
    
    /**
     * Haal kleuren op voor de grafieken
     */
    private function getColor(int $index, float $alpha = 1)
    {
        $colors = [
            'rgb(59, 130, 246)',   // blue - Dagteller In (ID 6)
            'rgb(16, 185, 129)',   // green - Nachtteller In (ID 7)
            'rgb(245, 158, 11)',   // amber - Dagteller Uit (ID 8)
            'rgb(239, 68, 68)',    // red - Nachtteller Uit (ID 9)
            'rgb(99, 102, 241)',   // indigo - Afgenomen (berekend)
            'rgb(236, 72, 153)',   // pink - Geïnjecteerd (berekend)
        ];
        
        $color = $colors[$index % count($colors)];
        
        if ($alpha < 1) {
            return str_replace('rgb', 'rgba', str_replace(')', ", $alpha)", $color));
        }
        
        return $color;
    }
}