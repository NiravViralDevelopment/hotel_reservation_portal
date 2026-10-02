<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Optional: allow NULL country on travel agencies (app still defaults to United Kingdom).
        if (Schema::hasTable('travel_agencies') && Schema::hasColumn('travel_agencies', 'country')) {
            DB::statement("ALTER TABLE travel_agencies MODIFY country VARCHAR(191) NULL DEFAULT 'United Kingdom'");
        }

        if (Schema::hasTable('enquiries')) {
            $enquiryColumns = [
                'response_date' => fn (Blueprint $table) => $table->date('response_date')->nullable()->after('enquiry_date'),
                'client_response' => fn (Blueprint $table) => $table->text('client_response')->nullable()->after('response_date'),
                'check_in' => fn (Blueprint $table) => $table->date('check_in')->nullable()->after('day'),
                'check_in_day' => fn (Blueprint $table) => $table->string('check_in_day', 20)->nullable()->after('check_in'),
                'check_out' => fn (Blueprint $table) => $table->date('check_out')->nullable()->after('check_in_day'),
                'has_tax' => fn (Blueprint $table) => $table->boolean('has_tax')->default(false)->after('total_revenue'),
                'tax_percentage' => fn (Blueprint $table) => $table->decimal('tax_percentage', 5, 2)->nullable()->after('has_tax'),
                'tax_revenue' => fn (Blueprint $table) => $table->decimal('tax_revenue', 12, 2)->default(0)->after('tax_percentage'),
            ];

            foreach ($enquiryColumns as $column => $definition) {
                if (! Schema::hasColumn('enquiries', $column)) {
                    Schema::table('enquiries', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }

            // Status Master stores free-text titles; widen enquiry status off the old ENUM.
            DB::statement("ALTER TABLE enquiries MODIFY status VARCHAR(191) NOT NULL DEFAULT 'new'");
        }

        if (! Schema::hasTable('status_masters')) {
            Schema::create('status_masters', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        $now = now();
        foreach (['new', 'follow_up', 'quoted', 'confirmed', 'lost', 'cancelled'] as $title) {
            $exists = DB::table('status_masters')->where('title', $title)->exists();
            if ($exists) {
                DB::table('status_masters')->where('title', $title)->update([
                    'status' => 'active',
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('status_masters')->insert([
                    'title' => $title,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (! Schema::hasTable('enquiry_responses')) {
            Schema::create('enquiry_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->date('response_date')->nullable();
                $table->text('client_response');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('permissions')) {
            foreach (['statuses.view', 'statuses.create', 'statuses.edit', 'statuses.delete'] as $name) {
                $exists = DB::table('permissions')
                    ->where('name', $name)
                    ->where('guard_name', 'web')
                    ->exists();

                if ($exists) {
                    DB::table('permissions')
                        ->where('name', $name)
                        ->where('guard_name', 'web')
                        ->update(['updated_at' => $now]);
                } else {
                    DB::table('permissions')->insert([
                        'name' => $name,
                        'guard_name' => 'web',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            $adminRoleId = DB::table('roles')
                ->where('name', 'Administrator')
                ->where('guard_name', 'web')
                ->value('id');

            if ($adminRoleId && Schema::hasTable('role_has_permissions')) {
                $permissionIds = DB::table('permissions')
                    ->where('guard_name', 'web')
                    ->whereIn('name', ['statuses.view', 'statuses.create', 'statuses.edit', 'statuses.delete'])
                    ->pluck('id');

                foreach ($permissionIds as $permissionId) {
                    $exists = DB::table('role_has_permissions')
                        ->where('permission_id', $permissionId)
                        ->where('role_id', $adminRoleId)
                        ->exists();

                    if (! $exists) {
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $permissionId,
                            'role_id' => $adminRoleId,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('role_has_permissions') && Schema::hasTable('permissions') && Schema::hasTable('roles')) {
            $adminRoleId = DB::table('roles')
                ->where('name', 'Administrator')
                ->where('guard_name', 'web')
                ->value('id');

            $permissionIds = DB::table('permissions')
                ->where('guard_name', 'web')
                ->whereIn('name', ['statuses.view', 'statuses.create', 'statuses.edit', 'statuses.delete'])
                ->pluck('id');

            if ($adminRoleId && $permissionIds->isNotEmpty()) {
                DB::table('role_has_permissions')
                    ->where('role_id', $adminRoleId)
                    ->whereIn('permission_id', $permissionIds)
                    ->delete();
            }

            DB::table('permissions')
                ->where('guard_name', 'web')
                ->whereIn('name', ['statuses.view', 'statuses.create', 'statuses.edit', 'statuses.delete'])
                ->delete();
        }

        Schema::dropIfExists('enquiry_responses');
        Schema::dropIfExists('status_masters');

        if (Schema::hasTable('enquiries')) {
            $drop = [];
            foreach (['tax_revenue', 'tax_percentage', 'has_tax', 'check_out', 'check_in_day', 'check_in', 'client_response', 'response_date'] as $column) {
                if (Schema::hasColumn('enquiries', $column)) {
                    $drop[] = $column;
                }
            }

            if ($drop !== []) {
                Schema::table('enquiries', function (Blueprint $table) use ($drop) {
                    $table->dropColumn($drop);
                });
            }

            DB::statement("ALTER TABLE enquiries MODIFY status ENUM('new','follow_up','quoted','confirmed','lost','cancelled') NOT NULL DEFAULT 'new'");
        }

        if (Schema::hasTable('travel_agencies') && Schema::hasColumn('travel_agencies', 'country')) {
            DB::table('travel_agencies')->whereNull('country')->update(['country' => 'United Kingdom']);
            DB::statement("ALTER TABLE travel_agencies MODIFY country VARCHAR(191) NOT NULL DEFAULT 'United Kingdom'");
        }
    }
};
