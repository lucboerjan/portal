<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_transactie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekening_id')->constrained('fin_rekening');
            $table->foreignId('begunstigde_id')->nullable()->constrained('fin_begunstigde')->nullOnDelete();
            $table->date('datum');
            $table->smallInteger('volgnummer')->default(0); // volgorde op uittreksel


            $table->string('omschrijving')->nullable();
            $table->decimal('bedrag', 15, 2); // positief = inkomst, negatief = uitgave
            $table->boolean('verwerkt')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_transactie');
    }
};
