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
        Schema::create('status_car', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del estado (e.g. "Disponible")
            $table->string('code'); // Código único para el estado (e.g. "AVAILABLE", "IN_ROUTE")
            $table->text('description')->nullable(); // Descripción opcional del estado

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_car');
    }
};
