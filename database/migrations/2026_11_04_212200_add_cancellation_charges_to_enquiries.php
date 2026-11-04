<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'cancellation_charges')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->boolean('cancellation_charges')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'cancellation_charges')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('cancellation_charges');
        });
    }
};
