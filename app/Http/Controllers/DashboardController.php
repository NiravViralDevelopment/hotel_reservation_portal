<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\User;
use App\Support\HotelAccess;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $this->authorize('dashboard.view');

        $allocatedHotels = HotelAccess::hotels();
        $currentHotel = HotelAccess::currentHotel();

        if ($currentHotel === null && ! HotelAccess::canAccessAllHotels()) {
            return view('dashboard.select-hotel', compact('allocatedHotels'));
        }

        $confirmedQuery = Enquiry::query()->accessibleBy()->groupBookings();
        $enquiriesQuery = Enquiry::query()->accessibleBy();

        $stats = [
            'hotels' => Hotel::query()->accessibleBy()->count(),
            'companies' => Company::query()->count(),
            'users' => User::query()->count(),
            'activeGroups' => Enquiry::query()->accessibleBy()->groupBookings()->count(),
            'arrivalsToday' => (clone $confirmedQuery)->whereDate('check_in', now()->toDateString())->count(),
            'departuresToday' => (clone $confirmedQuery)->whereDate('check_out', now()->toDateString())->count(),
            'openEnquiries' => (clone $enquiriesQuery)->openPipeline()->count(),
            'revenueYtd' => (clone $confirmedQuery)
                ->whereYear('check_in', now()->year)
                ->sum('grand_total'),
        ];

        $upcomingArrivals = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->groupBookings()
            ->whereDate('check_in', '>=', now()->toDateString())
            ->orderBy('check_in')
            ->limit(10)
            ->get();

        $upcomingDepartures = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->groupBookings()
            ->whereDate('check_out', '>=', now()->toDateString())
            ->orderBy('check_out')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('stats', 'upcomingArrivals', 'upcomingDepartures', 'currentHotel', 'allocatedHotels'));
    }
}
