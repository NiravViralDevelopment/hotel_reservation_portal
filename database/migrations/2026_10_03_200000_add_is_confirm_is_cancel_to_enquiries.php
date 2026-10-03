<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'is_confirm')) {
                $table->boolean('is_confirm')->default(false)->after('status');
            }
            if (! Schema::hasColumn('enquiries', 'is_cancel')) {
                $table->boolean('is_cancel')->default(false)->after('is_confirm');
            }
        });

        if (Schema::hasColumn('enquiries', 'is_confirm')) {
            DB::table('enquiries')
                ->whereRaw('LOWER(status) = ?', ['confirmed'])
                ->update(['is_confirm' => 1, 'is_cancel' => 0]);

            DB::table('enquiries')
                ->whereRaw('LOWER(status) = ?', ['cancelled'])
                ->update(['is_cancel' => 1, 'is_confirm' => 0]);
        }
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (Schema::hasColumn('enquiries', 'is_cancel')) {
                $table->dropColumn('is_cancel');
            }
            if (Schema::hasColumn('enquiries', 'is_confirm')) {
                $table->dropColumn('is_confirm');
            }
        });
    }
};
