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
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Assuming the name should be unique
            $table->string('driver')->nullable();
            $table->string('port')->nullable(); // If the port is not always needed
            $table->ipAddress('ip_address')->nullable(); // Dedicated IP address field
            $table->timestamps(); // Created_at and updated_at columns
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
