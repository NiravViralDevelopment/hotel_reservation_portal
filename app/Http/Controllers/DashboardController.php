<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\TravelAgency;
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

        $hotelIds = HotelAccess::hotelIds();
        $hotelsQuery = Hotel::query()->accessibleBy()->where('status', 'active');
        $confirmedQuery = Enquiry::query()->accessibleBy()->groupBookings();
        $enquiriesQuery = Enquiry::query()->accessibleBy();

        $stats = [
            'hotels' => (clone $hotelsQuery)->count(),
            'companies' => Company::query()
                ->where('status', 'active')
                ->whereHas('hotels', fn ($q) => $q->whereIn('hotels.id', $hotelIds ?: [0]))
                ->count(),
            'travelAgencies' => TravelAgency::query()->where('status', 'active')->count(),
            'activeGroups' => (clone $confirmedQuery)->count(),
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

        return view('dashboard.index', compact('stats', 'upcomingArrivals', 'currentHotel', 'allocatedHotels'));
    }
}
