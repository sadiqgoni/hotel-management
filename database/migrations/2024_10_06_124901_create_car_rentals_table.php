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
        Schema::create('car_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade'); // Links to cars
            $table->foreignId('guest_id')->constrained()->onDelete('cascade'); // Links to the guest who rents the car
            $table->timestamp('rented_at')->nullable(); // When the car was rented
            $table->timestamp('returned_at')->nullable(); // When the car was returned
            $table->decimal('total_cost', 10, 2)->nullable(); // Final cost of the rental
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_rentals');
    }
};
