<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilitySolarPanelReading extends Model
{
    protected $fillable = [
        'date',
        'counter_reading',
    ];
}
