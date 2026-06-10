<?php
// app/Services/FundPriceScraper.php

namespace App\Services\Investment;

use App\Models\InvestmentFund;
use App\Models\InvestmentRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FundPriceScraper
{
    /**
     * Scrape de koers van één fonds op basis van zijn URL.
     * De URL wordt opgeslagen in aandelen_fondsen.url
     */
    public function scrapeAndStore(InvestmentFund $fund): array
    {
        $html = $this->fetchPage($fund->url);
        if (! $html) {
            return ['status' => 'error', 'message' => 'Pagina niet bereikbaar'];
        }

        $result = $this->parseKoers($html, $fund->url);
        if (! $result) {
            Log::warning("Koers niet gevonden voor fonds [{$fund->naam}] op {$fund->url}");
            return ['status' => 'error', 'message' => 'Koers niet gevonden'];
        }

        [$datum, $koers] = $result;

        // Bestaat er al een koers voor dit fonds op deze datum?
        $bestaand = InvestmentRate::where('fondsID', $fund->id)
            ->where('datum', $datum)
            ->first();

        if ($bestaand) {
            if (abs($bestaand->dagkoers - $koers) < 0.01) {
                //Log::channel('financial')->info("Koers al up-to-date voor [{$fund->naam} => {$koers} EUR op {$datum}] ");
                return [
                    'status'   => 'ongewijzigd',
                    'datum'    => $datum,
                    'dagkoers' => $bestaand->dagkoers,
                ];
            }
            Log::channel('financial')->info("Bestaande koers gevonden, [{$fund->naam} => {$koers} EUR op {$datum}] ");
            return [
                'status'   => 'ongewijzigd',
                'datum'    => $datum,
                'dagkoers' => $bestaand->dagkoers,
            ];
        }

        $rate = InvestmentRate::create([
            'fondsID'  => $fund->id,
            'datum'    => $datum,
            'dagkoers' => $koers,
        ]);

        Log::channel('financial')->info("Nieuwe koers opgeslagen, [{$fund->naam} => {$koers} EUR op {$datum}] ");

        return [
            'status'   => 'nieuw',
            'datum'    => $datum,
            'dagkoers' => $rate->dagkoers,
        ];
    }

    /**
     * Scrape alle fondsen uit de database.
     */
    public function scrapeAll(): array
    {
        $results = [];

        InvestmentFund::all()->each(function (InvestmentFund $fund) use (&$results) {
            $rate = $this->scrapeAndStore($fund);
            if ($rate) {
                $results[] = [
                    'naam'    => $fund->naam,
                    'datum'   => $rate[0]->datum,
                    'dagkoers' => $rate[0]->dagkoers,
                ];
            }
        });

        return $results;
    }

    // -------------------------------------------------------------------------

    protected function fetchPage(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept-Language' => 'nl-BE,nl;q=0.9',
                'Accept'          => 'text/html,application/xhtml+xml',
            ])->timeout(15)->get($url);

            if ($response->ok()) {
                return $response->body();
            }

            Log::error("HTTP {$response->status()} bij ophalen van {$url}");
        } catch (\Exception $e) {
            Log::error("Fout bij ophalen van {$url}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Haal datum + koers uit de HTML van een moneyflow.be pagina.
     *
     * Beide pagina-types (life. en ars.) tonen de koers in de rankingtabel als:
     *   <td>16/03/2026</td><td ...>78,87eur</td>
     *
     * We zoeken specifiek de rij die de stock_id van de pagina zelf bevat,
     * of – als fallback – de eerste koersrij in de tabel.
     */
    protected function parseKoers(string $html, string $url): ?array
    {
        // De HTML bevat geëncodeerde JavaScript met de koers en datum
        // Decodeer eerst de HTML entities
        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Patroon: dashboardid1000 = koers, dashboardlblid1000 = datum
        // '1.426,64</div>...' en 'di 17/03/2026'
        if (preg_match(
            '/dashboardid\d+["\']?\)\.innerHTML\s*=\s*[\'"]([0-9.,]+)(?:<[^>]+>.*?)?[\'"].*?' .
                'dashboardlblid\d+["\']?\)\.innerHTML\s*=\s*[\'"][a-z]{2}\s+(\d{2}\/\d{2}\/\d{4})[\'"]/',
            $decoded,
            $m
        )) {
            return $this->parseMatch($m[2], $m[1]);
        }

        return null;
    }

    protected function parseMatch(string $datumStr, string $koersStr): array
    {
        $datum = Carbon::createFromFormat('d/m/Y', trim($datumStr))->toDateString(); // → "2026-03-17"
        $koers = (float) str_replace(',', '.', str_replace('.', '', trim($koersStr)));

        return [$datum, $koers];
    }
}
