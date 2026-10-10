<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            }
        });

        $creators = DB::table('audit_logs')
            ->select(['auditable_id', 'user_id'])
            ->where('action', 'created')
            ->where('module', 'enquiries')
            ->where('auditable_type', 'App\\Models\\Enquiry')
            ->whereNotNull('auditable_id')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->get()
            ->unique('auditable_id');

        foreach ($creators as $row) {
            DB::table('enquiries')
                ->where('id', $row->auditable_id)
                ->whereNull('created_by')
                ->update(['created_by' => $row->user_id]);
        }
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (Schema::hasColumn('enquiries', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};
