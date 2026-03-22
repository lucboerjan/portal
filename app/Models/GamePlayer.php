<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class GamePlayer extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'position',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function roundScores(): HasMany
    {
        return $this->hasMany(RoundScore::class);
    }

    // ─── Computed ──────────────────────────────────────────────────

    public function getTotalScore(): int
    {
        return $this->roundScores()->sum('score');
    }

    /**
     * Score voor een specifieke ronde
     */
    public function getScoreForRound(Round $round): ?RoundScore
    {
        return $this->roundScores()->where('round_id', $round->id)->first();
    }
}
