<?php

namespace App\Http\Controllers;

use App\Models\BobMonthlySnapshot;
use App\Models\GroupBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->integer('year', (int) now()->format('Y'));

        $monthlyRevenue = GroupBooking::query()
            ->active()
            ->whereYear('arrival', $year)
            ->select(
                DB::raw('MONTH(arrival) as month'),
                DB::raw('SUM(revenue) as total_revenue'),
                DB::raw('SUM(bb_revenue) as bb_revenue'),
                DB::raw('SUM(dinner_revenue) as dinner_revenue'),
                DB::raw('SUM(total_rns) as room_nights'),
            )
            ->groupBy(DB::raw('MONTH(arrival)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $snapshots = BobMonthlySnapshot::query()
            ->where('year', $year)
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $yearTotal = GroupBooking::query()
            ->active()
            ->whereYear('arrival', $year)
            ->sum('revenue');

        return view('revenue.index', compact('monthlyRevenue', 'snapshots', 'year', 'yearTotal'));
    }
}
