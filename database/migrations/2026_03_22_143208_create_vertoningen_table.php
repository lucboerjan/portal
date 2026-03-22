<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vertoningen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvzender_id')->constrained('tvzender');
            $table->foreignId('imdbrating_id')->constrained('imdbrating');
            $table->string('datum', 10)->default('0000-00-00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vertoningen');
    }
};
