<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'breakfast_rate')) {
                $table->decimal('breakfast_rate', 12, 2)->nullable()->after('basis');
            }
            if (! Schema::hasColumn('enquiries', 'dinner_rate')) {
                $table->decimal('dinner_rate', 12, 2)->nullable()->after('breakfast_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['breakfast_rate', 'dinner_rate'],
                fn (string $column) => Schema::hasColumn('enquiries', $column)
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
