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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('car_name');
            $table->string('car_type'); // e.g., Sedan, SUV, etc.
            $table->string('number_plate')->unique();
            $table->enum('availability_status', ['available', 'rented', 'maintenance'])->default('available');
            $table->decimal('rate_per_hour', 8, 2); // Rental rate per hour
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
