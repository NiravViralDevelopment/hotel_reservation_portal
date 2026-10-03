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

        Schema::table('enquiries', function (Blueprint $table) {
            $columns = [
                'days' => fn (Blueprint $t) => $t->unsignedSmallInteger('days')->nullable()->after('nights'),
                'breakdown' => fn (Blueprint $t) => $t->text('breakdown')->nullable()->after('days'),
                'client' => fn (Blueprint $t) => $t->string('client')->nullable()->after('group_name'),
                'mobile' => fn (Blueprint $t) => $t->string('mobile', 30)->nullable()->after('email'),
                'source' => fn (Blueprint $t) => $t->string('source')->nullable()->after('mobile'),
                'service_person' => fn (Blueprint $t) => $t->string('service_person')->nullable()->after('source'),
                'subject' => fn (Blueprint $t) => $t->string('subject')->nullable()->after('service_person'),
                'booking_msg' => fn (Blueprint $t) => $t->text('booking_msg')->nullable()->after('subject'),
                'adults_price' => fn (Blueprint $t) => $t->decimal('adults_price', 12, 2)->default(0)->after('booking_msg'),
                'child_price' => fn (Blueprint $t) => $t->decimal('child_price', 12, 2)->default(0)->after('adults_price'),
                'adults_extra' => fn (Blueprint $t) => $t->decimal('adults_extra', 12, 2)->default(0)->after('child_price'),
                'child_extra' => fn (Blueprint $t) => $t->decimal('child_extra', 12, 2)->default(0)->after('adults_extra'),
                'total_pax' => fn (Blueprint $t) => $t->unsignedInteger('total_pax')->default(0)->after('child_extra'),
                'agent_price' => fn (Blueprint $t) => $t->decimal('agent_price', 12, 2)->default(0)->after('total_pax'),
                'our_cost' => fn (Blueprint $t) => $t->decimal('our_cost', 12, 2)->default(0)->after('agent_price'),
                'package_price' => fn (Blueprint $t) => $t->decimal('package_price', 12, 2)->default(0)->after('our_cost'),
                'gst_policy' => fn (Blueprint $t) => $t->string('gst_policy')->nullable()->after('package_price'),
                'total_price' => fn (Blueprint $t) => $t->decimal('total_price', 12, 2)->default(0)->after('gst_policy'),
                'net_price' => fn (Blueprint $t) => $t->decimal('net_price', 12, 2)->default(0)->after('total_price'),
                'advance' => fn (Blueprint $t) => $t->decimal('advance', 12, 2)->default(0)->after('net_price'),
                'remaining' => fn (Blueprint $t) => $t->decimal('remaining', 12, 2)->default(0)->after('advance'),
                'agent_comm_percent' => fn (Blueprint $t) => $t->decimal('agent_comm_percent', 5, 2)->default(0)->after('remaining'),
                'agent_comm_amount' => fn (Blueprint $t) => $t->decimal('agent_comm_amount', 12, 2)->default(0)->after('agent_comm_percent'),
                'payable_to_agent' => fn (Blueprint $t) => $t->decimal('payable_to_agent', 12, 2)->default(0)->after('agent_comm_amount'),
                'service_total' => fn (Blueprint $t) => $t->decimal('service_total', 12, 2)->default(0)->after('payable_to_agent'),
                'total_tax' => fn (Blueprint $t) => $t->decimal('total_tax', 12, 2)->default(0)->after('service_total'),
                'grand_total' => fn (Blueprint $t) => $t->decimal('grand_total', 12, 2)->default(0)->after('total_tax'),
            ];

            foreach ($columns as $name => $definition) {
                if (! Schema::hasColumn('enquiries', $name)) {
                    $definition($table);
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enquiries')) {
            return;
        }

        $drop = [
            'days', 'breakdown', 'client', 'mobile', 'source', 'service_person', 'subject', 'booking_msg',
            'adults_price', 'child_price', 'adults_extra', 'child_extra', 'total_pax',
            'agent_price', 'our_cost', 'package_price', 'gst_policy',
            'total_price', 'net_price', 'advance', 'remaining',
            'agent_comm_percent', 'agent_comm_amount', 'payable_to_agent',
            'service_total', 'total_tax', 'grand_total',
        ];

        Schema::table('enquiries', function (Blueprint $table) use ($drop) {
            foreach ($drop as $column) {
                if (Schema::hasColumn('enquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
