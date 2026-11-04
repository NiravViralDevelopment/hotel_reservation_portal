<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'foc_rooms')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->unsignedTinyInteger('foc_rooms')->nullable()->after('basis');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'foc_rooms')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('foc_rooms');
        });
    }
};
