@extends('layouts.app')

@section('title', 'Calendar')
@section('page', 'calendar')

@section('content')
  @php
    $prev = $start->copy()->subMonth();
    $next = $start->copy()->addMonth();
    $daysInMonth = $start->daysInMonth;
    $startDow = (int) $start->copy()->startOfMonth()->dayOfWeekIso; // 1=Mon
    $byDate = [];
    foreach ($bookings as $booking) {
      if (! $booking->check_in) {
        continue;
      }
      $cursor = $booking->check_in->copy()->startOfDay();
      $endDate = ($booking->check_out ?? $booking->check_in)->copy()->startOfDay();
      while ($cursor->lte($endDate)) {
        if ($cursor->month === (int) $month && $cursor->year === (int) $year) {
          $byDate[$cursor->toDateString()][] = $booking;
        }
        $cursor->addDay();
      }
    }
  @endphp

  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Calendar</li>
        </ol>
      </nav>
      <h1 class="page-title">Calendar</h1>
      <p class="page-subtitle">Confirmed bookings for {{ $start->format('F Y') }}.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <a href="{{ route('calendar.index', array_filter(['month' => $prev->month, 'year' => $prev->year, 'hotel_id' => request('hotel_id')])) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i></a>
      <a href="{{ route('calendar.index', array_filter(['month' => $next->month, 'year' => $next->year, 'hotel_id' => request('hotel_id')])) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-right"></i></a>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('calendar.index') }}" class="d-flex gap-2">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto; min-width:180px">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
      </form>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-2 text-center small fw-semibold text-secondary mb-2">
        <div class="col">Mon</div><div class="col">Tue</div><div class="col">Wed</div><div class="col">Thu</div><div class="col">Fri</div><div class="col">Sat</div><div class="col">Sun</div>
      </div>
      <div class="row g-2">
        @for ($i = 1; $i < $startDow; $i++)
          <div class="col"></div>
        @endfor
        @for ($day = 1; $day <= $daysInMonth; $day++)
          @php
            $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayBookings = $byDate[$dateKey] ?? [];
          @endphp
          <div class="col">
            <div class="border rounded p-2 h-100" style="min-height: 90px;">
              <div class="fw-semibold mb-1">{{ $day }}</div>
              @foreach (array_slice($dayBookings, 0, 3) as $bk)
                <div class="small text-truncate"><a href="{{ route('enquiries.show', $bk) }}">{{ $bk->ref ?: $bk->group_name }}</a></div>
              @endforeach
              @if (count($dayBookings) > 3)
                <div class="small text-secondary">+{{ count($dayBookings) - 3 }} more</div>
              @endif
            </div>
          </div>
          @if (($startDow + $day - 1) % 7 === 0)
            </div><div class="row g-2 mt-0">
          @endif
        @endfor
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Group</th>
            <th>Hotel</th>
            <th>Arrival</th>
            <th>Departure</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($bookings as $booking)
            <tr>
              <td><a href="{{ route('enquiries.show', $booking) }}">{{ $booking->ref ?: '—' }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->check_in?->format('d M Y') ?? '—' }}</td>
              <td>{{ $booking->check_out?->format('d M Y') ?? '—' }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary py-4">No confirmed bookings this month.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
