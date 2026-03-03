<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot met bedrag: splitsing van transactie over meerdere categorieën
        Schema::create('fin_transactie_categorie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transactie_id')->constrained('fin_transactie')->cascadeOnDelete();
            $table->foreignId('categorie_id')->constrained('fin_categorie');
            $table->decimal('bedrag', 15, 2)->nullable(); // null = volledig bedrag
            $table->string('opmerking')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_transactie_categorie');
    }
};