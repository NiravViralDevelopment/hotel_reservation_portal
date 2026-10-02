<?php

namespace App\Http\Middleware;

use App\Support\HotelAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHotelSelected
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        // Administrator is not forced to select a hotel first.
        if (HotelAccess::canAccessAllHotels()) {
            return $next($request);
        }

        if (HotelAccess::hasCurrentHotel()) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        return redirect()
            ->route('dashboard')
            ->with('warning', 'Please select a hotel first.');
    }

    private function shouldBypass(Request $request): bool
    {
        return $request->routeIs(
            'dashboard',
            'hotel-context.*',
            'profile.*',
            'logout',
            'login',
        );
    }
}
