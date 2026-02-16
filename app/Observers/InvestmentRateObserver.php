<?php

namespace App\Observers;

use App\Models\InvestmentRate;
use Illuminate\Support\Facades\Log;

class InvestmentRateObserver
{
    /**
     * Mapping van bron fonds ID naar doel fonds ID
     * Pas deze aan naar jouw specifieke fonds IDs
     */
    private const FUND_MAPPING = [
        3 => 4,
        4 => 3,
    ];

    private static bool $syncing = false; // Dit voorkomt de loop!

    /**
     * Handle the InvestmentRate "saved" event.
     * Dit triggert bij zowel create als update
     */
    public function saved(InvestmentRate $investmentRate): void
    {
        if (self::$syncing) {
            return;
        }
        // Check of dit fonds een gekoppeld fonds heeft
        if (!isset(self::FUND_MAPPING[$investmentRate->fondsID])) {
            return;
        }

        $targetFondsId = self::FUND_MAPPING[$investmentRate->fondsID];
        Log::info("Todo InvestmentRate bijwerken voor fonds {$targetFondsId}");

        try {
            self::$syncing = true;
            // Zoek of er al een record bestaat voor dit fonds en deze datum
            $existingRate = InvestmentRate::where('fondsID', $targetFondsId)
                ->where('datum', $investmentRate->datum)
                ->first();

            if ($existingRate) {
                // Update bestaand record
                $existingRate->update([
                    'dagkoers' => $investmentRate->dagkoers,
                    // Voeg hier andere velden toe die je wilt synchroniseren
                ]);

                Log::info("InvestmentRate bijgewerkt voor fonds {$targetFondsId}", [
                    'datum' => $investmentRate->datum,
                    'dagkoers' => $investmentRate->dagkoers
                ]);
            } else {
                // Maak nieuw record
                InvestmentRate::create([
                    'fondsID' => $targetFondsId,
                    'datum' => $investmentRate->datum,
                    'dagkoers' => $investmentRate->dagkoers,
                    // Voeg hier andere velden toe die je wilt synchroniseren
                ]);

                Log::info("InvestmentRate aangemaakt voor fonds {$targetFondsId}", [
                    'datum' => $investmentRate->datum,
                    'dagkoers' => $investmentRate->dagkoers
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Fout bij synchroniseren InvestmentRate naar fonds {$targetFondsId}: " . $e->getMessage());
        } finally {
            // Reset de syncing flag - BELANGRIJK voor het voorkomen van loops!
            self::$syncing = false;
        }
    }

    /**
     * Handle the InvestmentRate "deleted" event.
     * Optioneel: synchroniseer ook deletes
     */
    public function deleted(InvestmentRate $investmentRate): void
    {

        if (self::$syncing) {
            return;
        }
        if (!isset(self::FUND_MAPPING[$investmentRate->fondsID])) {
            return;
        }

        $targetFondsId = self::FUND_MAPPING[$investmentRate->fondsID];

        try {
            self::$syncing = true;
            InvestmentRate::where('fondsID', $targetFondsId)
                ->where('datum', $investmentRate->datum)
                ->delete();

            Log::info("InvestmentRate verwijderd voor fonds {$targetFondsId}", [
                'datum' => $investmentRate->datum
            ]);
        } catch (\Exception $e) {
            Log::error("Fout bij verwijderen InvestmentRate voor fonds {$targetFondsId}: " . $e->getMessage());
        } finally {
            self::$syncing = false;
        }
    }
}
