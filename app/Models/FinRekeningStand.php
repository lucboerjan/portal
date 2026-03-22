<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinRekeningStand extends Model
{
    protected $table = 'fin_rekening_stand';

    protected $fillable = [
        'rekening_id', 'jaar', 'maand', 'saldo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(FinRekening::class, 'rekening_id');
    }
}