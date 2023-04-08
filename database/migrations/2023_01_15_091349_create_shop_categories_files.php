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
        Schema::table('shop_files', function (Blueprint $table) {
            $table->string('type')->after('name');
        });

        Schema::create('shop_categories_files', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('shop_categories')->onDelete('CASCADE')->onUpdate('RESTRICT');
            $table->foreignId('file_id')
                ->constrained('shop_files')->onDelete('CASCADE')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_categories_files');
    }
};
