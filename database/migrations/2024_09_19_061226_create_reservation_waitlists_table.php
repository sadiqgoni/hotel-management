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
        Schema::create('reservation_waitlists', function (Blueprint $table) {
            $table->id();

            // Link to the guest table (foreign key constraint)
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();

            $table->string('waitlist_option')->nullable();


            // Allow the guest to choose either room type or specific room (but not both)
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->cascadeOnDelete();

            // Desired check-in and check-out dates
            $table->date('desired_check_in_date')->nullable();
            $table->date('desired_check_out_date')->nullable();

            // Track if the guest has been notified about room availability
            $table->boolean('is_notified')->default(false);

            // Timestamps for created_at and updated_at
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_waitlists');
    }
};
