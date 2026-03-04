<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentRate extends Model
{
    protected $table = 'aandelen_koersen';

    protected $fillable = [
        'fondsID',
        'datum',
        'dagkoers',
    ];

    protected $casts = [
        'datum' => 'date',
        'dagkoers' => 'decimal:2',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(InvestmentFund::class, 'fondsID');
    }

    public function getWaardeAttribute()
    {
        $totaalAantal = $this->fund->aandelenAankopen()->sum('aantal');
        return round($this->dagkoers * $totaalAantal, 2);
    }
}