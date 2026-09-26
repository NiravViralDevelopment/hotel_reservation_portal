<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_booking_daily_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_booking_id')->constrained()->cascadeOnDelete();
            $table->string('sheet')->nullable();
            $table->date('arrival')->nullable();
            $table->date('departure')->nullable();
            $table->unsignedSmallInteger('nights')->default(0);
            $table->unsignedInteger('total_rns')->default(0);
            $table->decimal('total_rev', 12, 2)->default(0);
            $table->string('meal_plan', 10)->nullable();
            $table->string('update_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_booking_daily_rows');
    }
};
