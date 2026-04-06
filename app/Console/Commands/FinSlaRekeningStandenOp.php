<?php

namespace App\Console\Commands;

use App\Models\FinRekening;
use App\Models\FinRekeningStand;
use App\Models\FinTransactie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FinSlaRekeningStandenOp extends Command
{
    protected $signature   = 'fin:rekening-standen-opslaan';
    protected $description = 'Sla de actuele rekeningsstanden op voor de huidige maand';

    public function handle(): void
    {
        // Vorige maand berekenen
        //$datum = now()->subMonth();
        $datum = now();
        $jaar  = $datum->year;
        $maand = $datum->month;
        $dag   = $datum->day;

        // Einde van vorige maand
        $eindeDatum = $datum->endOfMonth()->format('Y-m-d');

        $rekeningen = FinRekening::where('actief', true)->get();

        foreach ($rekeningen as $rekening) {
            $saldo = FinTransactie::where('rekening_id', $rekening->id)
                //->whereDate('datum', '<=', $eindeDatum)
                ->sum('bedrag');

            FinRekeningStand::updateOrCreate(
                [
                    'rekening_id' => $rekening->id,
                    'jaar'        => $jaar,
                    'maand'       => $maand,
                ],
                [
                    'saldo' => $saldo,
                ]
            );

            $this->info("{$rekening->omschrijving}: € " . number_format($saldo, 2, ',', '.'));
        }

        $this->info('✅ Rekeningstanden opgeslagen voor ' . $maand . '/' . $jaar);
        Log::channel('financial')->info('Rekeningstanden opgeslagen ', ['datum' => $dag . '/' . $maand . '/' . $jaar]);
    }
}
