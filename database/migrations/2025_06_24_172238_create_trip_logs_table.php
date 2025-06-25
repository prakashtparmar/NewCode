<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripLogsTable extends Migration
{
    public function up()
    {
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();

            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_logs');
    }
}
