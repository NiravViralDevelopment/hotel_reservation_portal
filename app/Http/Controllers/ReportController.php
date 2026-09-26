<?php

namespace App\Http\Controllers;

use App\Models\GroupBooking;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('reports.index', compact('hotels'));
    }

    public function run(Request $request): View
    {
        $validated = $request->validate([
            'report' => ['required', 'in:bookings_by_hotel,arrivals_summary,revenue_by_agency'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],
        ]);

        $query = GroupBooking::query()->with(['hotel', 'travelAgency']);

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

        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('reports.run', compact('results', 'validated', 'hotels'));
    }
}
