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

            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->string(column: 'reservation_number')->unique();
            $table->string('waitlist_option')->nullable();
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->cascadeOnDelete();
            $table->date('desired_check_in_date')->nullable();
            $table->date('desired_check_out_date')->nullable();
            $table->boolean('is_notified')->default(false);
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
