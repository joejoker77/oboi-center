<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('phone_auth')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->string('phone_verify_token')->nullable();
            $table->string('phone_verify_token_expire')->nullable();
            $table->string('role', 16)->default('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};
