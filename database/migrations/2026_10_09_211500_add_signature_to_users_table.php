<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('signature_disk')->nullable()->after('status');
            $table->string('signature_path')->nullable()->after('signature_disk');
            $table->string('signature_original_name')->nullable()->after('signature_path');
            $table->string('signature_mime_type')->nullable()->after('signature_original_name');
            $table->unsignedBigInteger('signature_size')->default(0)->after('signature_mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'signature_disk',
                'signature_path',
                'signature_original_name',
                'signature_mime_type',
                'signature_size',
            ]);
        });
    }
};
