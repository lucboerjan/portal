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
        Schema::create('aandelen_aankopen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fondsID')->constrained('aandelen_fondsen')->onDelete('cascade');
            $table->date('datum')->nullable();
            $table->decimal('aantal', 10, 3)->default(0.000);
            $table->decimal('aankoopprijs', 10, 3)->default(0.000);
            $table->timestamps();

            // Index for faster queries
            $table->index('fondsID');
            $table->index('datum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aandelen_aankopen');
    }
};