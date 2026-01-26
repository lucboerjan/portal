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
        Schema::create('utility_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utility_type_id')->constrained()->onDelete('cascade');
            $table->date('reading_date');
            $table->decimal('meter_stand', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['utility_type_id', 'reading_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_readings');
    }
};
