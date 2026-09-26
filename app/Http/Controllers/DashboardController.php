<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Enquiry;
use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Models\TravelAgency;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'hotels' => Hotel::query()->where('status', 'active')->count(),
            'companies' => Company::query()->where('status', 'active')->count(),
            'travelAgencies' => TravelAgency::query()->where('status', 'active')->count(),
            'activeGroups' => GroupBooking::query()->active()->count(),
            'arrivalsToday' => GroupBooking::query()->active()->arrivingOn(now()->toDateString())->count(),
            'departuresToday' => GroupBooking::query()->active()->departingOn(now()->toDateString())->count(),
            'openEnquiries' => Enquiry::query()
                ->whereNotIn('status', ['confirmed', 'lost', 'cancelled'])
                ->count(),
            'revenueYtd' => GroupBooking::query()
                ->active()
                ->whereYear('arrival', now()->year)
                ->sum('revenue'),
        ];

        $upcomingArrivals = GroupBooking::query()
            ->with(['hotel', 'travelAgency'])
            ->active()
            ->whereDate('arrival', '>=', now()->toDateString())
            ->orderBy('arrival')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('stats', 'upcomingArrivals'));
    }
}
