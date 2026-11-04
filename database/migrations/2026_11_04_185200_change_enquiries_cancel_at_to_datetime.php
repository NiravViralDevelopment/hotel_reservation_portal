<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'cancel_at')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dateTime('cancel_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'cancel_at')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->date('cancel_at')->nullable()->change();
        });
    }
};
