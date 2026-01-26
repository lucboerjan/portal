<?php

namespace Database\Seeders;

use App\Models\UtilityReading;
use App\Models\UtilityType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateOldTellerstandSeeder extends Seeder
{
    public function run(): void
    {
        // Haal utility types op
        $types = [
            'dag' => UtilityType::where('name', 'Elektriciteit Dag')->first(),
            'nacht' => UtilityType::where('name', 'Elektriciteit Nacht')->first(),
            'water' => UtilityType::where('name', 'Water')->first(),
            'gas' => UtilityType::where('name', 'Gas')->first(),
            'zon' => UtilityType::where('name', 'Zonnepanelen')->first(),
            'dagteller_in' => UtilityType::where('name', 'Dagteller In')->first(),
            'nachtteller_in' => UtilityType::where('name', 'Nachtteller In')->first(),
            'dagteller_uit' => UtilityType::where('name', 'Dagteller Uit')->first(),
            'nachtteller_uit' => UtilityType::where('name', 'Nachtteller Uit')->first(),
        ];

        // Haal oude data op
        $oldReadings = DB::table('tellerstand')->orderBy('datum')->get();

        foreach ($oldReadings as $reading) {
            $date = $reading->datum;
            
            // Map elke kolom naar een utility reading
            $mappings = [
                'dag' => $reading->dag,
                'nacht' => $reading->nacht,
                'water' => $reading->water,
                'gas' => $reading->gas,
                'zon' => $reading->zon,
                'dagteller_in' => $reading->dagteller_in,
                'nachtteller_in' => $reading->nachtteller_in,
                'dagteller_uit' => $reading->dagteller_uit,
                'nachtteller_uit' => $reading->nachtteller_uit,
            ];

            foreach ($mappings as $key => $value) {
                // Sla alleen op als waarde > 0
                if ($value > 0 && isset($types[$key])) {
                    UtilityReading::updateOrCreate(
                        [
                            'utility_type_id' => $types[$key]->id,
                            'reading_date' => $date,
                        ],
                        [
                            'meter_stand' => $value,
                            'created_at' => $reading->created_at,
                            'updated_at' => $reading->updated_at,
                        ]
                    );
                }
            }
        }

        $this->command->info('Migratie voltooid! ' . $oldReadings->count() . ' oude records verwerkt.');
    }
}