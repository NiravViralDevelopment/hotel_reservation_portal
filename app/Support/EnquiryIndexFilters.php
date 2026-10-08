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
    public static function applyDateRange(Builder $query, Request $request, string $column, string $fromKey = 'date_from', string $toKey = 'date_to'): void
    {
        [$from, $to] = self::dateBounds($request, $fromKey, $toKey);
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
    public static function dateBounds(Request $request, string $fromKey = 'date_from', string $toKey = 'date_to'): array
    {
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
