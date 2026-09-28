<?php

namespace App\Providers;

use App\Models\User;
use App\Support\HotelAccess;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::before(function (?User $user, string $ability) {
            if ($user === null) {
                return null;
            }

            if (method_exists($user, 'hasRole') && $user->hasRole('Administrator')) {
                return true;
            }

            return null;
        });

        View::composer('partials.header', function ($view) {
            if (! auth()->check()) {
                return;
            }

            $view->with([
                'accessibleHotels' => HotelAccess::hotels(),
                'currentHotelId' => HotelAccess::currentHotelId(),
            ]);
        });
    }
}
