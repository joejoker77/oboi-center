<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_filters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position')->default('left');
        });

        Schema::create('shop_filter_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('filter_id');
            $table->foreign('filter_id')->references('id')->on('shop_filters')->onDelete('CASCADE');
            $table->string('name');
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();
            $table->json('attributes')->nullable();
            $table->tinyInteger('display_header')->default(0);
        });

        Schema::create('shop_filters_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('filter_id');
            $table->foreign('filter_id')->references('id')->on('shop_filters')->onDelete('CASCADE');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('shop_categories')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_filters_categories');
        Schema::dropIfExists('shop_filter_groups');
        Schema::dropIfExists('shop_filters');
    }
};
