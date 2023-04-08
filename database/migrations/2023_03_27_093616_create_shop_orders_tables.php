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
        Schema::create('shop_delivery_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('cost')->nullable();
            $table->integer('min_weight')->default(0);
            $table->integer('max_weight')->default(0);
            $table->integer('min_amount')->default(0);
            $table->integer('max_amount')->default(0);
            $table->integer('min_dimensions')->default(0);
            $table->integer('max_dimensions')->default(0);
            $table->integer('sort');
        });

        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->unsignedBigInteger('delivery_id');
            $table->foreign('delivery_id')->references('id')->on('shop_delivery_methods')->onDelete('CASCADE');
            $table->string('payment_method');
            $table->integer('cost');
            $table->text('note');
            $table->integer('current_status');
            $table->text('cancel_reason');
            $table->json('statuses');
            $table->string('customer_phone');
            $table->string('customer_name');
            $table->string('delivery_index');
            $table->text('delivery_address');
            $table->integer('payment_id');
            $table->timestamps();
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('shop_orders')->onDelete('CASCADE');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('shop_products');
            $table->integer('price');
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
        Schema::dropIfExists('shop_delivery_methods');
    }
};
