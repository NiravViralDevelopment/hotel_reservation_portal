<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'payment_term_days')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->unsignedSmallInteger('payment_term_days')->nullable()->after('payment_term');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'payment_term_days')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('payment_term_days');
        });
    }
};
