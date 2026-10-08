<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('booking_contract_disk')->nullable()->after('commission_payable_status');
            $table->string('booking_contract_path')->nullable()->after('booking_contract_disk');
            $table->string('booking_contract_original_name')->nullable()->after('booking_contract_path');
            $table->string('booking_contract_mime_type')->nullable()->after('booking_contract_original_name');
            $table->unsignedBigInteger('booking_contract_size')->default(0)->after('booking_contract_mime_type');
            $table->foreignId('booking_contract_hotel_id')->nullable()->after('booking_contract_size')->constrained('hotels')->nullOnDelete();
            $table->timestamp('booking_contract_saved_at')->nullable()->after('booking_contract_hotel_id');
            $table->text('booking_contract_notes')->nullable()->after('booking_contract_saved_at');
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('booking_contract_hotel_id');
            $table->dropColumn([
                'booking_contract_disk',
                'booking_contract_path',
                'booking_contract_original_name',
                'booking_contract_mime_type',
                'booking_contract_size',
                'booking_contract_saved_at',
                'booking_contract_notes',
            ]);
        });
    }
};
