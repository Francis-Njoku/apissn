<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('plan_name');
                $table->string('track')->nullable();
                $table->string('plan_type');
                $table->decimal('amount', 10, 2);
                $table->timestamps();
            });
        } else {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'plan_name')) {
                    $table->string('plan_name');
                }
                if (!Schema::hasColumn('plans', 'track')) {
                    $table->string('track')->nullable();
                }
                if (!Schema::hasColumn('plans', 'plan_type')) {
                    $table->string('plan_type');
                }
                if (!Schema::hasColumn('plans', 'amount')) {
                    $table->decimal('amount', 10, 2);
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
