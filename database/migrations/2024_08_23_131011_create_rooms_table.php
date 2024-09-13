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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id(); 
            $table->string('room_number')->unique()->nullable();
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade'); 
            $table->foreignId('housekeeper_id')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->string('price_per_night')->nullable();
            $table->boolean('status')->default(value: true);
            $table->boolean('is_clean')->default(false);
            $table->text('note')->nullable();
            $table->text('description')->nullable();
            $table->string('max_occupancy')->nullable();

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
