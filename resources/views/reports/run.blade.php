@extends('layouts.app')

@section('title', 'Report results')
@section('page', 'reports')

@section('content')
  @php
    $reportLabels = [
      'bookings_by_hotel' => 'Bookings by hotel',
      'arrivals_summary' => 'Arrivals summary',
      'revenue_by_agency' => 'Revenue by agency',
    ];
    $title = $reportLabels[$validated['report']] ?? 'Report';
  @endphp
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
          <li class="breadcrumb-item active">Results</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $title }}</h1>
      <p class="page-subtitle">
        @if (!empty($validated['date_from']) || !empty($validated['date_to']))
          {{ $validated['date_from'] ?? '…' }} — {{ $validated['date_to'] ?? '…' }}
        @else
          All dates
        @endif
      </p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">New report</a>
  </div>

  <div class="card">
    <div class="table-wrapper">
      @if ($validated['report'] === 'bookings_by_hotel')
        <table class="table table-hover mb-0">
          <thead><tr><th>Hotel</th><th class="text-end">Bookings</th><th class="text-end">Revenue</th></tr></thead>
          <tbody>
            @forelse ($results as $row)
              @php $hotel = $hotels->firstWhere('id', $row->hotel_id); @endphp
              <tr>
                <td>{{ $hotel?->name ?? 'Hotel #'.$row->hotel_id }}</td>
                <td class="text-end">{{ $row->bookings }}</td>
                <td class="text-end">£{{ number_format((float) ($row->revenue ?? 0), 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-4">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'revenue_by_agency')
        <table class="table table-hover mb-0">
          <thead><tr><th>Agency ID</th><th class="text-end">Bookings</th><th class="text-end">Revenue</th></tr></thead>
          <tbody>
            @forelse ($results as $row)
              <tr>
                <td>{{ $row->travel_agency_id ?? '—' }}</td>
                <td class="text-end">{{ $row->bookings }}</td>
                <td class="text-end">£{{ number_format((float) ($row->revenue ?? 0), 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-4">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      @else
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Block ID</th>
              <th>Group</th>
              <th>Hotel</th>
              <th>Agency</th>
              <th>Arrival</th>
              <th class="text-end">Revenue</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($results as $booking)
              <tr>
                <td><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
                <td>{{ $booking->group_name }}</td>
                <td>{{ $booking->hotel?->code ?? '—' }}</td>
                <td>{{ $booking->travelAgency?->name ?? '—' }}</td>
                <td>{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
                <td class="text-end">£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
                <td><x-badge-status :status="$booking->status" /></td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-secondary py-4">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      @endif
    </div>
  </div>
@endsection
