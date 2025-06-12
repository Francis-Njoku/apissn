<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCommentsToMonomorphic extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'newsletter_id')) {
                $table->unsignedBigInteger('newsletter_id')->nullable();
            } else {
                $table->unsignedBigInteger('newsletter_id')->nullable()->change();
            }

            try {
                $table->foreign('newsletter_id')
                      ->references('id')
                      ->on('newsletter')
                      ->onDelete('cascade');
            } catch (\Exception $e) {
                // Skip if foreign key already exists
            }
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['newsletter_id']);
        });
    }
}
