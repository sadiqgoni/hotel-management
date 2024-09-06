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
        Schema::create('laundries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade'); // Foreign key to guests table
            $table->string('item'); // Description of the item to be laundered
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled']); // Status of the laundry request
            $table->decimal('amount', 8, 2)->nullable(); // Amount charged for the laundry service
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundries');
    }
};
