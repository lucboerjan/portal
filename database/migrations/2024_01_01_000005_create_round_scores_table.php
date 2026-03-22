<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('round_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_player_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();

            $table->unique(['round_id', 'game_player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('round_scores');
    }
};
