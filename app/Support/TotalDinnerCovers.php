<?php

namespace App\Support;

class TotalDinnerCovers
{
    /**
     * (Single Rooms × Nights + Double Rooms × Nights + Triple Rooms × Nights) × Nights.
     *
     * A booking with stay segments uses each segment’s own room counts and nights.
     * Daily rows are one night each. Otherwise the booking room counts and nights are used.
     *
     * @param  list<array<string, mixed>>  $segments
     * @param  list<array<string, mixed>>  $dailyRows
     */
    public static function calculate(array $segments, array $dailyRows, int $singleRooms, int $doubleRooms, int $tripleRooms, int $nights): int
    {
        if ($segments !== []) {
            $total = 0;
            foreach ($segments as $segment) {
                if (! is_array($segment)) {
                    continue;
                }
                $total += self::forRoomsAndNights(
                    (int) ($segment['single_rooms'] ?? 0),
                    (int) ($segment['double_rooms'] ?? 0),
                    (int) ($segment['triple_rooms'] ?? 0),
                    (int) ($segment['nights'] ?? 0),
                );
            }

            return $total;
        }

        if ($dailyRows !== []) {
            $total = 0;
            foreach ($dailyRows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $total += self::forRoomsAndNights(
                    (int) ($row['single_rooms'] ?? 0),
                    (int) ($row['double_rooms'] ?? 0),
                    (int) ($row['triple_rooms'] ?? 0),
                    1,
                );
            }

            return $total;
        }

        return self::forRoomsAndNights($singleRooms, $doubleRooms, $tripleRooms, $nights);
    }

    private static function forRoomsAndNights(int $singleRooms, int $doubleRooms, int $tripleRooms, int $nights): int
    {
        $nights = max(0, $nights);
        $rooms = max(0, $singleRooms) + max(0, $doubleRooms) + max(0, $tripleRooms);

        return ($rooms * $nights) * $nights;
    }
}
