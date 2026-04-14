<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recepten <-> Ingrediënten (met extra pivot-kolommen)
        Schema::create('recipe_ingredient_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')
                ->constrained('recipes')
                ->cascadeOnDelete();
            $table->foreignId('ingredient_id')
                ->constrained('recipe_ingredients')
                ->restrictOnDelete();
            $table->decimal('quantity', 8, 2)->nullable();
            $table->string('unit')->nullable();     // g, ml, el, tl, stuk, …
            $table->string('notes')->nullable();    // "fijngesneden", "op kamertemp."
            $table->unsignedSmallInteger('sort_order')->default(0);
        });
 
        // Recepten <-> Tags
        Schema::create('recipe_tag', function (Blueprint $table) {
            $table->foreignId('recipe_id')
                ->constrained('recipes')
                ->cascadeOnDelete();
            $table->foreignId('tag_id')
                ->constrained('recipe_tags')
                ->cascadeOnDelete();
            $table->primary(['recipe_id', 'tag_id']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('recipe_tag');
        Schema::dropIfExists('recipe_ingredient_pivot');
    }
};
