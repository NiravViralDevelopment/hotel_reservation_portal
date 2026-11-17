<?php

use App\Support\TotalDinnerCovers;
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

        if (! Schema::hasColumn('enquiries', 'total_dinner_covers')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->unsignedInteger('total_dinner_covers')->nullable()->after('dinner_revenue');
            });
        }

        DB::table('enquiries')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                DB::table('enquiries')->where('id', $row->id)->update([
                    'total_dinner_covers' => TotalDinnerCovers::calculate(
                        $this->decodeList($row->stay_segments ?? null),
                        $this->decodeList($row->daily_room_rates ?? null),
                        (int) ($row->single_rooms ?? 0),
                        (int) ($row->double_rooms ?? 0),
                        (int) ($row->triple_rooms ?? 0),
                        (int) ($row->nights ?? 0),
                    ),
                ]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'total_dinner_covers')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('total_dinner_covers');
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function decodeList(mixed $value): array
    {
        if (is_array($value)) {
            return array_is_list($value) ? $value : array_values($value);
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }
};
