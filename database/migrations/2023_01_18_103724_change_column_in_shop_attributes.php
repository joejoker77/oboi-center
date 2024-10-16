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
        Schema::table('shop_attributes', function (Blueprint $table) {
            $table->dropColumn('as_option');
            $table->string('mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_attributes', function (Blueprint $table) {
            $table->dropColumn('mode');
            $table->boolean('as_option');
        });
    }
};
