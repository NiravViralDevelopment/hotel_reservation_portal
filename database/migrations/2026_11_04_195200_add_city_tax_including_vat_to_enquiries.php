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
            if (! Schema::hasColumn('enquiries', 'city_tax_including_vat')) {
                $table->decimal('city_tax_including_vat', 12, 2)->nullable()->after('city_tax');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'city_tax_including_vat')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('city_tax_including_vat');
        });
    }
};
