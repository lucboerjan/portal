<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinBegunstigde extends Model
{
    protected $table = 'fin_begunstigde';

    protected $fillable = ['naam', 'rekeningnummer'];

    public function transacties(): HasMany
    {
        return $this->hasMany(FinTransactie::class, 'begunstigde_id');
    }
}