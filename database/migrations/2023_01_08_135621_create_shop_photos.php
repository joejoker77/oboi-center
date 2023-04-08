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
        Schema::create('shop_photos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('path')->nullable(false);
            $table->string('alt_tag')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort')->nullable(false);
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('shop_brands')->onDelete('CASCADE')->onUpdate('RESTRICT');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_photos');
    }
};
