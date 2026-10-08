<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'booking_contract_html')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->longText('booking_contract_html')->nullable()->after('booking_contract_notes');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'booking_contract_html')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('booking_contract_html');
        });
    }
};
