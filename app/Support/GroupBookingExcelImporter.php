<?php

namespace App\Support;

use App\Models\Contact;
use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\TravelAgency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GroupBookingExcelImporter
{
    /**
     * @return array{imported: int, updated: int, skipped: int}
     */
    public function import(string $path, ?int $hotelId = null, ?int $userId = null): array
    {
        $rows = SimpleXlsxReader::rows($path);
        if ($rows === []) {
            return ['imported' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $headers = array_map(fn ($h) => $this->normalizeHeader((string) $h), $rows[0]);
        $map = $this->headerMap($headers);

        $hotelId = $hotelId ?: HotelAccess::currentHotel()?->id;
        if (! $hotelId) {
            $hotelId = Hotel::optionsForSelect()->first()?->id;
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $get = function (string $key) use ($map, $row): ?string {
                if (! isset($map[$key])) {
                    return null;
                }
                $value = $row[$map[$key]] ?? null;
                if ($value === null) {
                    return null;
                }
                $value = trim((string) $value);

                return $value === '' ? null : $value;
            };

            $ref = $get('ref');
            $client = $get('client');
            if ($ref === null && $client === null) {
                $skipped++;
                continue;
            }

            $checkIn = $this->toDate($get('check_in'));
            $checkOut = $this->toDate($get('check_out'));
            $nights = max(1, (int) ($get('nights') ?? 1));
            if ($checkIn && $checkOut) {
                $diff = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
                if ($diff > 0) {
                    $nights = $diff;
                }
            }

            $groupName = $client ?: ($ref ?: 'Imported booking');
            if ($ref) {
                $groupName = $client ? "{$client} ({$ref})" : $ref;
            }

            $agency = $this->resolveAgency($client);
            $singleRooms = (int) ($get('single_rooms') ?? 0);
            $doubleRooms = (int) ($get('double_rooms') ?? 0);
            $twinRooms = (int) ($get('twin_rooms') ?? 0);
            $tripleRooms = (int) ($get('triple_rooms') ?? 0);
            $roomsPerNight = (int) ($get('rooms_per_night') ?? ($singleRooms + $doubleRooms + $twinRooms + $tripleRooms));
            $totalRevenue = $this->toDecimal($get('total_revenue'));
            $commission = $this->toCommissionPercent($get('commission'));

            $attrs = [
                'year' => $checkIn ? (int) Carbon::parse($checkIn)->format('Y') : (int) now()->format('Y'),
                'enquiry_date' => $checkIn ?: now()->toDateString(),
                'day' => $checkIn ? Carbon::parse($checkIn)->format('l') : null,
                'check_in' => $checkIn,
                'check_in_day' => $checkIn ? Carbon::parse($checkIn)->format('l') : null,
                'check_out' => $checkOut,
                'nights' => $nights,
                'days' => $nights,
                'group_name' => $groupName,
                'client' => $client,
                'travel_agency_id' => $agency?->id,
                'hotel_id' => $hotelId,
                'assigned_to' => $userId,
                'email' => $this->firstEmail($get('email')),
                'service_person' => $get('contact'),
                'subject' => $get('agency_ref'),
                'cxl_policy' => $get('cxl_policy'),
                'basis' => $get('basis') ?: 'BB',
                'single_rooms' => $singleRooms,
                'single_rate' => $this->toDecimal($get('single_rate')),
                'double_rooms' => $doubleRooms,
                'double_rate' => $this->toDecimal($get('double_rate')),
                'twin_rooms' => $twinRooms,
                'twin_rate' => $this->toDecimal($get('twin_rate')),
                'triple_rooms' => $tripleRooms,
                'triple_rate' => $this->toDecimal($get('triple_rate')),
                'rooms_per_night' => $roomsPerNight,
                'total_revenue' => $totalRevenue,
                'total_price' => $totalRevenue,
                'net_price' => $this->toDecimal($get('net_revenue')) ?: $totalRevenue,
                'grand_total' => $this->toDecimal($get('invoice_amount')) ?: $totalRevenue,
                'service_total' => $this->toDecimal($get('bb_revenue')),
                'agent_comm_percent' => $commission,
                'agent_comm_amount' => $commission > 0 ? round(($totalRevenue * $commission) / 100, 2) : 0,
                'remarks' => $this->joinNotes([
                    $get('update'),
                    $get('payment_status') ? 'Payment: '.$get('payment_status') : null,
                    $get('payment_term') ? 'Term: '.$get('payment_term') : null,
                    $get('rooming') ? 'Rooming: '.$get('rooming') : null,
                ]),
                'booking_msg' => $get('update'),
                'status' => 'confirmed',
                'is_confirm' => true,
                'is_cancel' => false,
                'cancellation_reason' => null,
                'has_tax' => false,
            ];

            $lookup = $ref
                ? ['ref' => $ref]
                : ['group_name' => $groupName];

            $existing = Enquiry::query()->where($lookup)->first();
            if ($existing) {
                // Keep unique group_name when updating by ref.
                if ($ref) {
                    unset($attrs['group_name']);
                }
                $existing->update($attrs);
                $updated++;
            } else {
                // Ensure unique group_name.
                $baseName = $attrs['group_name'];
                $suffix = 1;
                while (Enquiry::query()->where('group_name', $attrs['group_name'])->exists()) {
                    $attrs['group_name'] = $baseName.' #'.$suffix;
                    $suffix++;
                }
                if ($ref) {
                    $attrs['ref'] = $ref;
                }
                Enquiry::query()->create($attrs);
                $imported++;
            }
        }

        return compact('imported', 'updated', 'skipped');
    }

    /**
     * @param  list<string>  $headers
     * @return array<string, int>
     */
    private function headerMap(array $headers): array
    {
        $aliases = [
            'check_in' => ['date of arrival', 'arrival date', 'arrival', 'check in', 'check_in'],
            'check_out' => ['date of departure', 'departure date', 'departure', 'check out', 'check_out', 'end date'],
            'nights' => ['nights', 'night'],
            'ref' => ['block id', 'block_id', 'ref', 'reference'],
            'client' => ['client', 'group', 'group name', 'group_name'],
            'agency_ref' => ['agency ref', 'agency_ref'],
            'contact' => ['contact person', 'contact', 'service person', 'service_person'],
            'email' => ['email id', 'email', 'emailid'],
            'status' => ['status'],
            'cxl_policy' => ['cxl policy', 'cxl_policy', 'cancellation policy'],
            'commission' => ['commission', 'agent comm %', 'agent_comm_percent'],
            'single_rooms' => ['single rns', 'single rooms', 'single_rooms'],
            'single_rate' => ['single gross rate', 'single rate', 'single_rate'],
            'double_rooms' => ['double rns', 'double rooms', 'double_rooms'],
            'double_rate' => ['double rate', 'double_rate'],
            'twin_rooms' => ['twin rns', 'twin rooms', 'twin_rooms'],
            'twin_rate' => ['twin rate', 'twin_rate'],
            'triple_rooms' => ['triple rns', 'triple rooms', 'triple_rooms'],
            'triple_rate' => ['triple rate', 'triple_rate'],
            'rooms_per_night' => ['total rns', 'rooms per night', 'rooms_per_night', 'rooms'],
            'total_revenue' => ['total rev', 'total revenue', 'total_revenue', 'total price', 'total_price'],
            'bb_revenue' => ['bb revenue(nett £8)', 'bb revenue', 'bb_revenue'],
            'net_revenue' => ['total rev ex vat/bf/dinner', 'nett rev', 'net price', 'net_price'],
            'basis' => ['bb/dbb', 'basis', 'meal plan'],
            'update' => ['update', 'remarks', 'notes'],
            'payment_status' => ['payment status', 'payment_status'],
            'payment_term' => ['payment term', 'payment_term'],
            'rooming' => ['rooming list', 'rooming'],
            'invoice_amount' => ['invoice amount', 'invoice_amount', 'grand total', 'grand_total'],
        ];

        $map = [];
        foreach ($headers as $index => $header) {
            foreach ($aliases as $field => $names) {
                if (in_array($header, $names, true) && ! isset($map[$field])) {
                    $map[$field] = $index;
                }
            }
        }

        return $map;
    }

    private function normalizeHeader(string $header): string
    {
        $header = str_replace(["\r", "\n", "\t"], ' ', $header);
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;

        return Str::lower(trim($header));
    }

    private function toDate(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function toDecimal(?string $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', $value) ?? '0';

        return round((float) $clean, 2);
    }

    private function toCommissionPercent(?string $value): float
    {
        if ($value === null || $value === '' || Str::lower($value) === 'no') {
            return 0.0;
        }

        $num = $this->toDecimal($value);
        if ($num > 0 && $num <= 1) {
            return round($num * 100, 2);
        }

        return $num;
    }

    private function firstEmail(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $parts = preg_split('/[\/,;]+/', $value) ?: [];
        $email = trim((string) ($parts[0] ?? ''));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }

    /**
     * @param  list<string|null>  $parts
     */
    private function joinNotes(array $parts): ?string
    {
        $parts = array_values(array_filter(array_map(
            fn ($p) => $p === null ? null : trim($p),
            $parts
        )));

        return $parts === [] ? null : implode(' | ', $parts);
    }

    private function resolveAgency(?string $clientName): ?TravelAgency
    {
        $clientName = trim((string) $clientName);
        if ($clientName === '') {
            return null;
        }

        $existing = TravelAgency::query()
            ->where('name', $clientName)
            ->first();
        if ($existing) {
            return $existing;
        }

        $code = Str::upper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $clientName) ?: 'AGY', 0, 12));
        $base = $code;
        $i = 1;
        while (TravelAgency::query()->where('code', $code)->exists()) {
            $code = Str::upper(Str::substr($base, 0, 10)).$i;
            $i++;
        }

        return TravelAgency::query()->create([
            'name' => $clientName,
            'code' => $code,
            'country' => 'United Kingdom',
            'status' => 'active',
        ]);
    }

    /**
     * Columns shown on the group bookings index, excluding Hotel, Attachment, and Actions.
     * Hotel is always the user's currently selected hotel.
     *
     * @return list<string>
     */
    public function templateHeaders(): array
    {
        return [
            'Date of Arrival',
            'Date of Departure',
            'Arrival Day',
            'No. of Nights',
            'Block ID',
            'Client',
            'Agency Ref',
            'Contact Name',
            'Contact Phone/Mail',
            'Contract Sent On',
            "Contract Rec'd On",
            'Saved to Doc',
            'Payment Term',
            'Payment Due Date',
            'Payment Status',
            'CXL Policy',
            'CXL Due Date',
            'CXL Date',
            'Single RNs',
            'Single Rate',
            'Double RNs',
            'Double Rate',
            'Triple RNs',
            'Triple Rate',
            'Total Room Nights',
            'Total Rev',
            'BB Revenue Nett',
            'Dinner Revenue',
            'Nett Revenue excl BB / Dinner & VAT',
            'BB/DBB',
            'Comments',
            'Rooming List',
            'Invoice Status',
            'Invoice Sent On',
            'Invoice Amount',
            'Status',
            'Commission',
            'Twin RNs',
            'Twin Gross Rate',
            'Invoice Number',
            'Commission Payable Status',
        ];
    }

    /**
     * Two example rows in the same order as templateHeaders().
     *
     * @return list<list<string|int|null>>
     */
    public function templateExampleRows(): array
    {
        return [
            [
                '2026-12-01', '2026-12-03', 'Tuesday', 2, 'SAMPLE-001', 'Example Travel Ltd', 'AG-1001',
                'Jane Smith', '07700900111 / jane.smith@example.com', '2026-11-01', '2026-11-05', 'Yes',
                '14 days', '2026-11-20', 'Pending', '14 days', '2026-11-17', null,
                2, '95.00', 4, '120.00', 0, null, 6, '670.00', '540.00', '80.00', '50.00',
                'BB', 'Sample booking', 'Pending', 'Not sent', null, null, 'Confirmed', 'Yes',
                0, null, null, 'Pending',
            ],
            [
                '2026-12-10', '2026-12-12', 'Thursday', 2, 'SAMPLE-002', 'Sample Agency', 'AG-1002',
                'John Brown', '07700900222 / john.brown@example.com', '2026-11-08', '2026-11-10', 'Yes',
                '7 days', '2026-12-03', 'Paid', '7 days', '2026-12-03', null,
                1, '89.00', 2, '110.00', 1, '140.00', 5, '554.00', '360.00', '60.00', '29.00',
                'DBB', 'Second sample booking', 'Received', 'Sent', '2026-11-15', '554.00', 'Confirmed', 'No',
                1, '105.00', 'INV-1002', 'Not payable',
            ],
        ];
    }

    /**
     * @return array{headers: list<string>, rows: list<array<string, string|null>>}
     */
    public function readSheet(string $path): array
    {
        $rows = SimpleXlsxReader::rows($path);
        if ($rows === []) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = [];
        foreach ($rows[0] as $index => $header) {
            $header = trim((string) $header);
            if ($header !== '') {
                $headers[$index] = $header;
            }
        }

        $data = [];
        for ($i = 1, $count = count($rows); $i < $count; $i++) {
            $assoc = [];
            $blank = true;
            foreach ($headers as $index => $header) {
                $value = $rows[$i][$index] ?? null;
                $value = $value === null ? null : trim((string) $value);
                if ($value === '') {
                    $value = null;
                }
                if ($value !== null) {
                    $blank = false;
                }
                $assoc[$header] = $value;
            }
            if (! $blank) {
                $data[] = $assoc;
            }
        }

        return ['headers' => array_values($headers), 'rows' => $data];
    }

    /**
     * @param  list<array<string, string|null>>  $rows
     * @return array{saved: int, errors: list<string>}
     */
    public function saveRows(array $rows, int $hotelId, ?int $userId): array
    {
        $saved = 0;
        $errors = [];

        DB::transaction(function () use ($rows, $hotelId, $userId, &$saved, &$errors): void {
            foreach ($rows as $index => $row) {
                try {
                    $this->saveImportedRow($row, $hotelId, $userId);
                    $saved++;
                } catch (\Throwable $exception) {
                    $errors[] = 'Row '.($index + 1).': '.$exception->getMessage();
                }
            }
        });

        return ['saved' => $saved, 'errors' => $errors];
    }

    /**
     * @param  array<string, string|null>  $row
     */
    private function saveImportedRow(array $row, int $hotelId, ?int $userId): void
    {
        $values = $this->mappedImportValues($row);
        $blockId = $values['block_id'] ?? null;
        $client = $values['client'] ?? null;
        $ref = $values['ref'] ?? null;

        if ($blockId === null && $client === null && $ref === null) {
            throw new \RuntimeException('A row needs a Block ID, Client, or Agency Ref.');
        }

        $existing = null;
        if ($blockId !== null) {
            $existing = Enquiry::query()->where('block_id', $blockId)->first();
            if ($existing && (int) $existing->hotel_id !== $hotelId) {
                throw new \RuntimeException('Block ID '.$blockId.' belongs to another hotel.');
            }
        }

        [$phone, $email] = $this->splitPhoneMail($values['contact_phone_mail'] ?? null);
        $checkIn = $this->toDate($values['check_in'] ?? null);
        $checkOut = $this->toDate($values['check_out'] ?? null);
        $nights = isset($values['nights']) && $values['nights'] !== null && $values['nights'] !== ''
            ? max(0, (int) $values['nights'])
            : null;
        if (($nights === null || $nights === 0) && $checkIn && $checkOut) {
            $diff = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
            $nights = $diff > 0 ? $diff : 1;
        }
        $nights = $nights ?: 1;
        $day = $values['day'] ?? null;
        if (($day === null || $day === '') && $checkIn) {
            $day = Carbon::parse($checkIn)->format('l');
        }

        $single = (int) ($values['single_rooms'] ?? 0);
        $double = (int) ($values['double_rooms'] ?? 0);
        $twin = (int) ($values['twin_rooms'] ?? 0);
        $triple = (int) ($values['triple_rooms'] ?? 0);
        $totalRns = isset($values['total_rns']) && $values['total_rns'] !== null && $values['total_rns'] !== ''
            ? (int) $values['total_rns']
            : $single + $double + $twin + $triple;

        [$hasCommission, $commissionPercentage] = $this->commissionFromCell($values['commission'] ?? null);
        $totalRevenue = $this->toDecimal($values['total_revenue'] ?? null);

        $attrs = [
            'year' => $checkIn ? (int) Carbon::parse($checkIn)->format('Y') : (int) now()->format('Y'),
            'enquiry_date' => $checkIn ?: now()->toDateString(),
            'day' => $day,
            'check_in' => $checkIn,
            'check_in_day' => $day,
            'check_out' => $checkOut,
            'nights' => $nights,
            'days' => $nights,
            'client' => $client,
            'ref' => $ref,
            'block_id' => $blockId,
            'hotel_id' => $hotelId,
            'contact_id' => $this->resolveContact($values['contact_name'] ?? null, $email, $phone),
            'email' => $email,
            'contact_number' => $phone,
            'service_person' => $values['contact_name'] ?? null,
            'contract_sent_on' => $this->toDate($values['contract_sent_on'] ?? null),
            'contract_received_on' => $this->toDate($values['contract_received_on'] ?? null),
            'saved_to_doc' => $values['saved_to_doc'] ?? null,
            'payment_term' => $values['payment_term'] ?? null,
            'payment_due_date' => $this->toDate($values['payment_due_date'] ?? null),
            'payment_status' => $values['payment_status'] ?? null,
            'cxl_policy' => $values['cxl_policy'] ?? null,
            'cxl_due_date' => $this->toDate($values['cxl_due_date'] ?? null),
            'cxl_date' => $this->toDate($values['cxl_date'] ?? null),
            'single_rooms' => $single,
            'single_rate' => $this->toDecimal($values['single_rate'] ?? null),
            'double_rooms' => $double,
            'double_rate' => $this->toDecimal($values['double_rate'] ?? null),
            'triple_rooms' => $triple,
            'triple_rate' => $this->toDecimal($values['triple_rate'] ?? null),
            'twin_rooms' => $twin,
            'twin_rate' => $this->toDecimal($values['twin_rate'] ?? null),
            'rooms_per_night' => $single + $double + $twin + $triple,
            'total_rns' => $totalRns,
            'total_revenue' => $totalRevenue,
            'bb_revenue' => $this->toDecimal($values['bb_revenue'] ?? null),
            'dinner_revenue' => $this->toDecimal($values['dinner_revenue'] ?? null),
            'nett_rev_ex_vat' => $this->toDecimal($values['nett_rev_ex_vat'] ?? null),
            'basis' => $values['basis'] ?? null,
            'booking_update' => $values['booking_update'] ?? null,
            'rooming' => $values['rooming'] ?? null,
            'invoice_status' => $values['invoice_status'] ?? null,
            'invoice_sent_on' => $this->toDate($values['invoice_sent_on'] ?? null),
            'invoice_amount' => $this->toDecimal($values['invoice_amount'] ?? null),
            'invoice_number' => $values['invoice_number'] ?? null,
            'commission_payable_status' => $values['commission_payable_status'] ?? null,
            'has_commission' => $hasCommission,
            'commission_percentage' => $commissionPercentage,
            'status' => filled($values['status'] ?? null) ? $values['status'] : 'Confirmed',
            'is_confirm' => true,
            'is_cancel' => false,
            'cancellation_reason' => null,
        ];

        if ($existing) {
            $existing->update($attrs);
            $existing->refresh();
            GroupQuotePdf::generate($existing);

            return;
        }

        $attrs['group_name'] = $this->uniqueGroupName($client ?: ($blockId ?: ($ref ?: 'Imported booking')), $ref);
        $attrs['created_by'] = $userId;
        $created = Enquiry::query()->create($attrs);
        GroupQuotePdf::generate($created);
    }

    /**
     * @param  array<string, string|null>  $row
     * @return array<string, string|null>
     */
    private function mappedImportValues(array $row): array
    {
        $fields = $this->importHeaderFields();
        $values = [];
        foreach ($row as $header => $value) {
            $key = $this->normalizeHeader((string) $header);
            if (! isset($fields[$key])) {
                continue;
            }
            $text = $value === null ? null : trim((string) $value);
            $values[$fields[$key]] = $text === '' ? null : $text;
        }

        return $values;
    }

    /**
     * @return array<string, string>
     */
    private function importHeaderFields(): array
    {
        return [
            'date of arrival' => 'check_in',
            'date of departure' => 'check_out',
            'arrival day' => 'day',
            'day' => 'day',
            'no. of nights' => 'nights',
            'no of nights' => 'nights',
            'nights' => 'nights',
            'block id' => 'block_id',
            'client' => 'client',
            'agency ref' => 'ref',
            'contact name' => 'contact_name',
            'contact phone/mail' => 'contact_phone_mail',
            'contract sent on' => 'contract_sent_on',
            "contract rec'd on" => 'contract_received_on',
            'contract recd on' => 'contract_received_on',
            'saved to doc' => 'saved_to_doc',
            'payment term' => 'payment_term',
            'payment due date' => 'payment_due_date',
            'due date' => 'payment_due_date',
            'payment status' => 'payment_status',
            'cxl policy' => 'cxl_policy',
            'cxl due date' => 'cxl_due_date',
            'cxl date' => 'cxl_date',
            'single rns' => 'single_rooms',
            'single rate' => 'single_rate',
            'single gross rate' => 'single_rate',
            'double rns' => 'double_rooms',
            'double rate' => 'double_rate',
            'double gross rate' => 'double_rate',
            'triple rns' => 'triple_rooms',
            'triple rate' => 'triple_rate',
            'triple gross rate' => 'triple_rate',
            'total room nights' => 'total_rns',
            'total rns' => 'total_rns',
            'total rev' => 'total_revenue',
            'bb revenue nett' => 'bb_revenue',
            'dinner revenue' => 'dinner_revenue',
            'nett revenue excl bb / dinner & vat' => 'nett_rev_ex_vat',
            'bb/dbb' => 'basis',
            'comments' => 'booking_update',
            'rooming list' => 'rooming',
            'invoice status' => 'invoice_status',
            'invoice sent on' => 'invoice_sent_on',
            'invoice amount' => 'invoice_amount',
            'status' => 'status',
            'commission' => 'commission',
            'twin rns' => 'twin_rooms',
            'twin gross rate' => 'twin_rate',
            'twin rate' => 'twin_rate',
            'invoice number' => 'invoice_number',
            'commission payable status' => 'commission_payable_status',
        ];
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    private function splitPhoneMail(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [null, null];
        }

        $phone = null;
        $email = null;
        foreach (preg_split('/\s*\/\s*/', $value) ?: [] as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (filter_var($part, FILTER_VALIDATE_EMAIL)) {
                $email = $part;
            } else {
                $phone = $phone === null ? $part : $phone.' / '.$part;
            }
        }

        return [$phone, $email];
    }

    /**
     * @return array{0: bool|null, 1: float|null}
     */
    private function commissionFromCell(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [null, null];
        }

        $lower = Str::lower(trim($value));
        if (in_array($lower, ['yes', 'y'], true)) {
            return [true, null];
        }
        if (in_array($lower, ['no', 'n'], true)) {
            return [false, null];
        }

        return [true, $this->toDecimal($value)];
    }

    private function resolveContact(?string $name, ?string $email, ?string $phone): ?int
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        $query = Contact::query()->where('name', $name);
        if ($email !== null) {
            $query->where('email', $email);
        }
        $contact = $query->first();
        if ($contact) {
            return (int) $contact->id;
        }

        return (int) Contact::query()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ])->id;
    }

    private function uniqueGroupName(string $name, ?string $ref): string
    {
        $base = $name;
        $suffix = 1;
        while (Enquiry::query()->where('group_name', $name)->where('ref', $ref)->exists()) {
            $name = $base.' #'.$suffix;
            $suffix++;
        }

        return $name;
    }
}
