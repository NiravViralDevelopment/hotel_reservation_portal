<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        $indexes = collect(Schema::getIndexes('enquiries'));
        $hasRefUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['ref'];
        });
        $hasPairUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['group_name', 'ref'];
        });

        if ($hasRefUnique) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->dropUnique(['ref']);
            });
        }

        if (! $hasPairUnique) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->unique(['group_name', 'ref'], 'enquiries_group_name_ref_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        $indexes = collect(Schema::getIndexes('enquiries'));
        $hasPairUnique = $indexes->contains(function (array $index) {
            return ($index['name'] ?? null) === 'enquiries_group_name_ref_unique'
                || (($index['unique'] ?? false) && ($index['columns'] ?? []) === ['group_name', 'ref']);
        });
        $hasRefUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['ref'];
        });

        if ($hasPairUnique) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->dropUnique('enquiries_group_name_ref_unique');
            });
        }

        if (! $hasRefUnique) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->unique('ref');
            });
        }
    }
};
