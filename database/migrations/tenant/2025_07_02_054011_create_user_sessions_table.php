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
        Schema::create('user_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('ip_address')->nullable();
    $table->string('user_agent')->nullable();
    $table->string('platform', 20)->default('web');
    $table->timestamp('login_at')->nullable();
    $table->timestamp('logout_at')->nullable();
    $table->integer('session_duration')->nullable(); // in seconds
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
