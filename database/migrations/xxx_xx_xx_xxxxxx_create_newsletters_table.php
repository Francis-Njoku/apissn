<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewslettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('newsletters')) {
            Schema::create('newsletters', function (Blueprint $table) {
                $table->id();
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
                $table->json('tags')->nullable();
                $table->timestamps();

                $table->foreign('news_type_id')->references('id')->on('news_types')->onDelete('cascade');
            });
        } else {
            Schema::table('newsletters', function (Blueprint $table) {
                if (!Schema::hasColumn('newsletters', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('newsletters', 'title')) {
                    $table->string('title');
                }
                if (!Schema::hasColumn('newsletters', 'slug')) {
                    $table->string('slug')->unique();
                }
                if (!Schema::hasColumn('newsletters', 'body')) {
                    $table->longText('body');
                }
                if (!Schema::hasColumn('newsletters', 'news_date')) {
                    $table->date('news_date');
                }
                if (!Schema::hasColumn('newsletters', 'news_type_id')) {
                    $table->unsignedBigInteger('news_type_id');
                    $table->foreign('news_type_id')->references('id')->on('news_types')->onDelete('cascade');
                }
                if (!Schema::hasColumn('newsletters', 'status')) {
                    $table->string('status')->default('draft');
                }
                if (!Schema::hasColumn('newsletters', 'featured')) {
                    $table->boolean('featured')->default(false);
                }
                if (!Schema::hasColumn('newsletters', 'image')) {
                    $table->string('image')->nullable();
                }
                if (!Schema::hasColumn('newsletters', 'media')) {
                    $table->string('media')->nullable();
                }
                if (!Schema::hasColumn('newsletters', 'mediaType')) {
                    $table->string('mediaType')->nullable();
                }
                if (!Schema::hasColumn('newsletters', 'mediaSrc')) {
                    $table->string('mediaSrc')->nullable();
                }
                if (!Schema::hasColumn('newsletters', 'tags')) {
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
