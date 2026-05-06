<?php

namespace App\Jobs;

use App\Models\Imdbrating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckImdbUrls implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // 1 uur max

    public function handle(): void
    {
/*         $films = Imdbrating::whereNotNull('imdburl')
            ->where('imdburl', '!=', '')
            ->whereNull('url_geldig')  // ← enkel null, expliciet
            ->orderBy('titel')
            ->get(); */
            $films = Imdbrating::urlNakijken()->orderBy('titel')->get();

        Log::info('Films te controleren: ' . $films->count());

        foreach ($films as $film) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($film->imdburl);

                $geldig = $response->successful();

                $film->url_geldig = $geldig;
                $film->saveQuietly(); // ← saveQuietly om de booted() update event te omzeilen!
                Log::info("IMDB check: {$film->titel} → " . ($geldig ? 'OK' : 'FOUT'));
            } catch (\Exception $e) {
                $film->url_geldig = false;
                $film->saveQuietly();
                Log::warning("IMDB check mislukt: {$film->titel} → {$e->getMessage()}");
            }

            usleep(500000);
        }
    }
}
