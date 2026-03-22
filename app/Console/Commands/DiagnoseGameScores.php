<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseGameScores extends Command
{
    protected $signature   = 'gamescores:diagnose {--database=dashboard}';
    protected $description = 'Analyseer de oude dashboard database op problemen';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║   GameScores Diagnose                ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->info('');

        // ── Duplicaten in spelrondes ────────────────────────────────
        $this->info('① Dubbele scores (zelfde spelID + Volgorde + spelerID):');

        $dupes = DB::connection('import')
            ->table('spelrondes')
            ->select('spelID', 'Volgorde', 'spelerID', DB::raw('COUNT(*) as aantal'), DB::raw('GROUP_CONCAT(score) as scores'))
            ->groupBy('spelID', 'Volgorde', 'spelerID')
            ->having('aantal', '>', 1)
            ->orderBy('spelID')
            ->get();

        if ($dupes->isEmpty()) {
            $this->line('   ✓ Geen duplicaten gevonden');
        } else {
            $this->warn("   ⚠ {$dupes->count()} dubbele combinaties gevonden:");
            $this->table(
                ['spelID', 'Volgorde (ronde)', 'spelerID', 'Aantal', 'Scores'],
                $dupes->map(fn ($r) => [$r->spelID, $r->Volgorde, $r->spelerID, $r->aantal, $r->scores])
            );
        }

        // ── NULL waarden ────────────────────────────────────────────
        $this->info('');
        $this->info('② Rijen met NULL waarden in spelrondes:');

        $nulls = DB::connection('import')
            ->table('spelrondes')
            ->whereNull('spelID')
            ->orWhereNull('spelerID')
            ->orWhereNull('Volgorde')
            ->orWhereNull('score')
            ->count();

        $this->line($nulls > 0
            ? "   ⚠ {$nulls} rijen met NULL waarden"
            : '   ✓ Geen NULL waarden'
        );

        // ── Spelers die niet in spelers tabel zitten ────────────────
        $this->info('');
        $this->info('③ SpelerIDs in spelrondes die niet in spelers tabel staan:');

        $onbekend = DB::connection('import')
            ->table('spelrondes')
            ->leftJoin('spelers', 'spelrondes.spelerID', '=', 'spelers.spelerID')
            ->whereNull('spelers.spelerID')
            ->select('spelrondes.spelerID')
            ->distinct()
            ->get();

        $this->line($onbekend->isEmpty()
            ? '   ✓ Alle spelerIDs gevonden in spelers tabel'
            : "   ⚠ Onbekende spelerIDs: " . $onbekend->pluck('spelerID')->join(', ')
        );

        // ── Overzicht per spel ──────────────────────────────────────
        $this->info('');
        $this->info('④ Overzicht per spel:');

        $overzicht = DB::connection('import')
            ->table('spelmain')
            ->leftJoin('spelrondes', 'spelmain.spelID', '=', 'spelrondes.spelID')
            ->select(
                'spelmain.spelID',
                'spelmain.datum',
                DB::raw('COUNT(spelrondes.spelrondeID) as totaal_scores'),
                DB::raw('COUNT(DISTINCT spelrondes.Volgorde) as rondes'),
                DB::raw('COUNT(DISTINCT spelrondes.spelerID) as spelers')
            )
            ->groupBy('spelmain.spelID', 'spelmain.datum')
            ->orderBy('spelmain.spelID')
            ->get();

        $this->table(
            ['spelID', 'Datum', 'Totaal scores', 'Rondes', 'Spelers'],
            $overzicht->map(fn ($r) => [$r->spelID, $r->datum, $r->totaal_scores, $r->rondes, $r->spelers])
        );

        $this->info('');
        $this->info('Voer na analyse de import uit met:');
        $this->line('  php artisan gamescores:import          (duplicaten worden gemiddeld)');
        $this->line('  php artisan gamescores:import --fresh  (eerst cleanen, dan importeren)');
        $this->info('');

        return self::SUCCESS;
    }
}