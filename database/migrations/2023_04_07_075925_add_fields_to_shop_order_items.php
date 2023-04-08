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
        Schema::table('shop_order_items', function (Blueprint $table) {
            $table->string('product_name');
            $table->string('product_sku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_order_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('product_sku');
        });
    }
};
