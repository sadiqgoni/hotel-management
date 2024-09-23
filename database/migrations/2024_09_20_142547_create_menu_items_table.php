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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_category_id')->constrained()->cascadeOnDelete();  // Link to categories
            $table->string('name');  // Food item name
            $table->text('description')->nullable();  // Optional description
            $table->decimal('price', 10, 2);  // Price of the item
            $table->string('image')->nullable();  // Optional image
            $table->boolean('is_available')->default(true);  // Is the item currently available?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
