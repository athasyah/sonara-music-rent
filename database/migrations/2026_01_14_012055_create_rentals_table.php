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
        Schema::create('rentals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->foreignUuid('customer_id')->nullable()->constrained('users');
            $table->dateTime('rent_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->integer('total_price');
            $table->enum('status', ['pending','reserved', 'approved', 'ongoing', 'returned', 'cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
