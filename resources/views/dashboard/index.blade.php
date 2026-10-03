@extends('layouts.app')

@section('title', 'Dashboard')
@section('page', 'dashboard')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
      <h1 class="page-title">Dashboard</h1>
      <p class="page-subtitle">
        Welcome back, {{ auth()->user()->name }}.
        @if (! empty($currentHotel))
          Working in <strong>{{ $currentHotel->name }}</strong>.
        @else
          Viewing <strong>all hotels</strong>.
        @endif
      </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      @if (! empty($currentHotel))
        <form method="POST" action="{{ route('hotel-context.clear') }}" class="m-0">
          @csrf
          <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-building"></i> All hotels
          </button>
        </form>
      @elseif (! empty($allocatedHotels) && $allocatedHotels->isNotEmpty())
        <div class="dropdown">
          <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-building"></i> Filter by hotel
          </button>
          <ul class="dropdown-menu">
            @foreach ($allocatedHotels as $hotel)
              <li>
                <form method="POST" action="{{ route('hotel-context.switch') }}">
                  @csrf
                  <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                  <button type="submit" class="dropdown-item">{{ $hotel->name }}</button>
                </form>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
      <a href="{{ route('arrivals.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-in-right"></i> Today&apos;s arrivals</a>
      <a href="{{ route('group-bookings.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> New booking</a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Active Hotels</div>
        <div class="stat-card-value">{{ number_format($stats['hotels']) }}</div>
        <div class="stat-card-meta"><a href="{{ route('hotels.index') }}">View hotels</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Companies</div>
        <div class="stat-card-value">{{ number_format($stats['companies']) }}</div>
        <div class="stat-card-meta"><a href="{{ route('companies.index') }}">View companies</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Travel Agencies</div>
        <div class="stat-card-value">{{ number_format($stats['travelAgencies']) }}</div>
        <div class="stat-card-meta"><a href="{{ route('travel-agencies.index') }}">View agencies</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Active Groups</div>
        <div class="stat-card-value">{{ number_format($stats['activeGroups']) }}</div>
        <div class="stat-card-meta"><a href="{{ route('group-bookings.index') }}">View bookings</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Arrivals Today</div>
        <div class="stat-card-value">{{ number_format($stats['arrivalsToday']) }}</div>
        <div class="stat-card-meta"><a href="{{ route('arrivals.index') }}">Arrivals list</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Departures Today</div>
        <div class="stat-card-value">{{ number_format($stats['departuresToday']) }}</div>
        <div class="stat-card-meta"><a href="{{ route('departures.index') }}">Departures list</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Open Enquiries</div>
        <div class="stat-card-value">{{ number_format($stats['openEnquiries']) }}</div>
        <div class="stat-card-meta"><a href="{{ route('enquiries.index') }}">View enquiries</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Revenue YTD</div>
        <div class="stat-card-value">£{{ number_format($stats['revenueYtd'], 0) }}</div>
        <div class="stat-card-meta"><a href="{{ route('revenue.index') }}">Revenue report</a></div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3 px-4">
      <h2 class="h5 fw-semibold mb-0">Upcoming arrivals</h2>
      <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-secondary">Calendar</a>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Block ID</th>
            <th>Group</th>
            <th>Hotel</th>
            <th>Travel agency</th>
            <th>Arrival</th>
            <th class="text-center">Rooms</th>
            <th class="text-center">Pax</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($upcomingArrivals as $booking)
            <tr>
              <td><code>{{ $booking->block_id }}</code></td>
              <td class="fw-semibold">{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->name ?? '—' }}</td>
              <td>{{ $booking->travelAgency?->name ?? '—' }}</td>
              <td>{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
              <td class="text-center">{{ $booking->rooms ?? '—' }}</td>
              <td class="text-center">{{ $booking->pax ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('group-bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary" title="View">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-secondary py-4">No upcoming arrivals.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
