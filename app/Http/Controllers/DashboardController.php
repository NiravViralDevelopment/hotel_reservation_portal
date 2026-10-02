<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Enquiry;
use App\Models\GroupBooking;
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

        // Non-admin users must pick a hotel first. Administrators can use all hotels.
        if ($currentHotel === null && ! HotelAccess::canAccessAllHotels()) {
            return view('dashboard.select-hotel', compact('allocatedHotels'));
        }

        $hotelIds = HotelAccess::hotelIds();
        $hotelsQuery = Hotel::query()->accessibleBy()->where('status', 'active');
        $bookingsQuery = GroupBooking::query()->accessibleBy();
        $enquiriesQuery = Enquiry::query()->accessibleBy();

        $stats = [
            'hotels' => (clone $hotelsQuery)->count(),
            'companies' => Company::query()
                ->where('status', 'active')
                ->whereHas('hotels', fn ($q) => $q->whereIn('hotels.id', $hotelIds ?: [0]))
                ->count(),
            'travelAgencies' => TravelAgency::query()->where('status', 'active')->count(),
            'activeGroups' => (clone $bookingsQuery)->active()->count(),
            'arrivalsToday' => (clone $bookingsQuery)->active()->arrivingOn(now()->toDateString())->count(),
            'departuresToday' => (clone $bookingsQuery)->active()->departingOn(now()->toDateString())->count(),
            'openEnquiries' => (clone $enquiriesQuery)
                ->whereNotIn('status', ['confirmed', 'lost', 'cancelled'])
                ->count(),
            'revenueYtd' => (clone $bookingsQuery)
                ->active()
                ->whereYear('arrival', now()->year)
                ->sum('revenue'),
        ];

        $upcomingArrivals = GroupBooking::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->active()
            ->whereDate('arrival', '>=', now()->toDateString())
            ->orderBy('arrival')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('stats', 'upcomingArrivals', 'currentHotel', 'allocatedHotels'));
    }
}
