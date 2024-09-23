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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->foreignId(column: 'menu_item_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);  // Price of each item
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
