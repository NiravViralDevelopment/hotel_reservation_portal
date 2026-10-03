<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->unique('uuid');
            });
        }

        $missing = DB::table('users')
            ->where(function ($query) {
                $query->whereNull('uuid')->orWhere('uuid', '');
            })
            ->orderBy('id')
            ->get(['id']);

        foreach ($missing as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY uuid CHAR(36) NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
