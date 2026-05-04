<?php

namespace App\Console\Commands;

use App\Models\FinRekening;
use App\Models\FinRekeningStand;
use App\Models\FinTransactie;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FinHerbereken extends Command
{
    protected $signature   = 'fin:herbereken-standen {--vanaf= : Startmaand (formaat: YYYY-MM)} {--droog : Voer geen wijzigingen door}';
    protected $description = 'Herbereken rekeningstanden voor alle maanden';

    public function handle(): void
    {
        $droog = $this->option('droog');

        // Vroegste transactiedatum ophalen
        $vroegste = FinTransactie::min('datum');
        if (!$vroegste) {
            $this->warn('Geen transacties gevonden.');
            return;
        }

        // Optioneel overschrijven via --vanaf
        if ($vanaf = $this->option('vanaf')) {
            $start = Carbon::createFromFormat('Y-m', $vanaf)->startOfMonth();
        } else {
            $start = Carbon::parse($vroegste)->startOfMonth();
        }

        $einde = now()->startOfMonth();

        if ($droog) {
            $this->warn('🔍 DROGE RUN — geen wijzigingen worden opgeslagen');
        }

        $rekeningen = FinRekening::where('actief', true)->get();
        $periode    = $start->copy();
        $totaal     = 0;

        $this->info("Herberekening van {$start->format('m/Y')} tot {$einde->format('m/Y')}");
        $this->newLine();

        while ($periode->lte($einde)) {
            $jaar  = $periode->year;
            $maand = $periode->month;
            $eindeDatum = $periode->copy()->endOfMonth()->format('Y-m-d');

            $this->info("📅 {$periode->format('m/Y')}");

            foreach ($rekeningen as $rekening) {
                $saldo = FinTransactie::where('rekening_id', $rekening->id)
                    ->whereDate('datum', '<=', $eindeDatum)
                    ->sum('bedrag');

                if (!$droog) {
                    FinRekeningStand::updateOrCreate(
                        [
                            'rekening_id' => $rekening->id,
                            'jaar'        => $jaar,
                            'maand'       => $maand,
                        ],
                        ['saldo' => $saldo]
                    );
                }

                $this->line("  {$rekening->omschrijving}: € " . number_format($saldo, 2, ',', '.'));
                $totaal++;
            }

            $periode->addMonth();
        }

        $this->newLine();
        $this->info("✅ {$totaal} standen " . ($droog ? 'gecontroleerd' : 'opgeslagen'));
    }
}