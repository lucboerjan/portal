<?php

namespace App\Filament\Resources\Utilities\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;
use App\Models\UtilityType;
use App\Models\UtilityReading;
use App\Models\UtilityCorrection;
use Illuminate\Support\Facades\Log;

class WaterConsumptionTable extends BaseWidget
{
    protected static ?string $heading = 'Overzichtstabel Verbruik Water';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // We gebruiken een lege Eloquent query
                UtilityReading::query()->whereRaw('1 = 0')
            )
            ->columns([
                Tables\Columns\TextColumn::make('jaar')
                    ->label('Jaar')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('jan')
                    ->label('Jan')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('feb')
                    ->label('Feb')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('maa')
                    ->label('Maa')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('apr')
                    ->label('Apr')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('mei')
                    ->label('Mei')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('jun')
                    ->label('Jun')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('jul')
                    ->label('Jul')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('aug')
                    ->label('Aug')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('sep')
                    ->label('Sep')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('okt')
                    ->label('Okt')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('nov')
                    ->label('Nov')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('dec')
                    ->label('Dec')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('totaal')
                    ->label('Totaal')
                    ->alignCenter()
                    ->weight('bold'),
            ])
            ->paginated(false)
            ->recordAction(null)
            ->recordUrl(null);
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record['jaar'];
    }

    public function getTableRecords(): Collection
    {
        // Haal utility type IDs op
        $watertellerId = UtilityType::where('name', 'Water')->value('id');

        // Haal alle unieke jaren op uit de readings, gesorteerd van nieuw naar oud
        $years = UtilityReading::selectRaw('DISTINCT YEAR(reading_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $data = collect();
        // Loop door alle jaren met data
        foreach ($years as $year) {
            $row = [
                'jaar' => $year,
                'totaal' => 0,
            ];

            // Loop door alle maanden
            for ($month = 1; $month <= 12; $month++) {
                //$monthDate = now()->setYear($year)->setMonth($month)->setDay(1);
                $monthDate = now()
                    ->setYear((int) $year)
                    ->setMonth((int) $month)
                    ->setDay(1);

                // Maandnamen mapping
                $monthNames = [
                    1 => 'jan',
                    2 => 'feb',
                    3 => 'maa',
                    4 => 'apr',
                    5 => 'mei',
                    6 => 'jun',
                    7 => 'jul',
                    8 => 'aug',
                    9 => 'sep',
                    10 => 'okt',
                    11 => 'nov',
                    12 => 'dec'
                ];

                $monthKey = $monthNames[$month];

                // Bereken verbruik voor deze maand
                $consumption = $this->calculateMonthlyConsumption(
                    month: $monthDate,
                    watertellerId: $watertellerId
                );

                $row[$monthKey] = $consumption > 0 ? (int)round($consumption) : '';

                if ($consumption > 0) {
                    $row['totaal'] += $consumption;
                }
            }

            $row['totaal'] = $row['totaal'] > 0 ? (int)round($row['totaal']) : '';

            $data->push($row);
        }
        return $data;
    }


    private function calculateMonthlyConsumption(

        $month,
        $watertellerId

    ): float {


        $startOfMonth = $month->copy()->startOfMonth()->toDateString();
        $endOfMonth = $month->copy()->endOfMonth()->toDateString();

        // Bereken verschil voor elke meter
        $waterIn = $this->getMeterDifference($watertellerId, $startOfMonth, $endOfMonth);


        // Formule: (Dagteller In + Nachteller In) - (Dagteller Uit + Nachteller Uit) + Zonnepanelen
        $consumption = $waterIn;

        return $consumption;
    }

    private function _oldgetMeterDifference(?int $utilityTypeId, string $startOfMonth, string $endOfMonth): float
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
            //return 0;
            $difference = 0;
        } else {
            // Bereken basis verschil = huidige stand - vorige stand
            $difference = $currentReading->meter_stand - $previousReading->meter_stand;
        }





        // Check of er een correctie is in deze periode (metervervanging)
        if ($difference < 0 || $utilityTypeId == 3) {
            $correction = UtilityCorrection::where('utility_type_id', $utilityTypeId)
                ->whereBetween('correction_date', [$startOfMonth, $endOfMonth])
                ->sum('old_meter_final_reading');

            // dd($correction);
            if ($correction != 0) {
                //dd($correction);
                $difference = $correction - $previousReading->meter_stand + $currentReading->meter_stand;
            }
        }

        return $difference;
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

        // Check ALTIJD of er een correctie is in deze periode (metervervanging)
        // Dit geldt zowel voor negatieve verschillen (meter ging terug naar 0)
        // als voor abnormaal hoge verschillen (nieuwe meter met hoge beginstand)
        $correction = UtilityCorrection::where('utility_type_id', $utilityTypeId)
            ->whereBetween('correction_date', [$startOfMonth, $endOfMonth])
            ->first();

        if ($correction) {
            // Formule: (oude meter eindstand - vorige reading) + (huidige reading - nieuwe meter beginstand)
            // Dit geeft het werkelijke verbruik over de meterwissel heen
            $difference = ($correction->old_meter_final_reading - $previousReading->meter_stand)
                + ($currentReading->meter_stand - $correction->new_meter_start_reading);

            Log::info("old_meter_final_reading : $correction->old_meter_final_reading");
            Log::info("previousReading->meter_stand : $previousReading->meter_stand");
            Log::info("currentReading->meter_stand : $currentReading->meter_stand");
            Log::info("new_meter_start_reading : $correction->new_meter_start_reading");
        }
        return max(0, $difference); // Voorkom negatieve waarden
    }
}
