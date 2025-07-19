<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Retry up to 3 times with delay to ensure tables exist
        $maxAttempts = 3;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            try {
                if (Schema::hasTable('payment') && Schema::hasTable('users')) {
                    Schema::table('payment', function (Blueprint $table) {
                        if (Schema::hasColumn('payment', 'user_id') && 
                            Schema::hasColumn('users', 'id')) {
                            $table->foreign('user_id')
                                ->references('id')
                                ->on('users')
                                ->onDelete('cascade');
                        }
                    });
                }

                if (Schema::hasTable('payment') && Schema::hasTable('plans')) {
                    Schema::table('payment', function (Blueprint $table) {
                        if (Schema::hasColumn('payment', 'plan_id') && 
                            Schema::hasColumn('plans', 'id')) {
                            $table->foreign('plan_id')
                                ->references('id')
                                ->on('plans')
                                ->onDelete('set null');
                        }
                    });
                }
                
                break; // Success - exit loop
            } catch (\Exception $e) {
                $attempt++;
                if ($attempt >= $maxAttempts) {
                    throw $e; // Re-throw if final attempt fails
                }
                sleep(2); // Wait 2 seconds before retry
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['plan_id']);
        });
    }
};
