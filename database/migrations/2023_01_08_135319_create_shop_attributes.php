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
        Schema::create('shop_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('type')->nullable(false);
            $table->string('default_value')->nullable();
            $table->tinyInteger('as_option')->nullable();
            $table->string('unit')->nullable();
            $table->json('variants')->nullable(false);
            $table->integer('sort')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_attributes');
    }
};
