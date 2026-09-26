<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE group_booking_daily_rows MODIFY update_note TEXT NULL');
        DB::statement('ALTER TABLE group_bookings MODIFY update_notes TEXT NULL');
        DB::statement('ALTER TABLE group_bookings MODIFY cancellation_reason TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE group_booking_daily_rows MODIFY update_note VARCHAR(191) NULL');
        DB::statement('ALTER TABLE group_bookings MODIFY update_notes TEXT NULL');
        DB::statement('ALTER TABLE group_bookings MODIFY cancellation_reason VARCHAR(191) NULL');
    }
};
