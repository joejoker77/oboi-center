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
        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('CASCADE');
            $table->integer('quantity');
            $table->string('type');
        });

        Schema::create('shop_discounts', function (Blueprint $table) {
            $table->id();
            $table->integer('percent');
            $table->string('name');
            $table->timestamp('from_date');
            $table->timestamp('to_date');
            $table->boolean('active')->default(0);
            $table->integer('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_cart_items');
        Schema::dropIfExists('shop_discounts');
    }
};
