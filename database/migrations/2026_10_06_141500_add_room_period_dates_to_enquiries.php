<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'single_from_date')) {
                $table->date('single_from_date')->nullable()->after('single_rate');
            }
            if (! Schema::hasColumn('enquiries', 'single_to_date')) {
                $table->date('single_to_date')->nullable()->after('single_from_date');
            }
            if (! Schema::hasColumn('enquiries', 'double_from_date')) {
                $table->date('double_from_date')->nullable()->after('double_rate');
            }
            if (! Schema::hasColumn('enquiries', 'double_to_date')) {
                $table->date('double_to_date')->nullable()->after('double_from_date');
            }
            if (! Schema::hasColumn('enquiries', 'triple_from_date')) {
                $table->date('triple_from_date')->nullable()->after('triple_rate');
            }
            if (! Schema::hasColumn('enquiries', 'triple_to_date')) {
                $table->date('triple_to_date')->nullable()->after('triple_from_date');
            }
        });

        foreach (['single', 'double', 'triple'] as $type) {
            $until = $type.'_until_date';
            $to = $type.'_to_date';
            if (Schema::hasColumn('enquiries', $until) && Schema::hasColumn('enquiries', $to)) {
                DB::table('enquiries')
                    ->whereNull($to)
                    ->whereNotNull($until)
                    ->update([$to => DB::raw($until)]);
            }
        }

        $drop = array_values(array_filter(
            ['single_until_date', 'double_until_date', 'triple_until_date'],
            fn (string $column) => Schema::hasColumn('enquiries', $column)
        ));

        if ($drop !== []) {
            Schema::table('enquiries', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            foreach (['single_until_date' => 'single_rate', 'double_until_date' => 'double_rate', 'triple_until_date' => 'triple_rate'] as $column => $after) {
                if (! Schema::hasColumn('enquiries', $column)) {
                    $table->date($column)->nullable()->after($after);
                }
            }
        });

        $drop = array_values(array_filter(
            ['single_from_date', 'single_to_date', 'double_from_date', 'double_to_date', 'triple_from_date', 'triple_to_date'],
            fn (string $column) => Schema::hasColumn('enquiries', $column)
        ));

        if ($drop !== []) {
            Schema::table('enquiries', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }
    }
};
