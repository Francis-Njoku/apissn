<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('entries')) {
            Schema::create('entries', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->decimal('amount', 10, 2);
                $table->string('status');
                $table->string('reference')->unique();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('entries', function (Blueprint $table) {
                if (!Schema::hasColumn('entries', 'email')) {
                    $table->string('email');
                }
                if (!Schema::hasColumn('entries', 'amount')) {
                    $table->decimal('amount', 10, 2);
                }
                if (!Schema::hasColumn('entries', 'status')) {
                    $table->string('status');
                }
                if (!Schema::hasColumn('entries', 'reference')) {
                    $table->string('reference')->unique();
                }
                if (!Schema::hasColumn('entries', 'ip_address')) {
                    $table->string('ip_address')->nullable();
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
