<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vertoning;

class Imdbrating extends Model
{
    protected $table = 'imdbrating';

    protected $fillable = ['titel', 'jaar', 'imdburl', 'imdbrating'];

    public function vertoningen()
    {
        return $this->hasMany(vertoning::class, 'imdbrating_id')->withCount('imdbrating');
    }
}