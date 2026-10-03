<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hotels')) {
            return;
        }

        $indexes = collect(Schema::getIndexes('hotels'));
        $hasCodeUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['code'];
        });
        $hasCodeNameUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['code', 'name'];
        });

        if ($hasCodeUnique) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->dropUnique(['code']);
            });
        }

        if (! $hasCodeNameUnique) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->unique(['code', 'name'], 'hotels_code_name_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('hotels')) {
            return;
        }

        $indexes = collect(Schema::getIndexes('hotels'));
        $hasCodeNameUnique = $indexes->contains(function (array $index) {
            return ($index['name'] ?? null) === 'hotels_code_name_unique'
                || (($index['unique'] ?? false) && ($index['columns'] ?? []) === ['code', 'name']);
        });
        $hasCodeUnique = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['code'];
        });

        if ($hasCodeNameUnique) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->dropUnique('hotels_code_name_unique');
            });
        }

        if (! $hasCodeUnique) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->unique('code');
            });
        }
    }
};
