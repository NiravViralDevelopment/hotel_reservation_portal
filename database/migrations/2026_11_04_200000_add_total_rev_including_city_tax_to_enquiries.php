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
            if (! Schema::hasColumn('enquiries', 'total_rev_including_city_tax')) {
                $table->decimal('total_rev_including_city_tax', 12, 2)->nullable()->after('city_tax_including_vat');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'total_rev_including_city_tax')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('total_rev_including_city_tax');
        });
    }
};
