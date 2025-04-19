<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 'news_type_id', 'name', 'title', 'slug', 'body','news_date','featuredImage', 'media','mediaType','status'
        Schema::create('newsletter', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\NewsType::class, 'news_type_id');
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->date('news_date')->nullable();
            $table->string('status')->nullable();
            $table->mediumText('body')->nullable();
            $table->string('featuredImage')->nullable();
            $table->string('mediaType')->nullable();
            $table->string('media')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter');
    }
};
