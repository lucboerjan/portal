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
        Schema::create('fin_rekening_stand', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekening_id')->constrained('fin_rekening');
            $table->smallInteger('jaar');
            $table->tinyInteger('maand');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['rekening_id', 'jaar', 'maand']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_rekening_stand');
    }
};
