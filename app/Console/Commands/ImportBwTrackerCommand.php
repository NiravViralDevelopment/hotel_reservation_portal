<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Enums\EnquiryStatus;
use App\Enums\PaymentDisplayStatus;
use App\Models\BobMonthlySnapshot;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Enquiry;
use App\Models\GroupBooking;
use App\Models\GroupBookingDailyRow;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportBwTrackerCommand extends Command
{
    protected $signature = 'import:bw-tracker
                            {--file=imports/bw-2025-data.json : Path relative to storage/app}
                            {--fresh : Truncate booking-related tables before import}';

    protected $description = 'Import BW Group Tracker Excel JSON into HGBMS modules';

    public function handle(): int
    {
        $relative = $this->option('file');
        $path = storage_path('app/'.$relative);

        if (! is_file($path)) {
            $this->error("File not found: {$path}");
            $this->line('Run first: node html/scripts/export-bw-2025.cjs');

            return self::FAILURE;
        }

        $payload = json_decode(file_get_contents($path), true);
        if (! is_array($payload)) {
            $this->error('Invalid JSON payload.');

            return self::FAILURE;
        }

        $admin = User::query()->where('email', 'admin@hotelgroup.co.uk')->first()
            ?? User::query()->orderBy('id')->first();

        if ($this->option('fresh')) {
            $this->warn('Clearing existing tracker data...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            GroupBookingDailyRow::query()->truncate();
            Enquiry::query()->truncate();
            GroupBooking::query()->truncate();
            Contact::query()->truncate();
            TravelAgency::query()->truncate();
            BobMonthlySnapshot::query()->where('year', 2025)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $meta = $payload['meta'] ?? [];
        $this->info('Source: '.($meta['source'] ?? 'unknown'));

        $company = Company::query()->updateOrCreate(
            ['name' => 'BW Harbour Group'],
            [
                'city' => 'Brighton',
                'country' => 'United Kingdom',
                'status' => 'active',
                'notes' => 'Imported from BW Group Tracker 2025',
            ]
        );

        $hotel = Hotel::query()->updateOrCreate(
            ['code' => $meta['hotelCode'] ?? 'BWH01'],
            [
                'company_id' => $company->id,
                'name' => $meta['hotelName'] ?? 'Brighton Harbour Hotel',
                'city' => 'Brighton',
                'country' => 'United Kingdom',
                'rooms' => 200,
                'manager_name' => $admin?->name,
                'manager_user_id' => $admin?->id,
                'status' => 'active',
                'notes' => 'Primary hotel for BW Group Tracker imports',
            ]
        );

        $agencyCache = [];
        $resolveAgency = function (?string $clientName) use (&$agencyCache): ?TravelAgency {
            $clientName = trim((string) $clientName);
            if ($clientName === '') {
                return null;
            }
            $key = Str::lower($clientName);
            if (isset($agencyCache[$key])) {
                return $agencyCache[$key];
            }

            $code = Str::upper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $clientName) ?: 'AGY', 0, 12));
            $base = $code;
            $i = 1;
            while (TravelAgency::query()->where('code', $code)->where('name', '!=', $clientName)->exists()) {
                $code = Str::upper(Str::substr($base, 0, 10)).$i;
                $i++;
            }

            $agency = TravelAgency::query()->updateOrCreate(
                ['name' => $clientName],
                [
                    'code' => $code,
                    'country' => 'United Kingdom',
                    'status' => 'active',
                ]
            );

            return $agencyCache[$key] = $agency;
        };

        $this->info('Importing contacts...');
        $contactCount = 0;
        foreach ($payload['contacts'] ?? [] as $row) {
            $agency = $resolveAgency($row['company'] ?? null);
            $email = trim((string) ($row['email'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $query = Contact::query()->where('name', $name);
            if ($agency) {
                $query->where('travel_agency_id', $agency->id);
            } elseif ($email !== '') {
                $query->where('email', $email);
            }

            $contact = $query->first();
            $attrs = [
                'company_id' => $company->id,
                'travel_agency_id' => $agency?->id,
                'name' => $name,
                'email' => $email !== '' ? $email : null,
                'phone' => $row['phone'] ?: null,
                'position' => $row['position'] ?: null,
                'country' => 'United Kingdom',
                'notes' => $row['address'] ?: null,
            ];

            if ($contact) {
                $contact->update($attrs);
            } else {
                Contact::query()->create($attrs);
            }
            $contactCount++;
        }

        $importBooking = function (array $row, bool $cancelled) use ($hotel, $company, $admin, $resolveAgency): void {
            $blockId = trim((string) ($row['blockId'] ?? ''));
            if ($blockId === '') {
                return;
            }

            $agency = $resolveAgency($row['client'] ?? null);
            $status = $this->normalizeBookingStatus($row['status'] ?? null, $cancelled);
            $paymentDisplay = $this->normalizePaymentDisplay($row['paymentStatusDisplay'] ?? null);

            $booking = GroupBooking::query()->updateOrCreate(
                ['block_id' => $blockId],
                [
                    'company_id' => $company->id,
                    'hotel_id' => $hotel->id,
                    'travel_agency_id' => $agency?->id,
                    'created_by' => $admin?->id,
                    'group_name' => $row['client'] ?: ($row['agencyRef'] ?: $blockId),
                    'client' => $row['client'] ?: null,
                    'agency_name' => $agency?->name,
                    'contact_name' => $row['contact'] ?: null,
                    'email' => $row['email'] ?: null,
                    'arrival' => $row['arrival'],
                    'departure' => $row['departure'] ?: $row['arrival'],
                    'arrival_day' => $row['arrivalDay'] ?: null,
                    'nights' => max(1, (int) ($row['nights'] ?? 1)),
                    'status' => $status,
                    'contract_sent' => $row['contractSent'] ?: null,
                    'contract_recd' => $row['contractRecd'] ?: null,
                    'saved_doc' => $row['savedDoc'] ?: null,
                    'payment_term' => $row['paymentTerm'] ?: null,
                    'due_date' => $row['dueDate'] ?: null,
                    'payment_status' => $row['paymentStatus'] ?: null,
                    'payment_status_display' => $paymentDisplay,
                    'cxl_policy' => $row['cxlPolicy'] ?: null,
                    'cxl_due_date' => $row['cxlDueDate'] ?: null,
                    'cxl_date' => $row['cxlDate'] ?: null,
                    'commission' => is_numeric($row['commission'] ?? null) ? $row['commission'] : null,
                    'single_rns' => (int) ($row['singleRNs'] ?? 0),
                    'single_rate' => (float) ($row['singleRate'] ?? 0),
                    'double_rns' => (int) ($row['doubleRNs'] ?? 0),
                    'double_rate' => (float) ($row['doubleRate'] ?? 0),
                    'triple_rns' => (int) ($row['tripleRNs'] ?? 0),
                    'triple_rate' => (float) ($row['tripleRate'] ?? 0),
                    'total_rns' => (int) ($row['totalRNs'] ?? 0),
                    'rooms' => (int) ($row['rooms'] ?? 0),
                    'pax' => (int) ($row['pax'] ?? 0),
                    'revenue' => (float) ($row['revenue'] ?? 0),
                    'bb_revenue' => (float) ($row['bbRevenue'] ?? 0),
                    'dinner_revenue' => (float) ($row['dinnerRevenue'] ?? 0),
                    'nett_rev' => (float) ($row['nettRev'] ?? 0),
                    'meal_plan' => $this->normalizeMealPlan($row['mealPlan'] ?? null),
                    'rooming_status' => $row['roomingStatus'] ?: null,
                    'invoice_status' => $row['invoiceStatus'] ?: null,
                    'invoice_date' => $row['invoiceDate'] ?: null,
                    'invoice_amount' => (float) ($row['invoiceAmount'] ?? 0) ?: null,
                    'commission_payable' => (float) ($row['commissionPayable'] ?? 0) ?: null,
                    'opera_cross_check' => $row['operaCrossCheck'] ?: null,
                    'update_notes' => trim(($row['updateNotes'] ?? '').($row['agencyRef'] ? ' | Agency ref: '.$row['agencyRef'] : '')),
                    'cancelled_at' => $cancelled ? ($row['cxlDate'] ?: now()) : null,
                    'cancellation_reason' => $cancelled ? ($row['cancellationReason'] ?? 'Cancelled') : null,
                    'revenue_lost' => $cancelled ? (float) ($row['revenueLost'] ?? $row['revenue'] ?? 0) : null,
                    'city_tax' => $cancelled ? (float) ($row['cityTax'] ?? 0) : null,
                ]
            );

            if (! empty($row['sheet']) && $row['arrival']) {
                GroupBookingDailyRow::query()->updateOrCreate(
                    [
                        'group_booking_id' => $booking->id,
                        'sheet' => $row['sheet'],
                        'arrival' => $row['arrival'],
                    ],
                    [
                        'departure' => $row['departure'] ?: null,
                        'nights' => (int) ($row['nights'] ?? 0),
                        'total_rns' => (int) ($row['totalRNs'] ?? 0),
                        'total_rev' => (float) ($row['revenue'] ?? 0),
                        'meal_plan' => $this->normalizeMealPlan($row['mealPlan'] ?? null),
                        'update_note' => $row['updateNotes'] ?: null,
                    ]
                );
            }
        };

        $this->info('Importing group bookings...');
        $bar = $this->output->createProgressBar(count($payload['bookings'] ?? []));
        foreach ($payload['bookings'] ?? [] as $row) {
            $importBooking($row, false);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        $this->info('Importing cancelled bookings...');
        $bar = $this->output->createProgressBar(count($payload['cancelledBookings'] ?? []));
        foreach ($payload['cancelledBookings'] ?? [] as $row) {
            $importBooking($row, true);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        $this->info('Importing enquiries...');
        $enqCount = 0;
        foreach ($payload['enquiries'] ?? [] as $row) {
            $status = match ($row['status'] ?? 'new') {
                'cancelled', 'lost' => EnquiryStatus::Lost->value,
                'quoted', 'confirmed' => EnquiryStatus::Quoted->value,
                'follow_up', 'new' => EnquiryStatus::Chesed->value,
                default => EnquiryStatus::Chesed->value,
            };

            Enquiry::query()->updateOrCreate(
                ['ref' => $row['ref']],
                [
                    'year' => $row['year'] ?? null,
                    'enquiry_date' => $row['enquiryDate'] ?: null,
                    'day' => $row['day'] ?: null,
                    'nights' => max(1, (int) ($row['nights'] ?? 1)),
                    'group_name' => $row['groupName'],
                    'hotel_id' => $hotel->id,
                    'assigned_to' => $admin?->id,
                    'rooms_per_night' => (int) ($row['roomsPerNight'] ?? 0),
                    'single_rooms' => (int) ($row['single'] ?? 0),
                    'single_rate' => (float) ($row['singleRate'] ?? 0),
                    'double_rooms' => (int) ($row['double'] ?? 0),
                    'double_rate' => (float) ($row['doubleRate'] ?? 0),
                    'triple_rooms' => (int) ($row['triple'] ?? 0),
                    'triple_rate' => (float) ($row['tripleRate'] ?? 0),
                    'basis' => $row['basis'] ?: 'BB',
                    'total_revenue' => (float) ($row['totalRevenue'] ?? 0),
                    'cxl_policy' => $row['cxlPolicy'] ?: null,
                    'option_date' => $row['optionDate'] ?: null,
                    'email' => $row['email'] ?: null,
                    'remarks' => $row['remarks'] ?: null,
                    'status' => $status,
                ]
            );
            $enqCount++;
        }

        $this->info('Importing BOB snapshots...');
        foreach ($payload['bob'] ?? [] as $row) {
            BobMonthlySnapshot::query()->updateOrCreate(
                [
                    'year' => (int) ($row['year'] ?? 2025),
                    'month' => (int) $row['month'],
                ],
                [
                    'label' => $row['label'] ?? null,
                    'bob_current' => (float) ($row['bobCurrent'] ?? 0),
                    'bob_previous' => (float) ($row['bobPrevious'] ?? 0),
                    'bob_variance' => (float) ($row['bobVariance'] ?? 0),
                    'adr_current' => (float) ($row['adrCurrent'] ?? 0),
                    'adr_previous' => (float) ($row['adrPrevious'] ?? 0),
                    'adr_variance' => (float) ($row['adrVariance'] ?? 0),
                    'room_nights_current' => (int) ($row['roomNightsCurrent'] ?? 0),
                    'room_nights_previous' => (int) ($row['roomNightsPrevious'] ?? 0),
                    'breakfast_revenue' => (float) ($row['breakfastRevenue'] ?? 0),
                    'dinner_revenue' => (float) ($row['dinnerRevenue'] ?? 0),
                    'dinner_covers' => (int) ($row['dinnerCovers'] ?? 0),
                ]
            );
        }

        $this->newLine();
        $this->table(
            ['Module', 'Count'],
            [
                ['Contacts processed', $contactCount],
                ['Travel agencies', TravelAgency::query()->count()],
                ['Group bookings', GroupBooking::query()->count()],
                ['Daily rows', GroupBookingDailyRow::query()->count()],
                ['Enquiries', Enquiry::query()->count()],
                ['BOB 2025 months', BobMonthlySnapshot::query()->where('year', 2025)->count()],
                ['Active bookings', GroupBooking::query()->active()->count()],
                ['Cancelled bookings', GroupBooking::query()->cancelled()->count()],
            ]
        );

        $this->info('BW Group Tracker 2025 import completed.');

        return self::SUCCESS;
    }

    private function normalizeBookingStatus(?string $status, bool $cancelled): string
    {
        if ($cancelled) {
            return BookingStatus::Cancelled->value;
        }

        $status = trim((string) $status);
        $allowed = BookingStatus::values();
        if (in_array($status, $allowed, true)) {
            return $status;
        }

        return BookingStatus::Provisional->value;
    }

    private function normalizePaymentDisplay(?string $status): ?string
    {
        $status = trim((string) $status);
        if ($status === '') {
            return null;
        }

        return in_array($status, PaymentDisplayStatus::values(), true)
            ? $status
            : PaymentDisplayStatus::Partial->value;
    }

    private function normalizeMealPlan(?string $plan): string
    {
        $plan = strtoupper(trim((string) $plan));
        $allowed = ['RO', 'BB', 'HB', 'FB', 'DBB', 'SC'];

        if (in_array($plan, $allowed, true)) {
            return $plan;
        }

        if (preg_match('/\b(DBB|HB|FB|BB|RO|SC)\b/', $plan, $m)) {
            return $m[1];
        }

        return 'BB';
    }
}
