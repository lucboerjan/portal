<?php

namespace App\Models;

use App\Enums\CategorieRichting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinCategorie extends Model
{
    protected $table = 'fin_categorie';

    protected $fillable = [
        'parent_id', 'omschrijving', 'richting', 'exclude', 'actief',
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

    public function transacties(): HasMany
    {
        return $this->hasMany(FinTransactieCategorie::class, 'categorie_id');
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
}