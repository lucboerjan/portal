<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\GameType;
use App\Models\Round;
use App\Models\RoundScore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGameScores extends Command
{
    protected $signature = 'gamescores:import
                            {--database=dashboard : Naam van de brondatabase}
                            {--fresh : Verwijder eerst alle bestaande data}
                            {--dry-run : Simuleer import zonder data op te slaan}';

    protected $description = 'Importeer spelletjesdata vanuit de oude dashboard database';

    private bool $dryRun = false;
    private int  $gamesImported   = 0;
    private int  $roundsImported  = 0;
    private int  $scoresImported  = 0;
    private int  $skipped         = 0;

    public function handle(): int
    {
        $this->dryRun = $this->option('dry-run');
        $sourceDb     = $this->option('database');

        $this->info('');
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║   GameScores Import                  ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->info('');

        if ($this->dryRun) {
            $this->warn('▶ DRY-RUN modus — geen data wordt opgeslagen');
            $this->info('');
        }

        // ── Controleer verbinding ───────────────────────────────────
        try {
            DB::connection('import')->getPdo();
            $this->info("✓ Verbonden met database: {$sourceDb}");
        } catch (\Exception $e) {
            $this->error("✗ Kan niet verbinden met '{$sourceDb}': " . $e->getMessage());
            $this->info('');
            $this->info('Voeg dit toe aan config/database.php connections:');
            $this->line("  'import' => [");
            $this->line("      'driver'   => 'mysql',");
            $this->line("      'host'     => env('DB_HOST', '127.0.0.1'),");
            $this->line("      'port'     => env('DB_PORT', '3306'),");
            $this->line("      'database' => '{$sourceDb}',");
            $this->line("      'username' => env('DB_USERNAME'),");
            $this->line("      'password' => env('DB_PASSWORD'),");
            $this->line("      'charset'  => 'utf8mb4',");
            $this->line("  ],");
            return self::FAILURE;
        }

        // ── Fresh optie ─────────────────────────────────────────────
        if ($this->option('fresh')) {
            if (!$this->confirm('⚠ Dit verwijdert ALLE bestaande GameScores data. Doorgaan?')) {
                $this->info('Geannuleerd.');
                return self::SUCCESS;
            }
            if (!$this->dryRun) {
                RoundScore::query()->delete();
                Round::query()->delete();
                GamePlayer::query()->delete();
                Game::query()->delete();
            
            }
            $this->warn('✓ Bestaande data verwijderd');
        }

        // ── Zorg dat Uno game type bestaat ──────────────────────────
        $gameType = $this->dryRun
            ? (object) ['id' => 1, 'name' => 'Uno']
            : GameType::firstOrCreate(
                ['slug' => 'uno'],
                [
                    'name'              => 'Uno',
                    'lowest_score_wins' => true,
                    'min_players'       => 2,
                    'max_players'       => 10,
                    'active'            => true,
                ]
            );

        $this->info("✓ Speltype: {$gameType->name}");
        $this->info('');

        // ── Laad brondata ───────────────────────────────────────────
        $spelmain   = DB::connection('import')->table('spelmain')->orderBy('spelID')->get();
        $spelers    = DB::connection('import')->table('spelers')->get()->keyBy('spelerID');
        $spelrondes = DB::connection('import')->table('spelrondes')
            ->orderBy('spelID')
            ->orderBy('spelrondeSpelID')
            ->orderBy('spelerID')
            ->get();

        $this->info("Gevonden in '{$sourceDb}':");
        $this->line("  • {$spelmain->count()} spellen (spelmain)");
        $this->line("  • {$spelers->count()} spelers (spelers)");
        $this->line("  • {$spelrondes->count()} spelronde-scores (spelrondes)");
        $this->info('');

        // Groepeer alle spelrondes per spelID
        $rondesPerSpel = $spelrondes->groupBy('spelID');

        // ── Importeer spel per spel ─────────────────────────────────
        $progressBar = $this->output->createProgressBar($spelmain->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $progressBar->setMessage('Starten...');
        $progressBar->start();

        foreach ($spelmain as $oud) {
            $progressBar->setMessage("Spel #{$oud->spelID} — {$oud->datum}");

            $datum = $this->parseDatum($oud->datum);

            if (!$datum) {
                $this->newLine();
                $this->warn("  ⚠ Ongeldige datum voor spelID {$oud->spelID}: '{$oud->datum}' — overgeslagen");
                $this->skipped++;
                $progressBar->advance();
                continue;
            }

            $spelRondesVoorSpel = $rondesPerSpel->get($oud->spelID, collect());
            $spelerIdsInSpel    = $spelRondesVoorSpel->pluck('spelerID')->unique();

            DB::transaction(function () use (
                $oud,
                $datum,
                $gameType,
                $spelRondesVoorSpel,
                $spelerIdsInSpel,
                $spelers
            ) {
                // 1. Game aanmaken
                if (!$this->dryRun) {
                    $game = Game::create([
                        'game_type_id' => $gameType->id,
                        'played_at'    => $datum,
                        'description'  => $oud->omschrijving ?: null,
                        'status'       => 'finished',
                    ]);
                } else {
                    $game = (object) ['id' => $oud->spelID];
                }

                // 2. GamePlayers aanmaken — naam ophalen uit spelers tabel
                $playerMap = [];
                foreach ($spelerIdsInSpel as $spelerID) {
                    $naam = $spelers->get($spelerID)?->naam ?? "Speler {$spelerID}";
                    if (!$this->dryRun) {
                        $gamePlayer           = GamePlayer::create(['game_id' => $game->id, 'name' => $naam]);
                        $playerMap[$spelerID] = $gamePlayer->id;
                    } else {
                        $playerMap[$spelerID] = $spelerID;
                    }
                }
                // 3. Rondes aanmaken
                $rondesGegroepeerd = $spelRondesVoorSpel->groupBy('spelrondeSpelID');
                $rondeNummer = 0;

                foreach ($rondesGegroepeerd as $spelrondeSpelID => $scores) {
                    $rondeNummer++;

                    if (!$this->dryRun) {
                        $round = Round::create([
                            'game_id'      => $game->id,
                            'round_number' => $rondeNummer,
                        ]);
                    } else {
                        $round = (object) ['id' => $spelrondeSpelID];
                    }

                    foreach ($scores as $scoreRij) {
                        $gamePlayerId = $playerMap[$scoreRij->spelerID] ?? null;
                        if (!$gamePlayerId) continue;

                        if (!$this->dryRun) {
                            RoundScore::updateOrCreate(
                                ['round_id' => $round->id, 'game_player_id' => $gamePlayerId],
                                ['score'    => (int) $scoreRij->score]
                            );
                        }

                        $this->scoresImported++;
                    }

                    $this->roundsImported++;
                }
            });

            $progressBar->advance();
        }

        $progressBar->setMessage('Klaar!');
        $progressBar->finish();

        // ── Samenvatting ────────────────────────────────────────────
        $this->newLine(2);
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║   Import voltooid                    ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->table(
            ['', 'Aantal'],
            [
                ['✓ Spellen geïmporteerd',  $this->gamesImported],
                ['✓ Rondes geïmporteerd',   $this->roundsImported],
                ['✓ Scores geïmporteerd',   $this->scoresImported],
                ['⚠ Overgeslagen',          $this->skipped],
            ]
        );

        if ($this->dryRun) {
            $this->warn('▶ DRY-RUN — geen data opgeslagen. Verwijder --dry-run om echt te importeren.');
        }

        $this->info('');
        return self::SUCCESS;
    }

    private function parseDatum(string $datum): ?string
    {
        $datum = trim($datum);

        // DD-MM-YYYY
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $datum, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        // YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum)) {
            return $datum;
        }

        return null;
    }
}
