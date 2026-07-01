<?php

namespace App\Console\Commands;

use App\Models\FinRekening;
use App\Models\FinRekeningStand;
use App\Models\FinTransactie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FinSlaRekeningStandenOp extends Command
{
    protected $description = 'Sla de actuele rekeningsstanden op voor de huidige maand';
    protected $signature = 'fin:rekening-standen-opslaan {--jaar= : Jaar} {--maand= : Maand}';

    public function handle(): void
    {
        $jaar  = $this->option('jaar')  ?? now()->year;
        $maand = $this->option('maand') ?? now()->month;

        $eindeDatum = \Carbon\Carbon::create($jaar, $maand, 1)->endOfMonth()->format('Y-m-d');

        $rekeningen = FinRekening::where('actief', true)->get();

        foreach ($rekeningen as $rekening) {
            $saldo = FinTransactie::where('rekening_id', $rekening->id)
                ->whereDate('datum', '<=', $eindeDatum)
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
        Log::channel('financial')->info('Rekeningstanden opgeslagen', ['maand' => $maand . '/' . $jaar]);
    }
}
