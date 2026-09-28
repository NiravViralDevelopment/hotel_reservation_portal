<?php

namespace App\Http\Controllers;

use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CancelledBookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroupBooking::class);

        $query = GroupBooking::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->cancelled();

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        QuerySort::apply($query, $request, [
            'block_id' => 'block_id',
            'group_name' => 'group_name',
            'cancelled_at' => 'cancelled_at',
            'revenue' => 'revenue_lost',
            'reason' => 'cancellation_reason',
        ], 'cancelled_at', 'desc');

        $bookings = $query->paginate(25)->withQueryString();
        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);

        return view('cancelled-bookings.index', compact('bookings', 'hotels'));
    }
}
