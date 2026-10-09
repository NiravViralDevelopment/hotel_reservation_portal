<?php

use App\Http\Controllers\ArrivalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CancelledBookingController;
use App\Http\Controllers\CancelledInquiryController;
use App\Http\Controllers\CompanyContractController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartureController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\GroupBookingController;
use App\Http\Controllers\HotelContextController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatusMasterController;
use App\Http\Controllers\TravelAgencyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');


Route::get('/test-url', function () {
    return [
        'app_url' => config('app.url'),
        'login_url' => url('/login'),
        'route_login' => route('login'),
    ];
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::post('/hotel-context', [HotelContextController::class, 'switch'])->name('hotel-context.switch');
    Route::post('/hotel-context/clear', [HotelContextController::class, 'clear'])->name('hotel-context.clear');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('companies/{company}/contracts/{contract}/download', [CompanyContractController::class, 'download'])
        ->name('companies.contracts.download')
        ->scopeBindings();
    Route::resource('companies.contracts', CompanyContractController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->scoped();
    Route::resource('companies', CompanyController::class);
    Route::get('hotels/{hotel}/document', [HotelController::class, 'downloadDocument'])->name('hotels.document.download');
    Route::get('hotels/{hotel}/logo', [HotelController::class, 'viewLogo'])->name('hotels.logo');
    Route::resource('hotels', HotelController::class);
    Route::resource('travel-agencies', TravelAgencyController::class);

    // Contacts module removed
    Route::any('contacts/{any?}', fn () => redirect()->route('dashboard'))
        ->where('any', '.*')
        ->name('contacts.hidden');

    Route::get('enquiries/export', [EnquiryController::class, 'export'])->name('enquiries.export');
    Route::resource('enquiries', EnquiryController::class);
    Route::post('enquiries/{enquiry}/response', [EnquiryController::class, 'storeResponse'])->name('enquiries.response');
    Route::post('enquiries/{enquiry}/convert', [EnquiryController::class, 'convert'])->name('enquiries.convert');
    Route::get('enquiries/{enquiry}/group-booking', [EnquiryController::class, 'groupBooking'])->name('enquiries.group-booking');
    Route::post('enquiries/{enquiry}/group-booking', [EnquiryController::class, 'storeGroupBooking'])->name('enquiries.group-booking.store');
    Route::post('enquiries/{enquiry}/cancel', [EnquiryController::class, 'cancel'])->name('enquiries.cancel');
    Route::post('enquiries/{enquiry}/cancel-booking', [EnquiryController::class, 'cancelBooking'])->name('enquiries.cancel-booking');

    Route::get('cancelled-inquiries', [CancelledInquiryController::class, 'index'])->name('cancelled-inquiries.index');
    Route::get('cancelled-inquiries/export', [CancelledInquiryController::class, 'export'])->name('cancelled-inquiries.export');
    Route::get('cancelled-inquiries/{enquiry}/edit', [EnquiryController::class, 'editCancelled'])->name('cancelled-inquiries.edit');

    // Group bookings = confirmed enquiries (same enquiries table)
    Route::get('group-bookings', [GroupBookingController::class, 'index'])->name('group-bookings.index');
    Route::get('group-bookings/export', [GroupBookingController::class, 'export'])->name('group-bookings.export');
    Route::post('group-bookings/import', [GroupBookingController::class, 'import'])->name('group-bookings.import');
    Route::get('group-bookings/create', [GroupBookingController::class, 'create'])->name('group-bookings.create');
    Route::get('group-bookings/hotel-contract/{hotel}', [GroupBookingController::class, 'viewHotelContract'])->name('group-bookings.hotel-contract');
    Route::get('group-bookings/{enquiry}/contract', [GroupBookingController::class, 'contract'])->name('group-bookings.contract');
    Route::post('group-bookings/{enquiry}/contract', [GroupBookingController::class, 'updateContract'])->name('group-bookings.contract.update');
    Route::post('group-bookings/{enquiry}/contract/photos', [GroupBookingController::class, 'storeContractPhoto'])->name('group-bookings.contract.photos.store');
    Route::delete('group-bookings/{enquiry}/contract/photos/{photo}', [GroupBookingController::class, 'destroyContractPhoto'])->name('group-bookings.contract.photos.destroy');
    Route::get('group-bookings/{enquiry}/contract/photos/{photo}', [GroupBookingController::class, 'viewContractPhoto'])->name('group-bookings.contract.photos.show');
    Route::get('group-bookings/{enquiry}/contract/pdf', [GroupBookingController::class, 'previewContractPdf'])->name('group-bookings.contract.pdf');
    Route::get('group-bookings/{enquiry}/contract/hotel-preview', [GroupBookingController::class, 'previewHotelContract'])->name('group-bookings.contract.hotel-preview');
    Route::get('group-bookings/{enquiry}/contract/download', [GroupBookingController::class, 'downloadBookingContract'])->name('group-bookings.contract.download');
    Route::get('group-bookings/{enquiry}', [GroupBookingController::class, 'show'])->name('group-bookings.show');
    Route::get('group-bookings/{enquiry}/edit', [GroupBookingController::class, 'edit'])->name('group-bookings.edit');

    Route::get('cancelled-bookings', [CancelledBookingController::class, 'index'])->name('cancelled-bookings.index');
    Route::get('cancelled-bookings/export', [CancelledBookingController::class, 'export'])->name('cancelled-bookings.export');
    Route::get('cancelled-bookings/{enquiry}', [CancelledBookingController::class, 'show'])->name('cancelled-bookings.show');
    Route::get('arrivals', [ArrivalController::class, 'index'])->name('arrivals.index');
    Route::get('arrivals/{enquiry}', [ArrivalController::class, 'show'])->name('arrivals.show');
    Route::get('departures', [DepartureController::class, 'index'])->name('departures.index');
    Route::get('departures/{enquiry}', [DepartureController::class, 'show'])->name('departures.show');
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Documents module temporarily hidden
    Route::any('documents/{any?}', fn () => redirect()->route('dashboard'))
        ->where('any', '.*')
        ->name('documents.hidden');

    Route::get('revenue', [RevenueController::class, 'index'])->name('revenue.index');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/run', [ReportController::class, 'run'])->name('reports.run');
    Route::get('reports/{report}', [ReportController::class, 'module'])
        ->whereIn('report', ['group-bookings', 'enquiries', 'cancelled-bookings'])
        ->name('reports.module');

    Route::resource('status-masters', StatusMasterController::class)->except(['show']);

    Route::get('users/{user}/signature', [UserController::class, 'viewSignature'])->name('users.signature');
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Settings module temporarily hidden
    Route::any('settings/{any?}', fn () => redirect()->route('dashboard'))
        ->where('any', '.*')
        ->name('settings.hidden');
});
