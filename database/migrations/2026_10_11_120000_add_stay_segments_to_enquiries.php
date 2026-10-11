<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'stay_segments')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->json('stay_segments')->nullable()->after('daily_room_rates');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'stay_segments')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('stay_segments');
        });
    }
};
