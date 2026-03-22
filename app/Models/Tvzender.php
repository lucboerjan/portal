<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tvzender extends Model
{
    protected $table = 'tvzender';

    protected $fillable = ['naam', 'volgnummer'];

    public function vertoningen()
    {
        return $this->hasMany(vertoning::class, 'tvzenderID');
    }
}