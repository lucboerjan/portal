<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinTransactie extends Model
{
    protected $table = 'fin_transactie';

    protected $fillable = [
        'rekening_id',
        'begunstigde_id',
        'datum',
        'volgnummer',
        'omschrijving',
        'bedrag',
        'verwerkt',
    ];

    protected $casts = [
        'datum'     => 'date',
        'bedrag'    => 'decimal:2',
        'verwerkt'  => 'boolean',
    ];

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(FinRekening::class, 'rekening_id');
    }

    public function begunstigde(): BelongsTo
    {
        return $this->belongsTo(FinBegunstigde::class, 'begunstigde_id');
    }

    public function categorieKoppelingen(): HasMany
    {
        return $this->hasMany(FinTransactieCategorie::class, 'transactie_id');
    }

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

    public function getCategorieIdAttribute(): ?int
    {
        return $this->categorieKoppelingen()->first()?->categorie_id;
    }
}
