<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vertoning extends Model
{
    protected $table = 'vertoningen';

    protected $fillable = ['tvzenderID', 'imdbratingID', 'datum'];

    public function tvzender()
    {
        return $this->belongsTo(Tvzender::class, 'tvzenderID');
    }

    public function imdbrating()
    {
        return $this->belongsTo(ImdbRating::class, 'imdbratingID');
    }
}