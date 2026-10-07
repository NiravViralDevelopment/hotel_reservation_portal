<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DepartureController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $month = $this->selectedMonth($request);

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->groupBookings()
            ->whereYear('check_out', $month->year)
            ->whereMonth('check_out', $month->month)
            ->whereDate('check_out', '<=', now()->toDateString());

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'group_name' => 'group_name',
            'check_out' => 'check_out',
            'nights' => 'nights',
            'status' => 'status',
        ], 'check_out');

        $bookings = $query->get();
        $hotels = Hotel::optionsForSelect();
        $monthValue = $month->format('Y-m');

        return view('departures.index', compact('bookings', 'hotels', 'month', 'monthValue'));
    }

    private function selectedMonth(Request $request): Carbon
    {
        $value = $request->string('month')->toString();
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            $month = Carbon::createFromFormat('!Y-m', $value);
            if ($month) {
                return $month->startOfMonth();
            }
        }

        return now()->startOfMonth();
    }
}
