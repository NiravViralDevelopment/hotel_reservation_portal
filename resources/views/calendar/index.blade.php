@extends('layouts.app')

@section('title', 'Calendar')
@section('page', 'calendar')

@section('content')
  @php
    $prev = $start->copy()->subMonth();
    $next = $start->copy()->addMonth();
  @endphp
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Calendar</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $start->format('F Y') }}</h1>
      <p class="page-subtitle">Group booking calendar overview.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <a href="{{ route('calendar.index', array_filter(['month' => $prev->month, 'year' => $prev->year, 'hotel_id' => request('hotel_id')])) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i></a>
      <a href="{{ route('calendar.index', array_filter(['month' => $next->month, 'year' => $next->year, 'hotel_id' => request('hotel_id')])) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-right"></i></a>
    </div>
  </div>

  <div class="card mb-4">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('calendar.index') }}" class="d-flex gap-2">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected(request('hotel_id') == $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
      </form>
    </div>
  </div>

  @php
    $monthStart = $start->copy()->startOfMonth();
    $daysInMonth = $monthStart->daysInMonth;
    $leadingEmpty = $monthStart->dayOfWeekIso - 1;
    $cells = array_merge(
      array_fill(0, $leadingEmpty, null),
      range(1, $daysInMonth),
      array_fill(0, (7 - (($leadingEmpty + $daysInMonth) % 7)) % 7, null)
    );
  @endphp

  <div class="card mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead class="table-light">
            <tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>
          </thead>
          <tbody>
            @foreach (array_chunk($cells, 7) as $week)
              <tr>
                @foreach ($week as $dayNum)
                  <td style="height:96px; vertical-align:top; width:14%">
                    @if ($dayNum)
                      @php
                        $current = $monthStart->copy()->day($dayNum);
                        $dayBookings = $bookings->filter(function ($bk) use ($current) {
                          if (! $bk->arrival) {
                            return false;
                          }
                          $end = ($bk->departure ?? $bk->arrival)->copy()->subDay();
                          return $current->between($bk->arrival->copy()->startOfDay(), $end->endOfDay());
                        });
                      @endphp
                      <div class="fw-semibold small mb-1">{{ $dayNum }}</div>
                      @foreach ($dayBookings->take(3) as $bk)
                        <div class="small text-truncate"><a href="{{ route('group-bookings.show', $bk) }}">{{ $bk->block_id }}</a></div>
                      @endforeach
                      @if ($dayBookings->count() > 3)
                        <div class="small text-muted">+{{ $dayBookings->count() - 3 }} more</div>
                      @endif
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Bookings this month ({{ $bookings->count() }})</div>
    <div class="table-wrapper">
      <table class="table table-sm table-hover mb-0">
        <thead><tr><th>Block ID</th><th>Group</th><th>Hotel</th><th>Arrival</th><th>Departure</th><th>Status</th></tr></thead>
        <tbody>
          @forelse ($bookings as $booking)
            <tr>
              <td><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
              <td>{{ $booking->departure?->format('d M Y') ?? '—' }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary py-3">No bookings in this month.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
