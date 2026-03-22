<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Game extends Model
{
    protected $fillable = [
        'game_type_id',
        'played_at',
        'description',
        'status',
    ];

    protected $casts = [
        'played_at' => 'date',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function gameType(): BelongsTo
    {
        return $this->belongsTo(GameType::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class)->orderBy('round_number');
    }

    public function roundScores(): HasManyThrough
    {
        return $this->hasManyThrough(RoundScore::class, Round::class);
    }

    // ─── Computed ──────────────────────────────────────────────────

    /**
     * Geeft de totaalscore per speler terug, gesorteerd (laagste eerst als lowest_score_wins).
     * Returns: Collection van GamePlayer met ->total_score en ->rank
     */
    public function getRanking()
    {
        $lowestWins = $this->gameType->lowest_score_wins;

        $players = $this->players()->with(['roundScores'])->get();

        $players = $players->map(function (GamePlayer $player) {
            $player->total_score = $player->roundScores->sum('score');
            return $player;
        });

        $sorted = $lowestWins
            ? $players->sortBy('total_score')
            : $players->sortByDesc('total_score');

        return $sorted->values()->map(function (GamePlayer $player, int $index) {
            $player->rank = $index + 1;
            return $player;
        });
    }

    public function getPlayerCount(): int
    {
        return $this->players()->count();
    }

    public function getRoundCount(): int
    {
        return $this->rounds()->count();
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }
}
