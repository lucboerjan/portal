<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityReading extends Model
{
    Protected $fillable = [
        'utility_type_id',
        'reading_date',
        'meter_stand',
        'note',
    ];
    public function utilityType()
    {
        return $this->belongsTo(UtilityType::class);
    }

}
