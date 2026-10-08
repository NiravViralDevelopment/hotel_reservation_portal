<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('document_disk')->nullable()->after('notes');
            $table->string('document_path')->nullable()->after('document_disk');
            $table->string('document_original_name')->nullable()->after('document_path');
            $table->string('document_mime_type')->nullable()->after('document_original_name');
            $table->unsignedBigInteger('document_size')->default(0)->after('document_mime_type');
        });

        Schema::dropIfExists('hotel_documents');
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'document_disk',
                'document_path',
                'document_original_name',
                'document_mime_type',
                'document_size',
            ]);
        });
    }
};
