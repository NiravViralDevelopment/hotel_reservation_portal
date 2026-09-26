<?php

namespace App\Http\Controllers;

use App\Models\GroupBooking;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroupBooking::class);

        $month = $request->integer('month', (int) now()->format('n'));
        $year = $request->integer('year', (int) now()->format('Y'));

        $start = now()->setDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query = GroupBooking::query()
            ->with(['hotel'])
            ->active()
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('arrival', [$start, $end])
                    ->orWhereBetween('departure', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('arrival', '<=', $start)
                            ->where('departure', '>=', $end);
                    });
            });

        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        $bookings = $query->orderBy('arrival')->get();
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('calendar.index', compact('bookings', 'hotels', 'month', 'year', 'start'));
    }
}
