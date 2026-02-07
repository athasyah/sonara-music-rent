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
        Schema::create('instrument_conditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('instrument_id')->nullable()->constrained();
            $table->foreignUuid('rental_id')->nullable()->constrained();
            $table->enum('condition',['good','minor_damage','major_damage'])->nullable();
            $table->string('note')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insrument_conditions');
    }
};
