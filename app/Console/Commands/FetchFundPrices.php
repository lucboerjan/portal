<?php
// app/Console/Commands/FetchFundPrices.php

namespace App\Console\Commands;

use App\Services\Investment\FundPriceScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchFundPrices extends Command
{
    protected $signature   = 'funds:fetch {--id= : Optioneel: scrape slechts één fonds op ID}';
    protected $description = 'Haal de laatste beurskoersen op voor alle beleggingsfondsen';

public function handle(FundPriceScraper $scraper): int
{
    $fondsen = \App\Models\InvestmentFund::all();
    $nieuw   = 0;
    //Log::channel('financial')->info(" Koersen fondsen ophalen gestart...");
    foreach ($fondsen as $fund) {
        $result = $scraper->scrapeAndStore($fund);
        $datum  = isset($result['datum']) 
            ? \Carbon\Carbon::parse($result['datum'])->format('d/m/Y') 
            : '—';

        match ($result['status']) {
            'nieuw'       => ($this->line("✓ NIEUW  {$fund->naam}: {$result['dagkoers']} EUR ({$datum})") 
                              && $nieuw++),
            'ongewijzigd' => $this->line("  —      {$fund->naam}: geen nieuwe koers ({$datum})"),
            'error'       => $this->error("✗ FOUT   {$fund->naam}: {$result['message']}"),
        };
    }

    if ($nieuw > 0) {
        $this->info("{$nieuw} nieuwe koers(en) opgeslagen.");
    } else {
        $this->info('Geen nieuwe koersen gevonden.');
    }

    return self::SUCCESS;
}
}
