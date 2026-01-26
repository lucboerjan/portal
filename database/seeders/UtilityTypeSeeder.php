<?php

namespace Database\Seeders;

use App\Models\UtilityType;
use Illuminate\Database\Seeder;

class UtilityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Elektriciteit Dag', 'unit' => 'kWh', 'type' => 'electricity'],
            ['name' => 'Elektriciteit Nacht', 'unit' => 'kWh', 'type' => 'electricity'],
            ['name' => 'Water', 'unit' => 'm³', 'type' => 'water'],
            ['name' => 'Gas', 'unit' => 'm³', 'type' => 'gas'],
            ['name' => 'Zonnepanelen', 'unit' => 'kWh', 'type' => 'solar'],
            ['name' => 'Dagteller In', 'unit' => 'kWh', 'type' => 'electricity_in'],
            ['name' => 'Nachtteller In', 'unit' => 'kWh', 'type' => 'electricity_in'],
            ['name' => 'Dagteller Uit', 'unit' => 'kWh', 'type' => 'electricity_out'],
            ['name' => 'Nachtteller Uit', 'unit' => 'kWh', 'type' => 'electricity_out'],
        ];

        foreach ($types as $type) {
            UtilityType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}