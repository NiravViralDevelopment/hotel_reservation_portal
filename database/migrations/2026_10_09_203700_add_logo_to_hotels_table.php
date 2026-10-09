<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('logo_disk')->nullable()->after('document_size');
            $table->string('logo_path')->nullable()->after('logo_disk');
            $table->string('logo_original_name')->nullable()->after('logo_path');
            $table->string('logo_mime_type')->nullable()->after('logo_original_name');
            $table->unsignedBigInteger('logo_size')->default(0)->after('logo_mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'logo_disk',
                'logo_path',
                'logo_original_name',
                'logo_mime_type',
                'logo_size',
            ]);
        });
    }
};
