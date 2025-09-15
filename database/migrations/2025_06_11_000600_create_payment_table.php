<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if payment table exists, if not create it
        if (!Schema::hasTable('payment')) {
            Schema::create('payment', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('ip_address')->nullable();
                $table->string('order_id')->nullable();
                $table->text('gateway_response')->nullable();
                $table->text('status_response')->nullable();
                $table->string('reference')->unique();
                $table->decimal('amount', 10, 2);
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->date('due_date')->nullable();
                $table->string('status');
                $table->text('metadata')->nullable();
                $table->string('payment_method')->nullable(); // e.g., paystack, bank transfer
                $table->string('currency')->default('NGN');
                $table->string('transaction_id')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                // Foreign key will be added later after users table exists
            });
        } else {
            // Check and add columns if they don't exist
            Schema::table('payment', function (Blueprint $table) {
                if (!Schema::hasColumn('payment', 'payment_method')) {
                    $table->string('payment_method')->nullable();
                }
                if (!Schema::hasColumn('payment', 'currency')) {
                    $table->string('currency')->default('NGN');
                }
                if (!Schema::hasColumn('payment', 'transaction_id')) {
                    $table->string('transaction_id')->nullable();
                }
                if (!Schema::hasColumn('payment', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable();
                }
                if (!Schema::hasColumn('payment', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable();
                    // Foreign key will be added later after plans table exists
                }
                if (!Schema::hasColumn('payment', 'interval')) {
                    $table->string('interval')->nullable(); // monthly, quarterly, etc.
                }
                if (!Schema::hasColumn('payment', 'first_name')) {
                    $table->string('first_name')->nullable();
                }
                if (!Schema::hasColumn('payment', 'last_name')) {
                    $table->string('last_name')->nullable();
                }
                if (!Schema::hasColumn('payment', 'email')) {
                    $table->string('email')->nullable();
                }
                if (!Schema::hasColumn('payment', 'phone')) {
                    $table->string('phone')->nullable();
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
        Schema::dropIfExists('payment');
    }

    /**
     * Add foreign key constraints after all tables exist
     */
    public static function addForeignKeys()
    {
        if (Schema::hasTable('payment') && Schema::hasTable('users')) {
            Schema::table('payment', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('payment') && Schema::hasTable('plan')) {
            Schema::table('payment', function (Blueprint $table) {
                $table->foreign('plan_id')->references('id')->on('plan')->onDelete('set null');
            });
        }
    }
}
