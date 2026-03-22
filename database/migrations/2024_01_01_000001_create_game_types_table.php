<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Uno, Kaarten, ...
            $table->string('slug')->unique();                // uno, kaarten, ...
            $table->boolean('lowest_score_wins')->default(true);
            $table->integer('min_players')->default(2);
            $table->integer('max_players')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_types');
    }
};
