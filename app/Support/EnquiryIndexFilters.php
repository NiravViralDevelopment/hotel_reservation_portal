<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnquiryIndexFilters
{
    /**
     * Search, hotel, agency, and status filters used on the enquiry index.
     * Month is applied by the caller because each list uses a different date.
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

        if ($request->filled('travel_agency_id')) {
            $query->where('travel_agency_id', $request->integer('travel_agency_id'));
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
     * Month filter used on the enquiry index. An empty or invalid month shows every month.
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    public static function applyMonth(Builder $query, Request $request, string $column): void
    {
        $month = self::selectedMonth($request);
        if ($month) {
            $query->whereYear($column, $month->year)
                ->whereMonth($column, $month->month);
        }
    }

    public static function monthValue(Request $request): string
    {
        if (! $request->exists('month')) {
            return now()->format('Y-m');
        }

        return $request->string('month')->toString();
    }

    public static function selectedMonth(Request $request): ?Carbon
    {
        if (! $request->exists('month')) {
            return now()->startOfMonth();
        }

        $value = $request->string('month')->toString();
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            return null;
        }

        $month = Carbon::createFromFormat('!Y-m', $value);

        return $month ? $month->startOfMonth() : null;
    }
}
