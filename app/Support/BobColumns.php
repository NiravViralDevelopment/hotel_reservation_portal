<?php

namespace App\Support;

use App\Models\BobMonthlySnapshot;

class BobColumns
{
    /**
     * Index columns for the selected year.
     * Labels pick up that year and the year before it. Stored figures never use a year in the column name.
     *
     * @return list<array{
     *     key: string,
     *     label: string,
     *     group: string,
     *     group_label: string,
     *     format: string,
     *     kind: string,
     *     attribute?: string
     * }>
     */
    public static function forYear(int $year): array
    {
        $previousYear = $year - 1;

        return [
            [
                'key' => 'bob_nett',
                'label' => "BoB {$year} (Nett)",
                'group' => 'bob',
                'group_label' => 'BoB',
                'format' => 'money',
                'kind' => 'bookings_bob',
            ],
            [
                'key' => 'bob_nett_actual',
                'label' => "BoB {$previousYear} (Nett Actual)",
                'group' => 'bob',
                'group_label' => 'BoB',
                'format' => 'money',
                'kind' => 'manual',
                'attribute' => 'bob_nett_actual',
            ],
            [
                'key' => 'bob_variance_vs_previous',
                'label' => 'BoB Variance vs Previous Year',
                'group' => 'bob',
                'group_label' => 'BoB',
                'format' => 'money',
                'kind' => 'variance_bob',
            ],
            [
                'key' => 'stly_bob_nett',
                'label' => "STLY BoB {$previousYear} (Net)",
                'group' => 'bob',
                'group_label' => 'BoB',
                'format' => 'money',
                'kind' => 'manual',
                'attribute' => 'stly_bob_nett',
            ],
            [
                'key' => 'bob_variance_stly_vs_current',
                'label' => 'BoB Variance STLY vs Current Year',
                'group' => 'bob',
                'group_label' => 'BoB',
                'format' => 'money',
                'kind' => 'variance_stly',
            ],
            [
                'key' => 'adr_nett',
                'label' => "ADR {$year} (Net)",
                'group' => 'adr',
                'group_label' => 'ADR',
                'format' => 'money',
                'kind' => 'adr_current',
            ],
            [
                'key' => 'adr_nett_previous',
                'label' => "ADR {$previousYear} (Net)",
                'group' => 'adr',
                'group_label' => 'ADR',
                'format' => 'money',
                'kind' => 'manual',
                'attribute' => 'adr_nett',
            ],
            [
                'key' => 'adr_variance_vs_previous',
                'label' => 'ADR Variance vs Previous Year',
                'group' => 'adr',
                'group_label' => 'ADR',
                'format' => 'money',
                'kind' => 'adr_variance',
            ],
            [
                'key' => 'stly_adr_nett',
                'label' => "STLY ADR {$previousYear} (Net)",
                'group' => 'adr',
                'group_label' => 'ADR',
                'format' => 'money',
                'kind' => 'stly_adr',
            ],
            [
                'key' => 'room_nights',
                'label' => "Room Nights {$year}",
                'group' => 'nights',
                'group_label' => 'Room nights',
                'format' => 'number',
                'kind' => 'bookings_nights',
            ],
            [
                'key' => 'room_nights_previous',
                'label' => "Room Nights {$previousYear}",
                'group' => 'nights',
                'group_label' => 'Room nights',
                'format' => 'number',
                'kind' => 'manual',
                'attribute' => 'room_nights',
            ],
            [
                'key' => 'stly_room_nights',
                'label' => "STLY Room Nights {$previousYear}",
                'group' => 'nights',
                'group_label' => 'Room nights',
                'format' => 'number',
                'kind' => 'manual',
                'attribute' => 'stly_room_nights',
            ],
            [
                'key' => 'breakfast_revenue',
                'label' => 'Breakfast Revenue (Net) £10',
                'group' => 'fb',
                'group_label' => "Food & beverage {$year}",
                'format' => 'money',
                'kind' => 'bookings_breakfast',
            ],
            [
                'key' => 'dinner_revenue',
                'label' => 'Dinner Revenue (Net)',
                'group' => 'fb',
                'group_label' => "Food & beverage {$year}",
                'format' => 'money',
                'kind' => 'bookings_dinner',
            ],
            [
                'key' => 'dinner_covers',
                'label' => 'Total Dinner Covers',
                'group' => 'fb',
                'group_label' => "Food & beverage {$year}",
                'format' => 'dash',
                'kind' => 'blank',
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $columns
     * @return list<array<string, mixed>>
     */
    public static function cells(array $columns, ?object $booking, ?BobMonthlySnapshot $current, ?BobMonthlySnapshot $previous): array
    {
        $bob = self::bookingBob($booking);
        $nights = self::bookingNights($booking);
        $adr = self::divide($bob, $nights);
        $breakfast = self::bookingAmount($booking, 'breakfast_revenue');
        $dinner = self::bookingAmount($booking, 'dinner_revenue');
        $actual = self::manual($previous, 'bob_nett_actual');
        $stly = self::manual($previous, 'stly_bob_nett') ?? 0.0;
        $stlyNights = self::manual($previous, 'stly_room_nights');
        $adrPrevious = self::manual($previous, 'adr_nett');
        $stlyAdr = self::divide($stly, $stlyNights);

        return array_map(function (array $column) use ($bob, $nights, $adr, $breakfast, $dinner, $actual, $stly, $stlyAdr, $adrPrevious, $current, $previous) {
            $value = match ($column['kind']) {
                'bookings_bob' => $bob,
                'bookings_nights' => $nights,
                'bookings_breakfast' => $breakfast,
                'bookings_dinner' => $dinner,
                'adr_current' => $adr,
                'stly_adr' => $stlyAdr,
                'variance_bob' => self::subtract($bob, $actual),
                'variance_stly' => self::subtract($bob, $stly),
                'adr_variance' => self::subtract($adr, $adrPrevious),
                'manual' => self::manual($previous, $column['attribute']),
                'blank' => null,
                default => self::stored($column, $current, $previous),
            };

            return [
                'key' => $column['key'],
                'label' => $column['label'],
                'value' => $value,
                'raw' => self::raw($value, $column['format']),
                'format' => $column['format'],
                'group' => $column['group'],
                'editable' => $column['kind'] === 'manual',
                'input' => $column['kind'] === 'manual' ? $column['attribute'] : null,
                'formula' => in_array($column['kind'], ['variance_bob', 'variance_stly', 'adr_current', 'adr_variance', 'stly_adr'], true),
            ];
        }, $columns);
    }

    public static function bookingBob(?object $booking): float
    {
        if ($booking === null || $booking->bob_nett === null || $booking->bob_nett === '') {
            return 0.0;
        }

        return round((float) $booking->bob_nett, 2);
    }

    public static function bookingNights(?object $booking): int
    {
        if ($booking === null || $booking->room_nights === null || $booking->room_nights === '') {
            return 0;
        }

        return (int) $booking->room_nights;
    }

    public static function bookingAmount(?object $booking, string $attribute): float
    {
        if ($booking === null || $booking->{$attribute} === null || $booking->{$attribute} === '') {
            return 0.0;
        }

        return round((float) $booking->{$attribute}, 2);
    }

    public static function divide(float|int|null $amount, float|int|null $divisor): ?float
    {
        if ($amount === null || $divisor === null || (float) $divisor == 0.0) {
            return null;
        }

        return round((float) $amount / (float) $divisor, 2);
    }

    public static function subtract(float|int|null $left, float|int|null $right): ?float
    {
        if ($left === null || $right === null) {
            return null;
        }

        return round((float) $left - (float) $right, 2);
    }

    private static function manual(?BobMonthlySnapshot $previous, string $attribute): float|int|null
    {
        $value = $previous?->{$attribute};

        if ($value === null || $value === '') {
            return null;
        }

        return is_int($value) ? $value : (str_contains((string) $attribute, 'room') || $attribute === 'dinner_covers'
            ? (int) $value
            : round((float) $value, 2));
    }

    /**
     * @param  array<string, mixed>  $column
     */
    private static function stored(array $column, ?BobMonthlySnapshot $current, ?BobMonthlySnapshot $previous): float|int|null
    {
        $record = ($column['record'] ?? 'current') === 'previous' ? $previous : $current;
        $attribute = $column['attribute'] ?? null;

        if ($record === null || $attribute === null) {
            return null;
        }

        $value = $record->{$attribute};

        if ($value === null || $value === '') {
            return null;
        }

        return $column['format'] === 'number' ? (int) $value : round((float) $value, 2);
    }

    private static function raw(float|int|null $value, string $format): string
    {
        if ($value === null || (float) $value == 0.0) {
            return '';
        }

        if ($format === 'number') {
            return (string) (int) $value;
        }

        $text = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');

        return $text === '' || $text === '-' ? '' : $text;
    }
}
