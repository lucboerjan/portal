<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UtilityReading;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchP1Data extends Command
{
    protected $signature = 'p1:fetch';
    protected $description = 'Fetch P1 meter data and store in DB';

    public function handle()
{
    $response = Http::get(config('p1.meter_url'));

    if ($response->failed()) {
        $this->error('Failed to fetch P1 data');
        return;
    }

    $data = $response->json();
    $readingDate = now()->startOfMonth()->toDateString(); // eerste dag van de maand

    $readings = [
        6 => $data['total_power_import_t1_kwh'],  // Dagteller In
        7 => $data['total_power_import_t2_kwh'],  // Nachtteller In
        8 => $data['total_power_export_t1_kwh'],  // Dagteller Uit
        9 => $data['total_power_export_t2_kwh'],  // Nachtteller Uit
        4 => $data['total_gas_m3'],               // Gas
    ];

foreach ($readings as $typeId => $value) {
    // Verwijder alle records van deze maand voor dit type
    UtilityReading::where('utility_type_id', $typeId)
        ->whereYear('reading_date', now()->year)
        ->whereMonth('reading_date', now()->month)
        ->delete();

    // Maak nieuw record aan met vandaag als datum
    UtilityReading::create([
        'utility_type_id' => $typeId,
        'reading_date'    => now()->toDateString(),
        'meter_stand'     => $value,
    ]);
}

    $this->info('P1 data opgeslagen: ' . $readingDate);
  }
}
