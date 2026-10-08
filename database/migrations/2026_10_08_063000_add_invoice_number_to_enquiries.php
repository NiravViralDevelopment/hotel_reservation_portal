<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'invoice_number')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('invoice_status');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'invoice_number')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });
    }
};
