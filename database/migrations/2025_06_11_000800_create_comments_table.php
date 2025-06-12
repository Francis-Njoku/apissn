<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up()
    {
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->morphs('commentable');
                $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
                $table->text('content');
                $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])->default('pending');
                $table->string('author_name')->nullable();
                $table->string('author_email')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('moderated_at')->nullable();
                $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index(['commentable_type', 'commentable_id']);
                $table->index('status');
                $table->index('parent_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
