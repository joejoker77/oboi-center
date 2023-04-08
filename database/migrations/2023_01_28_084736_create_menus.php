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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('handler');
            $table->string('title');
            $table->boolean('show_title')->default(false);
        });

        Schema::create('navItems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('CASCADE')->onUpdate('RESTRICT');
            $table->string('title');
            $table->string('link_text')->nullable();
            $table->string('route_name')->nullable();
            $table->string('item_path')->nullable();
            $table->string('entity_type');
            $table->integer('parent_id')->nullable();
            $table->integer('entity_id');
            $table->integer('front_id');
            $table->integer('front_parent');
            $table->integer('sort')->default(0);
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navItems');
        Schema::dropIfExists('menus');
    }
};
