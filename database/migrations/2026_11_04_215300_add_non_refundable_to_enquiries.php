<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'non_refundable')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->boolean('non_refundable')->nullable()->after('cxl_policy');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'non_refundable')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('non_refundable');
        });
    }
};
