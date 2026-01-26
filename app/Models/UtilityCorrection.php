<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityCorrection extends Model
{
    protected $fillable = [
        'utility_type_id',
        'correction_date',
        'old_meter_final_reading',
        'new_meter_start_reading',
        'note',
    ];

    public function utilityType()
    {
        return $this->belongsTo(UtilityType::class);
    }
}
