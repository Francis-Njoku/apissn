<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('plan')) {
            Schema::create('plan', function (Blueprint $table) {
                $table->id();
                $table->string('plan_name');
                $table->string('track')->nullable();
                $table->string('plan_type');
                $table->decimal('amount', 10, 2);
                $table->timestamps();
            });
        } else {
            Schema::table('plan', function (Blueprint $table) {
                if (!Schema::hasColumn('plan', 'plan_name')) {
                    $table->string('plan_name');
                }
                if (!Schema::hasColumn('plan', 'track')) {
                    $table->string('track')->nullable();
                }
                if (!Schema::hasColumn('plan', 'plan_type')) {
                    $table->string('plan_type');
                }
                if (!Schema::hasColumn('plan', 'amount')) {
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
