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
        // Add user_id foreign key constraint
        if (Schema::hasTable('payment') && Schema::hasTable('users')) {
            Schema::table('payment', function (Blueprint $table) {
                if (
                    Schema::hasColumn('payment', 'user_id') &&
                    Schema::hasColumn('users', 'id')
                ) {

                    // Check if foreign key already exists before adding
                    if (!$this->foreignKeyExists('payment', 'payment_user_id_foreign')) {
                        $table->foreign('user_id', 'payment_user_id_foreign')
                            ->references('id')
                            ->on('users')
                            ->onDelete('cascade');
                    }
                }
            });
        }

        // Add plan_id foreign key constraint
        if (Schema::hasTable('payment') && Schema::hasTable('plan')) {
            Schema::table('payment', function (Blueprint $table) {
                if (
                    Schema::hasColumn('payment', 'plan_id') &&
                    Schema::hasColumn('plan', 'id')
                ) {

                    // Check if foreign key already exists before adding
                    if (!$this->foreignKeyExists('payment', 'payment_plan_id_foreign')) {
                        $table->foreign('plan_id', 'payment_plan_id_foreign')
                            ->references('id')
                            ->on('plan')
                            ->onDelete('set null');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            // Drop foreign keys by their constraint names
            if ($this->foreignKeyExists('payment', 'payment_user_id_foreign')) {
                $table->dropForeign('payment_user_id_foreign');
            }
            if ($this->foreignKeyExists('payment', 'payment_plan_id_foreign')) {
                $table->dropForeign('payment_plan_id_foreign');
            }
        });
    }

    /**
     * Check if a foreign key constraint exists on a table
     *
     * @param string $tableName
     * @param string $constraintName
     * @return bool
     */
    private function foreignKeyExists(string $tableName, string $constraintName): bool
    {
        $database = config('database.connections.mysql.database');

        $foreignKeyCount = \DB::selectOne("
            SELECT COUNT(*) as count
            FROM information_schema.table_constraints
            WHERE table_schema = ?
            AND table_name = ?
            AND constraint_name = ?
            AND constraint_type = 'FOREIGN KEY'
        ", [$database, $tableName, $constraintName]);

        return $foreignKeyCount && $foreignKeyCount->count > 0;
    }
};
