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
        Schema::create('coupon_managements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();  // Unique coupon code (e.g., 'WEEKEND10')
            $table->string('description')->nullable();  // Optional description for the coupon
            $table->string('discount_type')->nullable();  
            $table->integer('discount_amount')->nullable(); 
            $table->string('discount_percentage')->nullable(); 
            $table->integer('usage_limit')->default(1); 
            $table->integer('times_used')->default(0); 
            $table->date('valid_from')->nullable();  // Date from which the coupon is valid
            $table->date('valid_until')->nullable();  // Date until which the coupon is valid
            $table->string('status')->nullable(); // Employment status (e.g., active, suspended)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_management');
    }
};
