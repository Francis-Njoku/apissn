<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsletterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('newsletter')) {
            Schema::create('newsletter', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('author_id');
                $table->string('name');
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('body');
                $table->date('news_date');
                $table->unsignedBigInteger('news_type_id');
                $table->string('status')->default('draft');
                $table->boolean('featured')->default(false);
                $table->string('image')->nullable();
                $table->string('media')->nullable();
                $table->string('mediaType')->nullable();
                $table->string('mediaSrc')->nullable();
                $table->string('featuredImage')->nullable();
                $table->json('tags')->nullable();
                $table->timestamps();

                $table->foreign('news_type_id')->references('id')->on('news_type')->onDelete('cascade');
            });
        } else {
            Schema::table('newsletter', function (Blueprint $table) {
                if (!Schema::hasColumn('newsletter', 'author_id')) {
                    $table->unsignedBigInteger('author_id');
                }
                if (!Schema::hasColumn('newsletter', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('newsletter', 'title')) {
                    $table->string('title');
                }
                if (!Schema::hasColumn('newsletter', 'slug')) {
                    $table->string('slug')->unique();
                }
                if (!Schema::hasColumn('newsletter', 'body')) {
                    $table->longText('body');
                }
                if (!Schema::hasColumn('newsletter', 'news_date')) {
                    $table->date('news_date');
                }
                if (!Schema::hasColumn('newsletter', 'news_type_id')) {
                    $table->unsignedBigInteger('news_type_id');
                    $table->foreign('news_type_id')->references('id')->on('news_type')->onDelete('cascade');
                }
                if (!Schema::hasColumn('newsletter', 'status')) {
                    $table->string('status')->default('draft');
                }
                if (!Schema::hasColumn('newsletter', 'featured')) {
                    $table->boolean('featured')->default(false);
                }
                if (!Schema::hasColumn('newsletter', 'image')) {
                    $table->string('image')->nullable();
                }
                if (!Schema::hasColumn('newsletter', 'media')) {
                    $table->string('media')->nullable();
                }
                if (!Schema::hasColumn('newsletter', 'mediaType')) {
                    $table->string('mediaType')->nullable();
                }
                if (!Schema::hasColumn('newsletter', 'mediaSrc')) {
                    $table->string('mediaSrc')->nullable();
                }
                if (!Schema::hasColumn('newsletter', 'featuredImage')) {
                    $table->string('featuredImage')->nullable();
                }
                if (!Schema::hasColumn('newsletter', 'tags')) {
                    $table->json('tags')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't want to drop tables in the down method
        // as this migration is meant to ensure tables exist
    }
}
