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
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check_ins')->onDelete('cascade');
            $table->string('guest_name')->nullable();
            $table->string('room_number')->nullable();
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0)->nullable();
            $table->decimal('additional_charges', 10, 2)->default(0)->nullable();
            $table->decimal('restaurant_charge', 10, 2)->default(0)->nullable();
            $table->string('late_check_out')->nullable();
            $table->string('car_hire')->nullable();
            $table->string('laundry')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_outs');
    }
};
