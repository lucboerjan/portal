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
        Schema::create('internet_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignID('internet_group_id')->constrained()->cascadeOnDelete();
            $table->string('url',200)->default('');
            $table->string('link_title',200)->default('');
            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internet_links');
    }
};
