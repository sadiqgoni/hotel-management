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
        Schema::create('staff_managements', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('full_name'); // Staff's full name
            $table->string('email')->unique(); // Staff's email
            $table->string('phone_number')->nullable(); // Staff's contact number
            $table->string('role'); // Job role (e.g., Manager, Receptionist, Housekeeping)
            $table->string('status')->default('active'); // Employment status (e.g., active, suspended)
            $table->date('employment_date')->nullable(); // Date when staff was hired
            $table->date('termination_date')->nullable(); // Date when staff left (optional)
            $table->string('profile_picture')->nullable(); // URL for staff's profile picture (optional)
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable(); // Staff's address
            $table->string('shift')->nullable(); // Staff's working shift (e.g., morning, evening, night)
            $table->string('next_of_kin_name')->nullable(); // Next of kin's full name
            $table->text('next_of_kin_address')->nullable(); // Next of kin's address
            $table->string('next_of_kin_phone_number')->nullable(); // Next of kin's contact number

            $table->timestamps(); // Created_at and updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_management');
    }
};
