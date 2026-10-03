<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('documents') && Schema::hasColumn('documents', 'group_booking_id')) {
            $this->dropForeignIfExists('documents', 'group_booking_id');
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'group_booking_id')) {
                    $table->dropColumn('group_booking_id');
                }
            });
        }

        if (Schema::hasTable('enquiries') && Schema::hasColumn('enquiries', 'converted_booking_id')) {
            $this->dropForeignIfExists('enquiries', 'converted_booking_id');
            Schema::table('enquiries', function (Blueprint $table) {
                if (Schema::hasColumn('enquiries', 'converted_booking_id')) {
                    $table->dropColumn('converted_booking_id');
                }
            });
        }

        Schema::dropIfExists('group_booking_daily_rows');
        Schema::dropIfExists('group_bookings');
    }

    public function down(): void
    {
        // Intentionally empty — group bookings module removed.
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        $database = DB::getDatabaseName();
        $constraints = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$database, $table, $column]
        );

        foreach ($constraints as $constraint) {
            $name = $constraint->CONSTRAINT_NAME ?? null;
            if ($name) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
            }
        }
    }
};
