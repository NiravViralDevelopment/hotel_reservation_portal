@extends('layouts.app')

@section('title', 'Report results')
@section('page', 'reports')

@section('content')
  @php
    $reportLabels = [
      'group_bookings' => 'Group bookings',
      'enquiries' => 'Enquiries',
      'cancelled_bookings' => 'Cancelled bookings',
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
        @if (!empty($validated['date_from']) && !empty($validated['date_to']))
          {{ \Illuminate\Support\Carbon::parse($validated['date_from'])->format('d M Y') }}
          —
          {{ \Illuminate\Support\Carbon::parse($validated['date_to'])->format('d M Y') }}
          · {{ $results->count() }} {{ \Illuminate\Support\Str::plural('record', $results->count()) }}
        @elseif (!empty($validated['date_from']) || !empty($validated['date_to']))
          {{ $validated['date_from'] ?? '…' }} — {{ $validated['date_to'] ?? '…' }}
        @else
          All dates
        @endif
      </p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">All reports</a>
  </div>

  @php
    $reportSlugs = [
      'group_bookings' => 'group-bookings',
      'enquiries' => 'enquiries',
      'cancelled_bookings' => 'cancelled-bookings',
    ];
  @endphp
  @if (isset($reportSlugs[$validated['report']]))
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('reports.module', $reportSlugs[$validated['report']]) }}" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="date_from" class="form-label">Start date</label>
            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $validated['date_from'] }}" required>
          </div>
          <div class="col-md-4">
            <label for="date_to" class="form-label">End date</label>
            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $validated['date_to'] }}" required>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-accent btn-sm">Update report</button>
          </div>
        </form>
      </div>
    </div>
  @endif

  <div class="card">
    <div class="table-wrapper">
      @if ($validated['report'] === 'group_bookings')
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Block ID</th>
              <th>Group</th>
              <th>Hotel</th>
              <th>Agency</th>
              <th>Arrival</th>
              <th class="text-end">Nights</th>
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
                <td class="text-end">{{ $booking->nights ?? '—' }}</td>
                <td class="text-end">£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
                <td><x-badge-status :status="$booking->status" /></td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center text-secondary py-4">No group bookings in these dates.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'enquiries')
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Ref</th>
              <th>Enquiry date</th>
              <th>Group</th>
              <th>Hotel</th>
              <th>Agency</th>
              <th class="text-end">Nights</th>
              <th class="text-end">Revenue</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($results as $enquiry)
              <tr>
                <td><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref }}</a></td>
                <td>{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}</td>
                <td>{{ $enquiry->group_name }}</td>
                <td>{{ $enquiry->hotel?->code ?? '—' }}</td>
                <td>{{ $enquiry->travelAgency?->name ?? '—' }}</td>
                <td class="text-end">{{ $enquiry->nights ?? '—' }}</td>
                <td class="text-end">£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</td>
                <td><x-badge-status :status="$enquiry->status" /></td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center text-secondary py-4">No enquiries in these dates.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'cancelled_bookings')
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Block ID</th>
              <th>Group</th>
              <th>Hotel</th>
              <th>Cancelled</th>
              <th class="text-end">Revenue lost</th>
              <th>Reason</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($results as $booking)
              <tr>
                <td><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
                <td>{{ $booking->group_name }}</td>
                <td>{{ $booking->hotel?->code ?? '—' }}</td>
                <td>{{ $booking->cancelled_at?->format('d M Y') ?? $booking->cxl_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-end">£{{ number_format((float) ($booking->revenue_lost ?? 0), 2) }}</td>
                <td>{{ $booking->cancellation_reason ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-secondary py-4">No cancelled bookings in these dates.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'bookings_by_hotel')
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
