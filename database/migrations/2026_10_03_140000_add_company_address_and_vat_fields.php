<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('vat_number', 50)->nullable()->after('reg_number');
            $table->text('registered_address')->nullable()->after('address');
            $table->text('trading_address')->nullable()->after('registered_address');
        });

        if (Schema::hasColumn('companies', 'address')) {
            DB::table('companies')
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->update([
                    'registered_address' => DB::raw('address'),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['vat_number', 'registered_address', 'trading_address']);
        });
    }
};
