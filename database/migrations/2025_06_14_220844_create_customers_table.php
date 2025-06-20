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
        Schema::create('customers', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone', 20);
        $table->string('address');

        // Link to the company the customer belongs to
        $table->foreignId('company_id')
              ->constrained('companies')
              ->onDelete('cascade');

        // Assigned executive or user who manages the customer
        $table->foreignId('user_id')
              ->nullable()
              ->constrained('users')
              ->onDelete('set null');

        $table->boolean('is_active')->default(true);

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
