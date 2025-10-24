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
                if (!Schema::hasColumn('payment', 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn('payment', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('payment', 'ip_address')) {
                    $table->string('ip_address')->nullable();
                }
                if (!Schema::hasColumn('payment', 'order_id')) {
                    $table->string('order_id')->nullable();
                }
                if (!Schema::hasColumn('payment', 'gateway_response')) {
                    $table->text('gateway_response')->nullable();
                }
                if (!Schema::hasColumn('payment', 'status_response')) {
                    $table->text('status_response')->nullable();
                }
                if (!Schema::hasColumn('payment', 'reference')) {
                    $table->string('reference')->unique();
                }
                if (!Schema::hasColumn('payment', 'amount')) {
                    $table->decimal('amount', 10, 2);
                }
                if (!Schema::hasColumn('payment', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable();
                }
                if (!Schema::hasColumn('payment', 'due_date')) {
                    $table->date('due_date')->nullable();
                }
                if (!Schema::hasColumn('payment', 'status')) {
                    $table->string('status');
                }
                if (!Schema::hasColumn('payment', 'metadata')) {
                    $table->text('metadata')->nullable();
                }
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
                if (!Schema::hasColumn('payment', 'created_at')) {
                    $table->timestamps();
                }
                if (!Schema::hasColumn('payment', 'updated_at')) {
                    $table->timestamps();
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
        // Only drop the table if it exists
        if (Schema::hasTable('payment')) {
            Schema::dropIfExists('payment');
        }
    }

    /**
     * Add foreign key constraints after all tables exist
     *
     * @deprecated Use the separate migration 2025_06_11_000700_add_payment_foreign_keys.php instead
     */
    public static function addForeignKeys()
    {
        // This method is deprecated - use the separate migration file instead
        // The foreign keys are now handled in 2025_06_11_000700_add_payment_foreign_keys.php
    }
}
