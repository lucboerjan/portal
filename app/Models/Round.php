<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    protected $fillable = [
        'game_id',
        'round_number',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(RoundScore::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function getScoreForPlayer(GamePlayer $player): int
    {
        return $this->scores()->where('game_player_id', $player->id)->value('score') ?? 0;
    }
}
