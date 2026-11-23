<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'twin_rooms')) {
                $table->unsignedSmallInteger('twin_rooms')->default(0)->after('double_rate');
            }
            if (! Schema::hasColumn('enquiries', 'twin_rate')) {
                $table->decimal('twin_rate', 10, 2)->default(0)->after('twin_rooms');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            foreach (['twin_rate', 'twin_rooms'] as $column) {
                if (Schema::hasColumn('enquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
