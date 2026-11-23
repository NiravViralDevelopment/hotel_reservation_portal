<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bob_monthly_snapshots')) {
            return;
        }

        if (! Schema::hasColumn('bob_monthly_snapshots', 'hotel_id')) {
            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->foreignId('hotel_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('hotels')
                    ->cascadeOnDelete();
            });
        }

        $firstHotelId = DB::table('hotels')->orderBy('id')->value('id');
        if ($firstHotelId) {
            DB::table('bob_monthly_snapshots')
                ->whereNull('hotel_id')
                ->update(['hotel_id' => $firstHotelId]);
        } else {
            DB::table('bob_monthly_snapshots')->delete();
        }

        DB::table('bob_monthly_snapshots')->whereNull('hotel_id')->delete();

        $indexes = collect(Schema::getIndexes('bob_monthly_snapshots'));
        $hasYearMonthUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['year', 'month'];
        });
        $hasHotelYearMonthUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['hotel_id', 'year', 'month'];
        });

        if ($hasYearMonthUnique) {
            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->dropUnique(['year', 'month']);
            });
        }

        Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable(false)->change();
        });

        if (! $hasHotelYearMonthUnique) {
            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->unique(['hotel_id', 'year', 'month'], 'bob_monthly_snapshots_hotel_year_month_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bob_monthly_snapshots') || ! Schema::hasColumn('bob_monthly_snapshots', 'hotel_id')) {
            return;
        }

        $indexes = collect(Schema::getIndexes('bob_monthly_snapshots'));
        $hasHotelYearMonthUnique = $indexes->contains(function (array $index) {
            return ($index['name'] ?? null) === 'bob_monthly_snapshots_hotel_year_month_unique'
                || (($index['unique'] ?? false) && ($index['columns'] ?? []) === ['hotel_id', 'year', 'month']);
        });
        $hasYearMonthUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['year', 'month'];
        });

        if ($hasHotelYearMonthUnique) {
            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->dropUnique('bob_monthly_snapshots_hotel_year_month_unique');
            });
        }

        // Keep one row per year/month before restoring the old unique key.
        $duplicates = DB::table('bob_monthly_snapshots')
            ->select('year', 'month', DB::raw('MIN(id) as keep_id'))
            ->groupBy('year', 'month')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('bob_monthly_snapshots')
                ->where('year', $duplicate->year)
                ->where('month', $duplicate->month)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hotel_id');
        });

        if (! $hasYearMonthUnique) {
            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->unique(['year', 'month']);
            });
        }
    }
};
