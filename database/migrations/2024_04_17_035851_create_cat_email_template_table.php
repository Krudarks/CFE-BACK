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

        Schema::create('cat_email_template', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->nullable(false);
            $table->string('description', 200)->nullable();
            $table->string('title', 200)->nullable();
            $table->text('template')->nullable();
            $table->text('original_template')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_email_template');
    }
};
