<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $table = 'recipe_tags';

    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->name));
        static::updating(fn ($m) => $m->slug = Str::slug($m->name));
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_tag');
    }
}