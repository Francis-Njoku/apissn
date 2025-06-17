<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAndAuthTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create role table
        if (!Schema::hasTable('role')) {
            Schema::create('role', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        } else {
            Schema::table('role', function (Blueprint $table) {
                if (!Schema::hasColumn('role', 'name')) {
                    $table->string('name')->unique();
                }
            });
        }

        // Create users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone')->nullable();
                $table->unsignedBigInteger('role_id')->nullable();
                $table->string('identity')->nullable();
                $table->string('status')->default('active');
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('role')->onDelete('set null');
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('users', 'email')) {
                    $table->string('email')->unique();
                }
                if (!Schema::hasColumn('users', 'first_name')) {
                    $table->string('first_name')->nullable();
                }
                if (!Schema::hasColumn('users', 'last_name')) {
                    $table->string('last_name')->nullable();
                }
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (!Schema::hasColumn('users', 'role_id')) {
                    $table->unsignedBigInteger('role_id')->nullable();
                    $table->foreign('role_id')->references('id')->on('role')->onDelete('set null');
                }
                if (!Schema::hasColumn('users', 'identity')) {
                    $table->string('identity')->nullable();
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status')->default('active');
                }
                if (!Schema::hasColumn('users', 'password')) {
                    $table->string('password');
                }
                if (!Schema::hasColumn('users', 'remember_token')) {
                    $table->rememberToken();
                }
            });
        }

        // Create password_resets table
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        } else {
            Schema::table('password_resets', function (Blueprint $table) {
                if (!Schema::hasColumn('password_resets', 'email')) {
                    $table->string('email')->index();
                }
                if (!Schema::hasColumn('password_resets', 'token')) {
                    $table->string('token');
                }
                if (!Schema::hasColumn('password_resets', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
            });
        }

        // Create user_groups table
        if (!Schema::hasTable('user_groups')) {
            Schema::create('user_groups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('group_id');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                // Note: 'group_id' foreign key is not added as 'groups' table is not defined in the requirements
            });
        } else {
            Schema::table('user_groups', function (Blueprint $table) {
                if (!Schema::hasColumn('user_groups', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
                if (!Schema::hasColumn('user_groups', 'group_id')) {
                    $table->unsignedBigInteger('group_id');
                    // Foreign key not added as 'groups' table is not defined
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
