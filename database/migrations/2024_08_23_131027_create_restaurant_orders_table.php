<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade'); // Foreign key to guests table
            $table->text('items'); // JSON or text to store details of items ordered
            $table->decimal('total_amount', 8, 2); // Total amount for the order
            $table->enum('status', ['pending', 'preparing', 'served', 'billed', 'cancelled']); // Status of the order
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_orders');
    }
};
