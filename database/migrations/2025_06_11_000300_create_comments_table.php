<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('commentable_type');
                $table->unsignedBigInteger('commentable_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->text('content');
                $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])->default('pending');
                $table->string('author_name')->nullable();
                $table->string('author_email')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('moderated_at')->nullable();
                $table->unsignedBigInteger('moderated_by')->nullable();
                $table->timestamps();

                $table->index(['commentable_type', 'commentable_id']);
                $table->index('status');
                $table->index('parent_id');
            });
        } else {
            Schema::table('comments', function (Blueprint $table) {
                if (!Schema::hasColumn('comments', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('comments', 'commentable_type')) {
                    $table->string('commentable_type');
                }
                if (!Schema::hasColumn('comments', 'commentable_id')) {
                    $table->unsignedBigInteger('commentable_id');
                }
                if (!Schema::hasColumn('comments', 'parent_id')) {
                    $table->unsignedBigInteger('parent_id')->nullable();
                }
                if (!Schema::hasColumn('comments', 'content')) {
                    $table->text('content');
                }
                if (!Schema::hasColumn('comments', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])->default('pending');
                }
                if (!Schema::hasColumn('comments', 'author_name')) {
                    $table->string('author_name')->nullable();
                }
                if (!Schema::hasColumn('comments', 'author_email')) {
                    $table->string('author_email')->nullable();
                }
                if (!Schema::hasColumn('comments', 'ip_address')) {
                    $table->ipAddress('ip_address')->nullable();
                }
                if (!Schema::hasColumn('comments', 'metadata')) {
                    $table->json('metadata')->nullable();
                }
                if (!Schema::hasColumn('comments', 'moderated_at')) {
                    $table->timestamp('moderated_at')->nullable();
                }
                if (!Schema::hasColumn('comments', 'moderated_by')) {
                    $table->unsignedBigInteger('moderated_by')->nullable();
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
        Schema::dropIfExists('comments');
    }
}
