<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bob_monthly_snapshots')) {
            return;
        }

        if (! Schema::hasColumn('bob_monthly_snapshots', 'bob_nett')) {
            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->decimal('bob_nett', 14, 2)->nullable();
                $table->decimal('stly_bob_nett', 14, 2)->nullable();
                $table->decimal('adr_nett', 10, 2)->nullable();
                $table->decimal('stly_adr_nett', 10, 2)->nullable();
                $table->unsignedInteger('room_nights')->nullable();
            });
        }

        $this->makeMetricColumnsNullable();

        if (Schema::hasColumn('bob_monthly_snapshots', 'bob_current')) {
            $this->moveYearRelativeFiguresOntoTheirOwnYear();

            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $columns = array_values(array_filter([
                    'bob_current',
                    'bob_previous',
                    'bob_variance',
                    'stly_bob',
                    'adr_current',
                    'adr_previous',
                    'adr_variance',
                    'adr_stly',
                    'room_nights_current',
                    'room_nights_previous',
                    'stly_breakfast_revenue',
                ], fn (string $column) => Schema::hasColumn('bob_monthly_snapshots', $column)));

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        $this->grantBobPermission();
    }

    public function down(): void
    {
        if (Schema::hasTable('bob_monthly_snapshots') && ! Schema::hasColumn('bob_monthly_snapshots', 'bob_current')) {
            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->decimal('bob_current', 14, 2)->default(0);
                $table->decimal('bob_previous', 14, 2)->default(0);
                $table->decimal('bob_variance', 14, 2)->default(0);
                $table->decimal('stly_bob', 14, 2)->default(0);
                $table->decimal('adr_current', 10, 2)->default(0);
                $table->decimal('adr_previous', 10, 2)->default(0);
                $table->decimal('adr_variance', 10, 2)->default(0);
                $table->decimal('adr_stly', 10, 2)->default(0);
                $table->unsignedInteger('room_nights_current')->default(0);
                $table->unsignedInteger('room_nights_previous')->default(0);
                $table->decimal('stly_breakfast_revenue', 14, 2)->default(0);
            });

            $rows = DB::table('bob_monthly_snapshots')->orderByDesc('year')->orderBy('month')->get();
            $byYearMonth = $rows->keyBy(fn ($row) => $row->year.'-'.$row->month);

            foreach ($rows as $row) {
                $previous = $byYearMonth->get(((int) $row->year - 1).'-'.$row->month);

                DB::table('bob_monthly_snapshots')->where('id', $row->id)->update([
                    'bob_current' => $row->bob_nett ?? 0,
                    'bob_previous' => $previous?->bob_nett ?? 0,
                    'adr_current' => $row->adr_nett ?? 0,
                    'adr_previous' => $previous?->adr_nett ?? 0,
                    'room_nights_current' => $row->room_nights ?? 0,
                    'room_nights_previous' => $previous?->room_nights ?? 0,
                    'stly_bob' => $previous?->stly_bob_nett ?? 0,
                    'adr_stly' => $previous?->stly_adr_nett ?? 0,
                    'stly_room_nights' => $previous?->stly_room_nights ?? 0,
                ]);
            }

            Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
                $table->dropColumn([
                    'bob_nett',
                    'stly_bob_nett',
                    'adr_nett',
                    'stly_adr_nett',
                    'room_nights',
                ]);
            });
        }

        $this->revokeBobPermission();
    }

    private function makeMetricColumnsNullable(): void
    {
        Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
            if (Schema::hasColumn('bob_monthly_snapshots', 'breakfast_revenue')) {
                $table->decimal('breakfast_revenue', 14, 2)->nullable()->default(null)->change();
            }
            if (Schema::hasColumn('bob_monthly_snapshots', 'dinner_revenue')) {
                $table->decimal('dinner_revenue', 14, 2)->nullable()->default(null)->change();
            }
            if (Schema::hasColumn('bob_monthly_snapshots', 'dinner_covers')) {
                $table->unsignedInteger('dinner_covers')->nullable()->default(null)->change();
            }
            if (Schema::hasColumn('bob_monthly_snapshots', 'stly_room_nights')) {
                $table->unsignedInteger('stly_room_nights')->nullable()->default(null)->change();
            }
        });
    }

    /**
     * Each stored row keeps only the figures for its own year.
     * Values that used to sit on the following year (previous-year actuals and STLY)
     * are written onto that earlier year.
     */
    private function moveYearRelativeFiguresOntoTheirOwnYear(): void
    {
        $rows = DB::table('bob_monthly_snapshots')->orderBy('year')->orderBy('month')->get();
        $existing = [];

        foreach ($rows as $row) {
            $existing[$row->year.'-'.$row->month] = true;

            DB::table('bob_monthly_snapshots')->where('id', $row->id)->update([
                'bob_nett' => $row->bob_current,
                'adr_nett' => $row->adr_current,
                'room_nights' => $row->room_nights_current,
            ]);
        }

        $stlyRoomNights = [];

        foreach ($rows as $row) {
            $previousYear = (int) $row->year - 1;
            $key = $previousYear.'-'.$row->month;
            $stlyRoomNights[$key] = $row->stly_room_nights;
            $stly = [
                'stly_bob_nett' => $row->stly_bob,
                'stly_adr_nett' => $row->adr_stly,
                'updated_at' => now(),
            ];

            if (isset($existing[$key])) {
                DB::table('bob_monthly_snapshots')
                    ->where('year', $previousYear)
                    ->where('month', $row->month)
                    ->update($stly);

                continue;
            }

            DB::table('bob_monthly_snapshots')->insert([
                'year' => $previousYear,
                'month' => $row->month,
                'label' => $row->label,
                'bob_nett' => $row->bob_previous,
                'adr_nett' => $row->adr_previous,
                'room_nights' => $row->room_nights_previous,
                'stly_bob_nett' => $row->stly_bob,
                'stly_adr_nett' => $row->adr_stly,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $existing[$key] = true;
        }

        DB::table('bob_monthly_snapshots')->update(['stly_room_nights' => null]);

        foreach ($stlyRoomNights as $key => $roomNights) {
            [$year, $month] = array_map('intval', explode('-', (string) $key, 2));

            DB::table('bob_monthly_snapshots')
                ->where('year', $year)
                ->where('month', $month)
                ->update(['stly_room_nights' => $roomNights]);
        }
    }

    private function grantBobPermission(): void
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles') || ! Schema::hasTable('role_has_permissions')) {
            return;
        }

        $now = now();
        $permissionId = DB::table('permissions')
            ->where('name', 'bob.view')
            ->where('guard_name', 'web')
            ->value('id');

        if ($permissionId === null) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => 'bob.view',
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $roleIds = DB::table('roles')
            ->where('guard_name', 'web')
            ->whereIn('name', ['Administrator', 'User'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_has_permissions')
                ->where('permission_id', $permissionId)
                ->where('role_id', $roleId)
                ->exists();

            if (! $exists) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function revokeBobPermission(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissionId = DB::table('permissions')
            ->where('name', 'bob.view')
            ->where('guard_name', 'web')
            ->value('id');

        if ($permissionId !== null && Schema::hasTable('role_has_permissions')) {
            DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }

        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
};
