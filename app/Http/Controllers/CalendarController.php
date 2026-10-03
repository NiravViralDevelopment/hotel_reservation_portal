<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\HotelAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $month = $request->integer('month', (int) now()->format('n'));
        $year = $request->integer('year', (int) now()->format('Y'));

        $start = now()->setDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel'])
            ->groupBookings()
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('check_in', [$start, $end])
                    ->orWhereBetween('check_out', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('check_in', '<=', $start)
                            ->where('check_out', '>=', $end);
                    });
            });

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        $bookings = $query->orderBy('check_in')->get();
        $hotels = Hotel::optionsForSelect();

        return view('calendar.index', compact('bookings', 'hotels', 'month', 'year', 'start'));
    }
}
