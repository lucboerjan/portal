<?php

namespace App\Models;

use App\Enums\RekeningType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinRekening extends Model
{
    protected $table = 'fin_rekening';
    protected $appends = ['berekend_saldo', 'verschil_saldo'];

    protected $fillable = [
        'referentie', 'omschrijving', 'saldo',
        'order', 'rekening_type', 'actief',
    ];

    protected $casts = [
        'saldo'         => 'decimal:2',
        'rekening_type' => RekeningType::class,
        'actief'        => 'boolean',
    ];

    public function transacties(): HasMany
    {
        return $this->hasMany(FinTransactie::class, 'rekening_id');
        
    }

        public function getBerekendSaldoAttribute(): float
    {
        return (float) $this->transacties()->sum('bedrag');
    }

    public function getVerschilSaldoAttribute(): float
    {
        return (float) ($this->saldo - $this->berekend_saldo);
    }
}