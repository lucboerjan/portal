<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vertoning extends Model
{
    protected $table = 'vertoningen';

    protected $fillable = ['tvzender_id', 'imdbrating_id', 'datum'];

    public function tvzender()
    {
        return $this->belongsTo(Tvzender::class, 'tvzender_id');
    }

    public function imdbrating()
    {
        return $this->belongsTo(ImdbRating::class, 'imdbrating_id');
    }
}