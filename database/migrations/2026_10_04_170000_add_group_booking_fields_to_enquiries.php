<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('enquiries', 'block_id')) {
                $table->string('block_id')->nullable()->unique()->after('ref');
            }
            if (! Schema::hasColumn('enquiries', 'agency_ref')) {
                $table->string('agency_ref')->nullable()->after('client');
            }
            if (! Schema::hasColumn('enquiries', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('agency_ref');
            }
            if (! Schema::hasColumn('enquiries', 'contract_sent_on')) {
                $table->date('contract_sent_on')->nullable()->after('cxl_policy');
            }
            if (! Schema::hasColumn('enquiries', 'contract_received_on')) {
                $table->date('contract_received_on')->nullable()->after('contract_sent_on');
            }
            if (! Schema::hasColumn('enquiries', 'saved_to_doc')) {
                $table->string('saved_to_doc')->nullable()->after('contract_received_on');
            }
            if (! Schema::hasColumn('enquiries', 'payment_term')) {
                $table->string('payment_term')->nullable()->after('saved_to_doc');
            }
            if (! Schema::hasColumn('enquiries', 'payment_due_date')) {
                $table->date('payment_due_date')->nullable()->after('payment_term');
            }
            if (! Schema::hasColumn('enquiries', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('payment_due_date');
            }
            if (! Schema::hasColumn('enquiries', 'cxl_due_date')) {
                $table->date('cxl_due_date')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('enquiries', 'cxl_date')) {
                $table->date('cxl_date')->nullable()->after('cxl_due_date');
            }
            if (! Schema::hasColumn('enquiries', 'commission')) {
                $table->decimal('commission', 12, 2)->nullable()->after('cxl_date');
            }
            if (! Schema::hasColumn('enquiries', 'total_rns')) {
                $table->unsignedInteger('total_rns')->nullable()->after('commission');
            }
            if (! Schema::hasColumn('enquiries', 'bb_revenue')) {
                $table->decimal('bb_revenue', 12, 2)->nullable()->after('total_revenue');
            }
            if (! Schema::hasColumn('enquiries', 'dinner_revenue')) {
                $table->decimal('dinner_revenue', 12, 2)->nullable()->after('bb_revenue');
            }
            if (! Schema::hasColumn('enquiries', 'nett_rev_ex_vat')) {
                $table->decimal('nett_rev_ex_vat', 12, 2)->nullable()->after('dinner_revenue');
            }
            if (! Schema::hasColumn('enquiries', 'booking_update')) {
                $table->string('booking_update')->nullable()->after('nett_rev_ex_vat');
            }
            if (! Schema::hasColumn('enquiries', 'rooming')) {
                $table->string('rooming')->nullable()->after('booking_update');
            }
            if (! Schema::hasColumn('enquiries', 'invoice_status')) {
                $table->string('invoice_status')->nullable()->after('rooming');
            }
            if (! Schema::hasColumn('enquiries', 'invoice_sent_on')) {
                $table->date('invoice_sent_on')->nullable()->after('invoice_status');
            }
            if (! Schema::hasColumn('enquiries', 'invoice_amount')) {
                $table->decimal('invoice_amount', 12, 2)->nullable()->after('invoice_sent_on');
            }
            if (! Schema::hasColumn('enquiries', 'commission_payable_status')) {
                $table->string('commission_payable_status')->nullable()->after('invoice_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $columns = [
                'block_id',
                'agency_ref',
                'contact_name',
                'contract_sent_on',
                'contract_received_on',
                'saved_to_doc',
                'payment_term',
                'payment_due_date',
                'payment_status',
                'cxl_due_date',
                'cxl_date',
                'commission',
                'total_rns',
                'bb_revenue',
                'dinner_revenue',
                'nett_rev_ex_vat',
                'booking_update',
                'rooming',
                'invoice_status',
                'invoice_sent_on',
                'invoice_amount',
                'commission_payable_status',
            ];

            $present = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('enquiries', $column)));

            if ($present !== []) {
                $table->dropColumn($present);
            }
        });
    }
};
