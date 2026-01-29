<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentPurchase extends Model
{
    protected $table = 'aandelen_aankopen';

    protected $fillable = [
        'fondsID',
        'datum',
        'aantal',
        'aankoopprijs',
    ];

    protected $casts = [
        'datum' => 'date',
        'aantal' => 'decimal:3',
        'aankoopprijs' => 'decimal:2',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(InvestmentFund::class, 'fondsID');
    }

    public function getPurchaseAmountAttribute(): float
    {
        return $this->aantal * $this->aankoopprijs;
    }
}