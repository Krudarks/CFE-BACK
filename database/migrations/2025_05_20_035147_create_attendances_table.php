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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->string('user_number', 5)->nullable(); // Número de usuario registrado
            $table->timestamp('entry_time')->nullable(); // Hora de entrada
            $table->timestamp('exit_time')->nullable();  // Hora de salida
            $table->date('date')->nullable();                        // Fecha de asistencia
            $table->boolean('is_late')->default(false);  // Retardo
            $table->integer('worker_count')->default(0);
            $table->timestamps();

            $table->softDeletes();// Para borrado suave
            $table->foreign('worker_id')->references('id')->on('workers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
