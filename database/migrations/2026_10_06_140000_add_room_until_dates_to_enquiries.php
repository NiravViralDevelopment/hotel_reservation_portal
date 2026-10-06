<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'single_until_date')) {
                $table->date('single_until_date')->nullable()->after('single_rate');
            }
            if (! Schema::hasColumn('enquiries', 'double_until_date')) {
                $table->date('double_until_date')->nullable()->after('double_rate');
            }
            if (! Schema::hasColumn('enquiries', 'triple_until_date')) {
                $table->date('triple_until_date')->nullable()->after('triple_rate');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            foreach (['single_until_date', 'double_until_date', 'triple_until_date'] as $column) {
                if (Schema::hasColumn('enquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
