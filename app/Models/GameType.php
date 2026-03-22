<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'lowest_score_wins',
        'min_players',
        'max_players',
        'description',
        'active',
    ];

    protected $casts = [
        'lowest_score_wins' => 'boolean',
        'active' => 'boolean',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
