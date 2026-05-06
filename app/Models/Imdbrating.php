<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vertoning;
use Illuminate\Database\Eloquent\Builder;

class Imdbrating extends Model
{
    protected $table = 'imdbrating';

    protected $fillable = ['titel', 'jaar', 'imdburl', 'imdbrating'];

    public function vertoningen()
    {
        return $this->hasMany(vertoning::class, 'imdbrating_id')->withCount('imdbrating');
    }

    protected static function booted(): void
    {
        static::updating(function (Imdbrating $imdbrating) {
            if ($imdbrating->isDirty('imdburl')) {
                $imdbrating->url_geldig = null;
            }
        });
    }
    public function scopeUrlNakijken(Builder $query): Builder
    {
        return $query
            ->whereNotNull('imdburl')
            ->where('imdburl', '!=', '')
            ->whereNull('url_geldig');
    }

    public static function heeftUrlsNakijken(): bool
    {
        return static::urlNakijken()->exists();
    }
}
