<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stap 1: kolom toevoegen zonder constraint
        Schema::table('aandelen_fondsen', function (Blueprint $table) {
            $table->unsignedBigInteger('rekening_id')->nullable();
        });

        // Stap 2: bestaande rijen updaten
        DB::table('aandelen_fondsen')->update(['rekening_id' => 1]); // jouw standaard rekening id

        // Stap 3: constraint toevoegen
        Schema::table('aandelen_fondsen', function (Blueprint $table) {
            $table->foreign('rekening_id')->references('id')->on('fin_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
