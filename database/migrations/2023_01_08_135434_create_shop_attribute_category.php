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
        Schema::create('shop_attribute_category', function (Blueprint $table) {
            $table
                ->foreignId('category_id')
                ->constrained('shop_categories')
                ->onDelete('CASCADE')
                ->onUpdate('RESTRICT');
            $table
                ->foreignId('attribute_id')
                ->constrained('shop_attributes')
                ->onDelete('CASCADE')
                ->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_attribute_category');
    }
};
