<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Support\EnquiryIndexFilters;
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

        EnquiryIndexFilters::apply($query, $request);

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'group_name' => 'group_name',
            'enquiry_date' => 'enquiry_date',
            'response_date' => 'response_date',
            'check_in' => 'check_in',
            'check_out' => 'check_out',
            'day' => 'day',
            'nights' => 'nights',
            'rooms_per_night' => 'rooms_per_night',
            'email' => 'email',
            'status' => 'status',
            'total_revenue' => 'total_revenue',
            'option_date' => 'option_date',
            'cxl_due_date' => 'cxl_due_date',
        ], 'check_out');

        $bookings = $query->get();
        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $monthValue = $month->format('Y-m');

        return view('departures.index', compact('bookings', 'hotels', 'travelAgencies', 'month', 'monthValue'));
    }

    public function show(Enquiry $enquiry): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($this->isVisibleDeparture($enquiry), 404);

        $enquiry->load(['hotel', 'responses.user']);

        return view('stay-lists.show', [
            'enquiry' => $enquiry,
            'list' => 'departures',
        ]);
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

    private function isVisibleDeparture(Enquiry $enquiry): bool
    {
        return Enquiry::query()
            ->accessibleBy()
            ->groupBookings()
            ->whereKey($enquiry->getKey())
            ->whereDate('check_out', '<=', now()->toDateString())
            ->exists();
    }
}
