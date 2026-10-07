<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ArrivalController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $month = $this->selectedMonth($request);

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->groupBookings()
            ->whereYear('check_in', $month->year)
            ->whereMonth('check_in', $month->month)
            ->where(function ($query) {
                $query->whereNull('check_out')
                    ->orWhereDate('check_out', '>', now()->toDateString());
            });

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'group_name' => 'group_name',
            'check_in' => 'check_in',
            'nights' => 'nights',
            'status' => 'status',
        ], 'check_in');

        $bookings = $query->get();
        $hotels = Hotel::optionsForSelect();
        $monthValue = $month->format('Y-m');

        return view('arrivals.index', compact('bookings', 'hotels', 'month', 'monthValue'));
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
