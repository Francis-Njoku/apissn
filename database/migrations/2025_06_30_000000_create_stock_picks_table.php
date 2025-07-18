<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up()
    {
        Schema::create('stock_picks', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 10);
            $table->foreignId('newsletter_id')->nullable()->constrained('newsletter');
            $table->date('recommendation_date');
            $table->decimal('initial_price', 10, 4);
            $table->decimal('current_price', 10, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_picks');
    }
};
