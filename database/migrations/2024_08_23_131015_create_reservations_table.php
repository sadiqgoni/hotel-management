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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('guest_id')->nullable()->constrained('guests')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('coupon_management_id')->nullable()->constrained('coupon_managements')->cascadeOnDelete();
            $table->date(column: 'check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('coupon_discount')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('price_per_night')->nullable();
            $table->string('status')->nullable();
            $table->text('special_requests')->nullable(); 
            $table->string('number_of_people')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
