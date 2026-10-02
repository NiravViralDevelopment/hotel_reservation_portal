<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Support\HotelAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $this->authorize('reports.view');

        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);

        $defaultFrom = now()->subMonth()->toDateString();
        $defaultTo = now()->toDateString();

        return view('reports.index', compact('hotels', 'defaultFrom', 'defaultTo'));
    }

    public function module(Request $request, string $report): View
    {
        $this->authorize('reports.view');

        $map = [
            'group-bookings' => 'group_bookings',
            'enquiries' => 'enquiries',
            'cancelled-bookings' => 'cancelled_bookings',
        ];

        abort_unless(isset($map[$report]), 404);

        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $validated['report'] = $map[$report];
        $validated['date_from'] = $validated['date_from'] ?? now()->subMonth()->toDateString();
        $validated['date_to'] = $validated['date_to'] ?? now()->toDateString();

        if (Carbon::parse($validated['date_to'])->lt(Carbon::parse($validated['date_from']))) {
            $validated['date_to'] = $validated['date_from'];
        }

        $results = $this->datedReport($validated);
        $hotels = collect();

        return view('reports.run', compact('results', 'validated', 'hotels'));
    }

    public function run(Request $request): View
    {
        $this->authorize('reports.generate');

        $datedReports = ['group_bookings', 'enquiries', 'cancelled_bookings'];

        $validated = $request->validate([
            'report' => ['required', 'in:group_bookings,enquiries,cancelled_bookings,bookings_by_hotel,arrivals_summary,revenue_by_agency'],
            'date_from' => [
                'nullable',
                'date',
                Rule::requiredIf(fn () => in_array($request->input('report'), $datedReports, true)),
            ],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
                Rule::requiredIf(fn () => in_array($request->input('report'), $datedReports, true)),
            ],
            'hotel_id' => ['nullable', 'integer', Rule::in(HotelAccess::hotelIds())],
        ]);

        if (! empty($validated['hotel_id'])) {
            HotelAccess::ensure(null, $validated['hotel_id']);
        }

        if (in_array($validated['report'], $datedReports, true)) {
            $results = $this->datedReport($validated);
            $hotels = collect();

            return view('reports.run', compact('results', 'validated', 'hotels'));
        }

        $query = GroupBooking::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency']);

        if ($validated['date_from'] ?? null) {
            $query->whereDate('arrival', '>=', $validated['date_from']);
        }

        if ($validated['date_to'] ?? null) {
            $query->whereDate('arrival', '<=', $validated['date_to']);
        }

        if ($validated['hotel_id'] ?? null) {
            $query->where('hotel_id', $validated['hotel_id']);
        }

        $results = match ($validated['report']) {
            'bookings_by_hotel' => (clone $query)
                ->select('hotel_id', DB::raw('COUNT(*) as bookings'), DB::raw('SUM(revenue) as revenue'))
                ->groupBy('hotel_id')
                ->get(),
            'arrivals_summary' => (clone $query)
                ->active()
                ->orderBy('arrival')
                ->get(),
            'revenue_by_agency' => (clone $query)
                ->select('travel_agency_id', DB::raw('COUNT(*) as bookings'), DB::raw('SUM(revenue) as revenue'))
                ->groupBy('travel_agency_id')
                ->get(),
        };

        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);

        return view('reports.run', compact('results', 'validated', 'hotels'));
    }

    /**
     * @param  array{report: string, date_from: string, date_to: string}  $validated
     */
    private function datedReport(array $validated): mixed
    {
        $from = $validated['date_from'];
        $to = $validated['date_to'];

        return match ($validated['report']) {
            'group_bookings' => GroupBooking::query()
                ->accessibleBy()
                ->with(['hotel', 'travelAgency', 'company', 'contact', 'createdBy'])
                ->active()
                ->whereDate('arrival', '>=', $from)
                ->whereDate('arrival', '<=', $to)
                ->orderBy('arrival')
                ->get(),
            'enquiries' => Enquiry::query()
                ->accessibleBy()
                ->with(['hotel', 'travelAgency', 'contact', 'assignedTo'])
                ->whereNotIn('status', ['confirmed', 'cancelled'])
                ->whereNull('converted_booking_id')
                ->whereDate('enquiry_date', '>=', $from)
                ->whereDate('enquiry_date', '<=', $to)
                ->orderBy('enquiry_date')
                ->get(),
            'cancelled_bookings' => GroupBooking::query()
                ->accessibleBy()
                ->with(['hotel', 'travelAgency', 'company', 'contact', 'createdBy'])
                ->cancelled()
                ->where(function ($query) use ($from, $to) {
                    $query->where(function ($dated) use ($from, $to) {
                        $dated->whereNotNull('cancelled_at')
                            ->whereDate('cancelled_at', '>=', $from)
                            ->whereDate('cancelled_at', '<=', $to);
                    })->orWhere(function ($dated) use ($from, $to) {
                        $dated->whereNull('cancelled_at')
                            ->whereDate('cxl_date', '>=', $from)
                            ->whereDate('cxl_date', '<=', $to);
                    });
                })
                ->orderByDesc('cancelled_at')
                ->get(),
        };
    }
}
