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
        Schema::create('aandelen_fondsen', function (Blueprint $table) {
            $table->id();
            $table->string('isin')->unique();
            $table->string('naam')->unique();
            $table->string('url');
            $table->enum('fondsType', ['Aandeel', 'Fonds'])->default('Fonds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aandelen_fondsen');
    }
};