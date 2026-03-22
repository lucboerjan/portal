<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('name');                          // naam als string (geen vaste users)
            $table->integer('position')->nullable();         // 0 = niet rank
            $table->timestamps();

            $table->unique(['game_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_players');
    }
};
