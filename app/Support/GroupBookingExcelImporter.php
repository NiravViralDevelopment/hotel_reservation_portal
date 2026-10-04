<?php

namespace App\Support;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\TravelAgency;
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
            $tripleRooms = (int) ($get('triple_rooms') ?? 0);
            $roomsPerNight = (int) ($get('rooms_per_night') ?? ($singleRooms + $doubleRooms + $tripleRooms));
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
}
