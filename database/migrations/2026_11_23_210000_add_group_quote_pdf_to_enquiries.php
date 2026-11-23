<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries') || Schema::hasColumn('enquiries', 'group_quote_pdf_path')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('group_quote_pdf_disk')->nullable()->after('booking_contract_html');
            $table->string('group_quote_pdf_path')->nullable()->after('group_quote_pdf_disk');
            $table->string('group_quote_pdf_original_name')->nullable()->after('group_quote_pdf_path');
            $table->string('group_quote_pdf_mime_type')->nullable()->after('group_quote_pdf_original_name');
            $table->unsignedBigInteger('group_quote_pdf_size')->default(0)->after('group_quote_pdf_mime_type');
            $table->timestamp('group_quote_pdf_generated_at')->nullable()->after('group_quote_pdf_size');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries') || ! Schema::hasColumn('enquiries', 'group_quote_pdf_path')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn([
                'group_quote_pdf_disk',
                'group_quote_pdf_path',
                'group_quote_pdf_original_name',
                'group_quote_pdf_mime_type',
                'group_quote_pdf_size',
                'group_quote_pdf_generated_at',
            ]);
        });
    }
};
