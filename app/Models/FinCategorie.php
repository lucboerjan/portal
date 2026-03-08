<?php

namespace App\Models;

use App\Enums\CategorieRichting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FinCategorie extends Model
{
    protected $table = 'fin_categorie';

    protected $fillable = [
        'parent_id',
        'omschrijving',
        'richting',
        'exclude',
        'actief',
    ];

    protected $casts = [
        'richting' => CategorieRichting::class,
        'exclude'  => 'boolean',
        'actief'   => 'boolean',
    ];

    // Hoofdcategorie
    public function parent(): BelongsTo
    {
        return $this->belongsTo(FinCategorie::class, 'parent_id');
    }

    // Subcategorieën
    public function children(): HasMany
    {
        return $this->hasMany(FinCategorie::class, 'parent_id');
    }

    public function isHoofdcategorie(): bool
    {
        return is_null($this->parent_id);
    }

    // In FinCategorie.php
    public function transacties(): BelongsToMany
    {
        return $this->belongsToMany(
            FinTransactie::class,
            'fin_transactie_categorie',
            'categorie_id',
            'transactie_id'
        )->withPivot('bedrag', 'opmerking')->withTimestamps();
    }

    // Handige scope: alleen hoofdcategorieën
    public function scopeHoofd($query)
    {
        return $query->whereNull('parent_id');
    }

    // Handige scope: alleen subcategorieën
    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeGeordend($query)
    {
        return $query
            ->orderByRaw('COALESCE(fin_categorie.parent_id, fin_categorie.id)')
            ->orderBy('fin_categorie.parent_id')
            ->orderBy('fin_categorie.omschrijving');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query
                ->orderByRaw('COALESCE(fin_categorie.parent_id, fin_categorie.id)')
                ->orderBy('parent_id')
                ->orderBy('omschrijving');
        });
    }
}
