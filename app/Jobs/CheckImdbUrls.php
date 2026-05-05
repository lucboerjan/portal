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
        $films = Imdbrating::whereNotNull('imdburl')
            ->where('imdburl', '!=', '')
            ->orderBy('titel')
            ->get();

        foreach ($films as $film) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($film->imdburl);

                $geldig = $response->successful(); // 200-299

                Imdbrating::where('id', $film->id)->update(['url_geldig' => $geldig]);

                Log::info("IMDB check: {$film->titel} → " . ($geldig ? 'OK' : 'FOUT'));

            } catch (\Exception $e) {
                Imdbrating::where('id', $film->id)->update(['url_geldig' => false]);
                Log::warning("IMDB check mislukt: {$film->titel} → {$e->getMessage()}");
            }

            // Kleine pauze om IMDB niet te overbelasten
            usleep(250000); // 0.25 seconde
        }
    }
}