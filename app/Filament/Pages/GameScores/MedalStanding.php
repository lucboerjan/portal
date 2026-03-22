<?php

namespace App\Filament\Pages\GameScores;

use App\Models\Game;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
class MedalStanding extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars2;
    protected static ?string $navigationLabel = 'Medaillestand';
    protected static ?string $title           = 'Medaillestand';
    
    protected static ?int    $navigationSort  = 3;


        public static function getNavigationGroup(): ?string
    {
        return 'Game Scores';
    }
    // Verwijst naar: resources/views/filament/pages/game-scores/medal-standing.blade.php
    protected string $view = 'filament.pages.game-scores.medal-standing';

    public function getStandings(): Collection
    {
        $finishedGames = Game::where('status', 'finished')
            ->with(['players.roundScores', 'gameType'])
            ->get();

        $standings = collect();

        foreach ($finishedGames as $game) {
            $ranking = $game->getRanking();

            foreach ($ranking as $player) {
                $name = $player->name;

                if (!$standings->has($name)) {
                    $standings->put($name, [
                        'name'           => $name,
                        'gold'           => 0,
                        'silver'         => 0,
                        'bronze'         => 0,
                        'participations' => 0,
                    ]);
                }

                $entry = $standings->get($name);
                $entry['participations']++;

                match ($player->rank) {
                    1 => $entry['gold']++,
                    2 => $entry['silver']++,
                    3 => $entry['bronze']++,
                    default => null,
                };

                $standings->put($name, $entry);
            }
        }

        // Goud desc → zilver desc → brons desc
        return $standings
            ->sortByDesc(fn ($s) => [$s['gold'], $s['silver'], $s['bronze']])
            ->values();
    }

    public function getGameOverview(): Collection
    {
        return Game::where('status', 'finished')
            ->with(['players', 'gameType'])
            ->orderByDesc('played_at')
            ->get()
            ->map(function (Game $game) {
                return [
                    'date'    => $game->played_at->format('d-m-Y'),
                    'type'    => $game->gameType->name,
                    'players' => $game->getRanking(),
                ];
            });
    }
}
