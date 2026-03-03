<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_rekening', function (Blueprint $table) {
            $table->id();
            $table->string('referentie')->unique();
            $table->string('omschrijving');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->smallInteger('order')->default(0);
            $table->enum('rekening_type', ['zichtrekening', 'spaarrekening', 'kredietkaart', 'cash']);
            $table->boolean('actief')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_rekening');
    }
};