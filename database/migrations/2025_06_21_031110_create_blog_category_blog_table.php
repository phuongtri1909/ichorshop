<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_category_blog', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id');
            $table->unsignedBigInteger('category_blog_id');
            $table->timestamps();

            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
            $table->foreign('category_blog_id')->references('id')->on('category_blogs')->onDelete('cascade');

            $table->unique(['blog_id', 'category_blog_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_category_blog');
    }
};
