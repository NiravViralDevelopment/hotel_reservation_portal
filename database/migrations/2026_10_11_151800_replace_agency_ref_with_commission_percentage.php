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
            if (Schema::hasColumn('enquiries', 'agency_ref')) {
                $table->dropColumn('agency_ref');
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'commission_percentage')) {
                $table->decimal('commission_percentage', 5, 2)->nullable()->after('has_commission');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            if (Schema::hasColumn('enquiries', 'commission_percentage')) {
                $table->dropColumn('commission_percentage');
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'agency_ref')) {
                $table->string('agency_ref')->nullable()->after('client');
            }
        });
    }
};
