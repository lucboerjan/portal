<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FinTransactie extends Model
{
    protected $table = 'fin_transactie';

    protected $fillable = [
        'rekening_id', 'begunstigde_id', 'datum',
        'omschrijving', 'bedrag', 'verwerkt',
    ];

    protected $casts = [
        'datum'    => 'date',
        'bedrag'   => 'decimal:2',
        'verwerkt' => 'boolean',
    ];

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(FinRekening::class, 'rekening_id');
    }

    public function begunstigde(): BelongsTo
    {
        return $this->belongsTo(FinBegunstigde::class, 'begunstigde_id');
    }

    // Via pivot model (met bedrag per categorie)
    public function categorieKoppelingen(): HasMany
    {
        return $this->hasMany(FinTransactieCategorie::class, 'transactie_id');
    }

    // Direct many-to-many (zonder bedrag)
    public function categorieen(): BelongsToMany
    {
        return $this->belongsToMany(
            FinCategorie::class,
            'fin_transactie_categorie',
            'transactie_id',
            'categorie_id'
        )->withPivot('bedrag', 'opmerking')->withTimestamps();
    }

    public function isInkomst(): bool
    {
        return $this->bedrag > 0;
    }

    public function isUitgave(): bool
    {
        return $this->bedrag < 0;
    }
}