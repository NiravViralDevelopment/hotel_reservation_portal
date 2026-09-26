<?php

namespace App\Http\Controllers;

use App\Models\GroupBooking;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CancelledBookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroupBooking::class);

        $query = GroupBooking::query()
            ->with(['hotel', 'travelAgency'])
            ->cancelled()
            ->orderByDesc('cancelled_at');

        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        $bookings = $query->paginate(25)->withQueryString();
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('cancelled-bookings.index', compact('bookings', 'hotels'));
    }
}
