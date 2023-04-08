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
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->text('note')->nullable()->change();
            $table->text('cancel_reason')->nullable()->change();
            $table->integer('payment_id')->nullable()->change();
            $table->string('payment_method')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->text('note')->nullable(false)->change();
            $table->text('cancel_reason')->nullable(false)->change();
            $table->integer('payment_id')->nullable(false)->change();
            $table->string('payment_method')->nullable(false)->change();
        });
    }
};
