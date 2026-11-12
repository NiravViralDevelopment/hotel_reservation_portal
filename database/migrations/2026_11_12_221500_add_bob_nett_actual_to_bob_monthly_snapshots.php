<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bob_monthly_snapshots') || Schema::hasColumn('bob_monthly_snapshots', 'bob_nett_actual')) {
            return;
        }

        Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
            $table->decimal('bob_nett_actual', 14, 2)->nullable()->after('bob_nett');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bob_monthly_snapshots') || ! Schema::hasColumn('bob_monthly_snapshots', 'bob_nett_actual')) {
            return;
        }

        Schema::table('bob_monthly_snapshots', function (Blueprint $table) {
            $table->dropColumn('bob_nett_actual');
        });
    }
};
