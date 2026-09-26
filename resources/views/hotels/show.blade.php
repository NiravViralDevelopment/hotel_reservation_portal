@extends('layouts.app')

@section('title', $hotel->name)
@section('page', 'hotels')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Hotels</a></li>
          <li class="breadcrumb-item active">{{ $hotel->code }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $hotel->name }}</h1>
      <p class="page-subtitle">{{ $hotel->code }} · <x-badge-status :status="$hotel->status" /></p>
    </div>
    @can('update', $hotel)
      <a href="{{ route('hotels.edit', $hotel) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
    @endcan
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">Property</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Company</div><div class="info-card-value">@if($hotel->company)<a href="{{ route('companies.show', $hotel->company) }}">{{ $hotel->company->name }}</a>@else — @endif</div></div>
        <div class="info-card mb-3"><div class="info-card-label">City</div><div class="info-card-value">{{ $hotel->city }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Country</div><div class="info-card-value">{{ $hotel->country ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Rooms</div><div class="info-card-value">{{ $hotel->rooms ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Contact</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Manager</div><div class="info-card-value">{{ $hotel->managerUser?->name ?? $hotel->manager_name ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Phone</div><div class="info-card-value">{{ $hotel->phone ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Email</div><div class="info-card-value">@if($hotel->email)<a href="mailto:{{ $hotel->email }}">{{ $hotel->email }}</a>@else — @endif</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Notes</div>
      <div class="card-body"><p class="mb-0 text-secondary">{{ $hotel->notes ?: 'No notes.' }}</p></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Recent group bookings ({{ $hotel->groupBookings->count() }})</div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead><tr><th>Block ID</th><th>Group</th><th>Arrival</th><th>Revenue</th><th>Status</th></tr></thead>
        <tbody>
          @forelse ($hotel->groupBookings->take(15) as $booking)
            <tr>
              <td><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
              <td>£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-secondary py-3">No bookings.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
