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
            $table->string('phone');
            $table->string('address');

            // Foreign key to 'users' table for mapping to executive
            $table->unsignedBigInteger('executive_id')->nullable();
            $table->foreign('executive_id')->references('id')->on('users')->onDelete('set null');

            // Active/Inactive status flag
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
