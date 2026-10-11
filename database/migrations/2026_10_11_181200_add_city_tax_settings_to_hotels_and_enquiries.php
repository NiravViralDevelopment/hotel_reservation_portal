<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (! Schema::hasColumn('hotels', 'city_tax_basis')) {
                $table->string('city_tax_basis', 20)->nullable()->after('notes');
            }
            if (! Schema::hasColumn('hotels', 'city_tax_amount')) {
                $table->decimal('city_tax_amount', 12, 2)->nullable()->after('city_tax_basis');
            }
            if (! Schema::hasColumn('hotels', 'city_tax_percentage')) {
                $table->decimal('city_tax_percentage', 8, 2)->nullable()->after('city_tax_amount');
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'city_tax')) {
                $table->decimal('city_tax', 12, 2)->nullable()->after('nett_rev_ex_vat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (Schema::hasColumn('enquiries', 'city_tax')) {
                $table->dropColumn('city_tax');
            }
        });

        Schema::table('hotels', function (Blueprint $table) {
            foreach (['city_tax_percentage', 'city_tax_amount', 'city_tax_basis'] as $column) {
                if (Schema::hasColumn('hotels', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
