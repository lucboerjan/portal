<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoundScore extends Model
{
    protected $fillable = [
        'round_id',
        'game_player_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    public function gamePlayer(): BelongsTo
    {
        return $this->belongsTo(GamePlayer::class);
    }
}
