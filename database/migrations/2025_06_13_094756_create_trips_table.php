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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('trip_date');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->double('start_lat')->nullable();
            $table->double('start_lng')->nullable();
            $table->double('end_lat')->nullable();
            $table->double('end_lng')->nullable();
            $table->double('total_distance_km')->nullable();
            $table->enum('travel_mode', ['car', 'bike', 'walk', 'public_transport'])->default('car');
            $table->text('purpose')->nullable();
            $table->enum('status', ['started', 'in_progress', 'completed', 'cancelled'])->default('started');

            // Approval-related fields
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('approval_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // assumes managers are users
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
