<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CookingMethod extends Model
{
    protected $table = 'recipe_cooking_methods';

    protected $fillable = ['name', 'slug', 'icon'];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->name));
        static::updating(fn ($m) => $m->slug = Str::slug($m->name));
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}