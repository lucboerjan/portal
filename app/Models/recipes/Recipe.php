<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'title', 'slug', 'category_id', 'cooking_method_id',
        'description', 'instructions', 'prep_time', 'cook_time',
        'servings', 'difficulty', 'source_type', 'source_value',
        'image', 'notes',
    ];

    protected $casts = [
        'instructions' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->title));
        static::updating(fn ($m) => $m->slug = Str::slug($m->title));
    }

    // ── Computed ─────────────────────────────────────────────────────────────

    public function getTotalTimeAttribute(): ?int
    {
        if ($this->prep_time === null && $this->cook_time === null) {
            return null;
        }
        return ($this->prep_time ?? 0) + ($this->cook_time ?? 0);
    }

    // ── Relaties ─────────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cookingMethod(): BelongsTo
    {
        return $this->belongsTo(CookingMethod::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient_pivot')
            ->withPivot(['quantity', 'unit', 'notes', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'recipe_tag');
    }
}