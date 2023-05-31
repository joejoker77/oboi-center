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
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('slug')->after('title');
        });

        Schema::create('blog_category_post', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained('blog_categories')->onDelete('CASCADE')->onUpdate('RESTRICT');
            $table->foreignId('post_id')->constrained('blog_posts')->onDelete('CASCADE')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::dropIfExists('blog_category_post');
    }
};
