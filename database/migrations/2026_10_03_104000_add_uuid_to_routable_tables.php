<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $tables = [
        'companies',
        'hotels',
        'travel_agencies',
        'contacts',
        'enquiries',
        'group_bookings',
        'documents',
        'status_masters',
        'roles',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            $this->addUuidColumn($table);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropUnique(['uuid']);
                    $blueprint->dropColumn('uuid');
                });
            }
        }
    }

    private function addUuidColumn(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (! Schema::hasColumn($table, 'uuid')) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->uuid('uuid')->nullable()->after('id');
            });

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unique('uuid');
            });
        }

        $missing = DB::table($table)
            ->where(function ($query) {
                $query->whereNull('uuid')->orWhere('uuid', '');
            })
            ->orderBy('id')
            ->get(['id']);

        foreach ($missing as $row) {
            DB::table($table)->where('id', $row->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE {$table} MODIFY uuid CHAR(36) NOT NULL");
        }
    }
};
