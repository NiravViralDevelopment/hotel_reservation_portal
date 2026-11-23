<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('enquiries', 'contact_name')) {
            DB::statement('ALTER TABLE `enquiries` CHANGE `contact_name` `contact_number` VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');

            return;
        }

        if (Schema::hasColumn('enquiries', 'contact_number')) {
            DB::statement('ALTER TABLE `enquiries` MODIFY `contact_number` VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('enquiries', 'contact_number') || Schema::hasColumn('enquiries', 'contact_name')) {
            return;
        }

        DB::statement('ALTER TABLE `enquiries` CHANGE `contact_number` `contact_name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
    }
};
