<?php

namespace App\Http\Controllers;

use App\Models\BobMonthlySnapshot;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('revenue.view');

        $year = $request->integer('year', (int) now()->format('Y'));

        $monthlyRevenue = Enquiry::query()
            ->accessibleBy()
            ->groupBookings()
            ->whereYear('check_in', $year)
            ->select(
                DB::raw('MONTH(check_in) as month'),
                DB::raw('SUM(grand_total) as total_revenue'),
                DB::raw('SUM(COALESCE(service_total, 0)) as bb_revenue'),
                DB::raw('SUM(COALESCE(total_tax, 0)) as dinner_revenue'),
                DB::raw('SUM(COALESCE(nights, 0) * COALESCE(rooms_per_night, 0)) as room_nights'),
            )
            ->groupBy(DB::raw('MONTH(check_in)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $snapshots = BobMonthlySnapshot::query()
            ->where('year', $year)
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $yearTotal = Enquiry::query()
            ->accessibleBy()
            ->groupBookings()
            ->whereYear('check_in', $year)
            ->sum('grand_total');

        return view('revenue.index', compact('monthlyRevenue', 'snapshots', 'year', 'yearTotal'));
    }
}
