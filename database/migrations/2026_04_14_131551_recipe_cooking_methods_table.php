<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')
                ->constrained('recipe_categories')
                ->restrictOnDelete();
            $table->foreignId('cooking_method_id')
                ->nullable()
                ->constrained('recipe_cooking_methods')
                ->nullOnDelete();
            $table->text('description')->nullable();
            $table->json('instructions');           // array van stappen
            $table->unsignedSmallInteger('prep_time')->nullable();
            $table->unsignedSmallInteger('cook_time')->nullable();
            $table->unsignedTinyInteger('servings')->default(4);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('source_type')->nullable();  // url | scan | boek
            $table->string('source_value', 500)->nullable();
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
