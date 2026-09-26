<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bob_monthly_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('label')->nullable();
            $table->decimal('bob_current', 14, 2)->default(0);
            $table->decimal('bob_previous', 14, 2)->default(0);
            $table->decimal('bob_variance', 14, 2)->default(0);
            $table->decimal('stly_bob', 14, 2)->default(0);
            $table->decimal('adr_current', 10, 2)->default(0);
            $table->decimal('adr_previous', 10, 2)->default(0);
            $table->decimal('adr_variance', 10, 2)->default(0);
            $table->decimal('adr_stly', 10, 2)->default(0);
            $table->unsignedInteger('room_nights_current')->default(0);
            $table->unsignedInteger('room_nights_previous')->default(0);
            $table->unsignedInteger('stly_room_nights')->default(0);
            $table->decimal('breakfast_revenue', 14, 2)->default(0);
            $table->decimal('dinner_revenue', 14, 2)->default(0);
            $table->unsignedInteger('dinner_covers')->default(0);
            $table->decimal('stly_breakfast_revenue', 14, 2)->default(0);
            $table->timestamps();
            $table->unique(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bob_monthly_snapshots');
    }
};
