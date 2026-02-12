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
        'reason',
    ];

    protected $casts = [
        'correction_date' => 'date',
        'old_meter_final_reading' => 'decimal:2',
        'new_meter_start_reading' => 'decimal:2',
    ];

    public function utilityType()
    {
        return $this->belongsTo(UtilityType::class);
    }
}