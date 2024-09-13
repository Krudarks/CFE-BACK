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
        Schema::create('vehicle_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('status_id');
            $table->date('date_of_use');
            $table->time('start_time')->nullable(); // Hora de entrada
            $table->time('end_time')->nullable();  // Hora de salida
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('status_id')->references('id')->on('status_car');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_usage');
    }
};
