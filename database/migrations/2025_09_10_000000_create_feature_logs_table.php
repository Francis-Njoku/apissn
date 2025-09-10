<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feature_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('feature_name');
            $table->integer('usage_count')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feature_logs');
    }
};