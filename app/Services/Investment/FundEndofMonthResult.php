<?php
// app/Services/Investment/FundEndofMonthResult.php

namespace App\Services\Investment;

use App\Models\InvestmentFund;
use App\Models\FinRekening;
use App\Models\FinTransactie;
use App\Models\FinTransactieCategorie;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FundEndofMonthResult
{
    public function readAndStore(InvestmentFund $fund): void
    {
        // Valideer rekening
        $rekening = FinRekening::find($fund->rekening_id);
        if (!$rekening) {
            Log::warning("Geen rekening gevonden voor fonds: {$fund->naam} (rekening_id: {$fund->rekening_id})");
            return;
        }

        // Valideer waarden
        $huidigeWaarde = $fund->huidigeWaarde;
        $rekeningStand = $rekening->saldo;

        Log::info("Verwerken fonds: {$fund->naam}", [
            'huidigeWaarde' => $huidigeWaarde,
            'rekeningStand' => $rekeningStand,
            'rekening_id'   => $rekening->id,
        ]);

        if (is_null($huidigeWaarde) || is_null($rekeningStand)) {
            Log::warning("Ontbrekende waarden voor fonds: {$fund->naam}", [
                'huidigeWaarde' => $huidigeWaarde,
                'rekeningStand' => $rekeningStand,
            ]);
            return;
        }

        $aangroeiBedrag = round($huidigeWaarde - $rekeningStand, 2);
        $datum = Carbon::now()->lastOfMonth()->toDateString();

        // Transactie aanmaken of bijwerken indien het verschil niet nul is
        if ($aangroeiBedrag != 0) {
            $transactie = FinTransactie::updateOrCreate(
                [
                    'rekening_id' => $rekening->id,
                    'datum'       => $datum,
                    'omschrijving' => 'Aangroei',
                ],
                [
                    'bedrag' => $aangroeiBedrag,
                ]
            );
        }

        Log::info("Transactie verwerkt voor {$fund->naam}", [
            'transactie_id' => $transactie->id,
            'aangroeiBedrag' => $aangroeiBedrag,
            'datum' => $datum,
        ]);

        // Transactiecategorie aanmaken of bijwerken
        FinTransactieCategorie::updateOrCreate(
            [
                'transactie_id' => $transactie->id,
            ],
            [
                'datum'       => $datum,
                'categorie_id' => 16,
                'bedrag'      => $aangroeiBedrag,
            ]
        );

        Log::info("Klaar met verwerken fonds: {$fund->naam}");
    }
}
