<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinTransactieCategorie extends Model
{
    protected $table = 'fin_transactie_categorie';

    protected $fillable = [
        'transactie_id', 'categorie_id', 'bedrag', 'opmerking',
    ];

    protected $casts = [
        'bedrag' => 'decimal:2',
    ];

    public function transactie(): BelongsTo
    {
        return $this->belongsTo(FinTransactie::class, 'transactie_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(FinCategorie::class, 'categorie_id');
    }
}