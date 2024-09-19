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
        Schema::create('group_reservations', function (Blueprint $table) {
            $table->id();
            // Organization information
            $table->string('organization_name'); // Organization making the group reservation
            $table->string('contact_person');    // Main contact person for the group
            $table->string('contact_phone')->nullable(); // Contact person's phone
            $table->string('contact_email')->nullable(); // Contact person's email
            // Group details
            $table->integer('group_size'); // Number of people in the group

            $table->foreignId(column: 'primary_guest_id')->nullable()->constrained('guests')->cascadeOnDelete();
            $table->foreignId('coupon_management_id')->nullable()->constrained('coupon_managements')->cascadeOnDelete();

            // Billing information
            $table->string('total_amount')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('remaining_balance')->default(0); // Remaining balance
            // Room management
            $table->json('room_ids'); // Stores an array of room IDs assigned to the group
            // Reservation dates
            $table->date('check_in_date'); // Check-in date for the group
            $table->date('check_out_date'); // Check-out date for the group
            // Special requests
            $table->text('special_requests')->nullable(); // Special requests by the organization
            $table->string('payment_method')->nullable();
            $table->string('coupon_discount')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('price_per_night')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_reservations');
    }
};
