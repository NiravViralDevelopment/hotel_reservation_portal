<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('block_id')->unique();
            $table->foreignId('enquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('travel_agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('group_name');
            $table->string('client')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->date('arrival');
            $table->date('departure');
            $table->string('arrival_day')->nullable();
            $table->unsignedSmallInteger('nights')->default(1);
            $table->string('status', 30)->default('Provisional');
            $table->date('contract_sent')->nullable();
            $table->date('contract_recd')->nullable();
            $table->string('saved_doc')->nullable();
            $table->string('payment_term')->nullable();
            $table->date('due_date')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_status_display', 30)->nullable();
            $table->string('cxl_policy')->nullable();
            $table->date('cxl_due_date')->nullable();
            $table->date('cxl_date')->nullable();
            $table->decimal('commission', 8, 2)->nullable();
            $table->unsignedInteger('single_rns')->default(0);
            $table->decimal('single_rate', 10, 2)->default(0);
            $table->unsignedInteger('double_rns')->default(0);
            $table->decimal('double_rate', 10, 2)->default(0);
            $table->unsignedInteger('triple_rns')->default(0);
            $table->decimal('triple_rate', 10, 2)->default(0);
            $table->unsignedInteger('total_rns')->default(0);
            $table->unsignedSmallInteger('rooms')->default(0);
            $table->unsignedSmallInteger('pax')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('bb_revenue', 12, 2)->default(0);
            $table->decimal('dinner_revenue', 12, 2)->default(0);
            $table->decimal('nett_rev', 12, 2)->default(0);
            $table->string('meal_plan', 10)->nullable();
            $table->string('rooming_status')->nullable();
            $table->string('invoice_status')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount', 12, 2)->nullable();
            $table->decimal('commission_payable', 12, 2)->nullable();
            $table->string('opera_cross_check')->nullable();
            $table->text('update_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->decimal('revenue_lost', 12, 2)->nullable();
            $table->decimal('city_tax', 12, 2)->nullable();
            $table->timestamps();

            $table->index(['arrival', 'departure']);
            $table->index('status');
            $table->index('payment_status_display');
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreign('converted_booking_id')->references('id')->on('group_bookings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropForeign(['converted_booking_id']);
        });
        Schema::dropIfExists('group_bookings');
    }
};
