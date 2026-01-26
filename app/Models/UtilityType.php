<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityType extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'type',
    ];

    public function utilityReadings()
    {
        return $this->hasMany(UtilityReading::class);
    }   

    public function utilityCorrections()
    {
        return $this->hasMany(UtilityCorrection::class);
    }
}