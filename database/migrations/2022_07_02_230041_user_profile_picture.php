<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserProfilePicture extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('user_profile_picture')) {
            Schema::create('user_profile_picture', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->text('crop_setting')->nullable();
                $table->string('path', 250);
                $table->string('path_original', 250);
                $table->string('disk', 25);

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_picture');
    }
}
