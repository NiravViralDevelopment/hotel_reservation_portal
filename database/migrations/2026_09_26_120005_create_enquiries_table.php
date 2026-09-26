<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('ref')->unique();
            $table->unsignedSmallInteger('year')->nullable();
            $table->date('enquiry_date')->nullable();
            $table->string('day')->nullable();
            $table->unsignedSmallInteger('nights')->default(1);
            $table->string('group_name');
            $table->foreignId('travel_agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('rooms_per_night')->default(0);
            $table->unsignedSmallInteger('single_rooms')->default(0);
            $table->decimal('single_rate', 10, 2)->default(0);
            $table->unsignedSmallInteger('double_rooms')->default(0);
            $table->decimal('double_rate', 10, 2)->default(0);
            $table->unsignedSmallInteger('triple_rooms')->default(0);
            $table->decimal('triple_rate', 10, 2)->default(0);
            $table->string('basis', 10)->nullable();
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->string('cxl_policy')->nullable();
            $table->date('option_date')->nullable();
            $table->string('email')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['new', 'follow_up', 'quoted', 'confirmed', 'lost', 'cancelled'])->default('new');
            $table->foreignId('converted_booking_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
