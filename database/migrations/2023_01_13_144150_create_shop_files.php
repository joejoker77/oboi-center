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
        Schema::table('shop_categories', function (Blueprint $table) {
            $table->string('import_id')->nullable();
            $table->string('supplier')->nullable();
        });

        Schema::table('shop_products', function (Blueprint $table) {
            $table->string('import_id')->nullable();
            $table->string('supplier')->nullable();
        });

        Schema::create('shop_files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('path')->nullable(false);
            $table->text('description')->nullable();
            $table->integer('sort')->nullable(false);
        });

        Schema::create('shop_categories_photos', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('shop_categories')->onDelete('CASCADE')->onUpdate('RESTRICT');
            $table->foreignId('photo_id')
                ->constrained('shop_photos')->onDelete('CASCADE')->onUpdate('RESTRICT');
        });

        Schema::create('shop_products_photos', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('shop_products')->onDelete('CASCADE')->onUpdate('RESTRICT');
            $table->foreignId('photo_id')
                ->constrained('shop_photos')->onDelete('CASCADE')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropColumns('shop_products', ['import_id', 'supplier']);
        Schema::dropColumns('shop_categories', ['import_id', 'supplier']);
        Schema::dropIfExists('shop_files');
        Schema::dropIfExists('shop_categories_photos');
        Schema::dropIfExists('shop_products_photos');
    }
};
