<?php

namespace App\Filament\Resources\GameScores\GameResource\Pages;

use App\Filament\Resources\GameScores\GameResource;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Round;
use App\Models\RoundScore;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Support\Icons\Heroicon;    

class GameScoreboard extends Page
{
    protected static string $resource = GameResource::class;
    protected string $view     = 'filament.resources.game-scores.game-resource.pages.game-scoreboard';

    public Game $record;

    // Scores array voor de modal form
    public array $roundScores    = [];
    public ?int  $editingRoundId = null;

    public function mount(Game $record): void
    {
        $this->record = $record->load([
            'gameType',
            'players',
            'rounds.scores.gamePlayer',
        ]);

        $this->resetRoundScores();
    }

    // ─── Computed ──────────────────────────────────────────────────

    public function getRanking(): Collection
    {
        return $this->record->getRanking();
    }

    public function getRounds(): Collection
    {
        return $this->record->rounds->sortByDesc('round_number')->values();
    }

    public function getPlayers(): Collection
    {
        return $this->record->players;
    }

    // ─── Header actions ────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            // "Spelronde toevoegen" als echte Filament Action met modal
            Action::make('addRound')
                ->label('Spelronde toevoegen')
                ->icon('heroicon::plus-circle')
                ->color('primary')
                ->visible(fn() => $this->record->status === 'active')
                ->schema($this->getRoundFormSchema())
                ->action(function (array $data): void {
                    $this->editingRoundId = null;
                    $this->roundScores = $data['scores'];
                    $this->saveRound();
                }),

            Action::make('edit')
                ->label('Spel bewerken')
                ->icon('heroicon::pencil')
                ->url(GameResource::getUrl('edit', ['record' => $this->record])),

            Action::make('finish')
                ->label('Spel afsluiten')
                ->icon('heroicon::flag')
                ->color('success')
                ->requiresConfirmation()
                ->action('finishGame')
                ->visible(fn() => $this->record->status === 'active'
                    && $this->record->rounds()->count() > 0),

            Action::make('reopen')
                ->label('Heropenen')
                ->icon('heroicon::arrow-path')
                ->color('warning')
                ->action('reopenGame')
                ->visible(fn() => $this->record->status === 'finished'),
        ];
    }

    // ─── Edit ronde action (per rij) ───────────────────────────────

    public function editRoundAction(): Action
    {
        return Action::make('editRound')
            ->iconButton()
            ->icon('heroicon::pencil-square')
            ->visible(fn() => $this->record->status === 'active')
            ->color('primary')
            ->schema(fn(array $arguments) => $this->getRoundFormSchema($arguments['roundId'] ?? null))
            ->fillForm(function (array $arguments): array {
                //dd($arguments);
                return $this->fillRoundForm($arguments['roundId'] ?? null);
            })
            ->action(function (array $arguments, array $data): void {

                $this->editingRoundId = $arguments['roundId'] ?? null;
                $this->roundScores = $data['scores'];
                $this->saveRound();
            });
    }

    public function deleteRoundAction(): Action
    {
        return Action::make('deleteRound')
            ->iconButton()
            ->icon('heroicon::scissors')
            ->visible(fn() => $this->record->status === 'active')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                Round::find($arguments['roundId'])?->delete();
                $this->refreshRecord();
                Notification::make()->title('Spelronde verwijderd')->success()->send();
            });
    }

    // ─── Form schema voor de modal ─────────────────────────────────

    private function getRoundFormSchema(?int $roundId = null): array
    {
        return $this->record->players->map(function ($player) {
            return TextInput::make("scores.{$player->id}")
                ->label($player->name)
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required();
        })->toArray();
    }

    private function fillRoundForm(?int $roundId): array
    {
        $scores = [];

        if ($roundId) {
            $round = Round::with('scores')->find($roundId);
            foreach ($this->record->players as $player) {
                $score = $round->scores->firstWhere('game_player_id', $player->id);
                $scores[$player->id] = $score ? $score->score : 0;
            }
        } else {
            foreach ($this->record->players as $player) {
                $scores[$player->id] = 0;
            }
        }

        return ['scores' => $scores];
    }

    // ─── Opslaan ──────────────────────────────────────────────────

    private function saveRound(): void
    {
        if ($this->editingRoundId) {
            $round = Round::find($this->editingRoundId);
            foreach ($this->roundScores as $playerId => $score) {
                RoundScore::updateOrCreate(
                    ['round_id' => $round->id, 'game_player_id' => $playerId],
                    ['score'    => (int) $score]
                );
            }
            $message = 'Spelronde bijgewerkt!';
        } else {
            $nextNumber = $this->record->rounds()->max('round_number') + 1;
            $round = Round::create([
                'game_id'      => $this->record->id,
                'round_number' => $nextNumber,
            ]);
            foreach ($this->roundScores as $playerId => $score) {
                RoundScore::create([
                    'round_id'       => $round->id,
                    'game_player_id' => $playerId,
                    'score'          => (int) $score,
                ]);
            }
            $message = 'Spelronde toegevoegd!';
        }

        $this->refreshRecord();
        Notification::make()->title($message)->success()->send();
    }

    // ─── Spel afsluiten ───────────────────────────────────────────

    public function finishGame(): void
    {
        foreach ($this->record->getRanking() as $player) {
            GamePlayer::where("id", $player->id)->update(["position" => $player->rank]);
        }
        $this->record->update(['status' => 'finished']);
        $this->refreshRecord();
        Notification::make()->title('Spel afgesloten!')->success()->send();
    }

    public function reopenGame(): void
    {
        $this->record->update(['status' => 'active']);
        $this->refreshRecord();
        Notification::make()->title('Spel heropend')->success()->send();
    }

    // ─── Helpers ──────────────────────────────────────────────────

    private function refreshRecord(): void
    {
        $this->record = $this->record->fresh([
            'gameType',
            'players',
            'rounds.scores.gamePlayer',
        ]);
    }

    private function resetRoundScores(): void
    {
        $this->roundScores = [];
        foreach ($this->record->players as $player) {
            $this->roundScores[$player->id] = 0;
        }
    }
}
