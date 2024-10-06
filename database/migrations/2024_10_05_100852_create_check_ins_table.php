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
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('reservation_number')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->string('room_number')->nullable();    
            $table->string('guest_name')->nullable();   
            $table->string('guest_phone')->nullable();  // Store guest phone number
            $table->string('paid_amount')->nullable(); 
            $table->string('due_amount')->nullable(); 
            $table->string(column: 'booking_status')->default('Check In');
            $table->string('payment_status')->nullable();
            $table->string('coupon_management')->nullable();     
            $table->string('total_amount')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('coupon_discount')->nullable();
            $table->string('frequent_guest_message')->nullable();
            $table->string('number_of_nights')->nullable();
            $table->text('special_requests')->nullable(); 
            $table->string('number_of_people')->nullable();
            $table->text(column: 'price_per_night')->nullable();
            $table->text(column: 'restaurant_bill')->nullable();

            $table->timestamps();    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
