<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnquiryIndexFilters
{
    /**
     * Search and hotel filters used on the enquiry index.
     * The date range is applied by the caller because each list uses a different date.
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    public static function apply(Builder $query, Request $request): void
    {
        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('ref', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhere('service_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    public static function applyStatus(Builder $query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
    }

    /**
     * Inclusive date range. With no dates in the request, the current month is used.
     * An empty or invalid date on that side is ignored.
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    public static function applyDateRange(Builder $query, Request $request, string $column, string $fromKey = 'date_from', string $toKey = 'date_to', ?string $monthKey = null): void
    {
        [$from, $to] = self::dateBounds($request, $fromKey, $toKey, $monthKey);
        if ($from) {
            $query->whereDate($column, '>=', $from);
        }
        if ($to) {
            $query->whereDate($column, '<=', $to);
        }
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    public static function dateBounds(Request $request, string $fromKey = 'date_from', string $toKey = 'date_to', ?string $monthKey = null): array
    {
        if ($monthKey) {
            $month = self::filterMonth($request, $monthKey);
            if ($month) {
                return [
                    $month->copy()->startOfMonth()->toDateString(),
                    $month->copy()->endOfMonth()->toDateString(),
                ];
            }
        }

        if (! $request->exists($fromKey) && ! $request->exists($toKey)) {
            return [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ];
        }

        return [
            self::filterDate($request, $fromKey),
            self::filterDate($request, $toKey),
        ];
    }

    /**
     * Month shown in the filter. An explicit month wins; otherwise a full calendar
     * month range is reflected so the control matches the dates already applied.
     */
    public static function monthValue(Request $request, ?string $from, ?string $to, string $monthKey): ?string
    {
        $month = self::filterMonth($request, $monthKey);
        if ($month) {
            return $month->format('Y-m');
        }

        if (! $from || ! $to) {
            return null;
        }

        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->startOfDay();
        if ($start->isSameDay($start->copy()->startOfMonth()) && $end->isSameDay($start->copy()->endOfMonth())) {
            return $start->format('Y-m');
        }

        return null;
    }

    public static function filterMonth(Request $request, string $key): ?Carbon
    {
        if (! $request->filled($key)) {
            return null;
        }

        $value = $request->string($key)->toString();
        if (! preg_match('/^\d{4}-\d{2}$/', $value)) {
            return null;
        }

        $date = Carbon::createFromFormat('!Y-m', $value);
        if (! $date || $date->format('Y-m') !== $value) {
            return null;
        }

        return $date->startOfMonth();
    }

    public static function filterDate(Request $request, string $key): ?string
    {
        if (! $request->filled($key)) {
            return null;
        }

        $value = $request->string($key)->toString();
        $date = Carbon::createFromFormat('!Y-m-d', $value);

        if (! $date || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $date->toDateString();
    }
}
