<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('title')->nullable();
            $table->string('status');
            $table->text('description')->nullable();
            $table->json('meta');
            NestedSet::columns($table);
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('CASCADE')->onUpdate('RESTRICT');
            $table->string('title');
            $table->text('description');
            $table->text('content');
            $table->string('status');
            $table->integer('sort');
            $table->json('meta');
            $table->timestamps();
        });

        Schema::create('blog_posts_photos', function (Blueprint $table) {
            $table->foreignId('post_id')
                ->constrained('blog_posts')->onDelete('CASCADE')->onUpdate('RESTRICT');
            $table->foreignId('photo_id')
                ->constrained('shop_photos')->onDelete('CASCADE')->onUpdate('RESTRICT');
        });

        Schema::create('blog_categories_photos', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('blog_categories')->onDelete('CASCADE')->onUpdate('RESTRICT');
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
        Schema::dropIfExists('blog_categories_photos');
        Schema::dropIfExists('blog_posts_photos');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_categories');
    }
};
