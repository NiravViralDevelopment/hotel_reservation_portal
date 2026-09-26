<?php

namespace App\Http\Controllers;

use App\Models\GroupBooking;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartureController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroupBooking::class);

        $date = $request->date('date')?->toDateString() ?? now()->toDateString();

        $query = GroupBooking::query()
            ->with(['hotel', 'travelAgency', 'contact'])
            ->active()
            ->departingOn($date)
            ->orderBy('group_name');

        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        $bookings = $query->get();
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('departures.index', compact('bookings', 'hotels', 'date'));
    }
}
