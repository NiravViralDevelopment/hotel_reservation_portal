@extends('layouts.app')

@section('title', 'Arrivals')
@section('page', 'arrivals')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Arrivals</li>
      </ol>
    </nav>
    <h1 class="page-title">Arrivals</h1>
    <p class="page-subtitle">Groups arriving on {{ \Illuminate\Support\Carbon::parse($date)->format('l, d M Y') }}.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('arrivals.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <input type="date" name="date" class="form-control form-control-sm" style="width:auto" value="{{ $date }}">
        <select name="hotel_id" class="form-select form-select-sm" style="width:auto">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected(request('hotel_id') == $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm">Apply</button>
      </form>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Block ID</th>
            <th>Group</th>
            <th>Hotel</th>
            <th>Agency</th>
            <th>Nights</th>
            <th>Rooms</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($bookings as $booking)
            <tr>
              <td><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->travelAgency?->name ?? '—' }}</td>
              <td>{{ $booking->nights ?? '—' }}</td>
              <td>{{ $booking->rooms ?? '—' }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">No arrivals for this date.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="table-footer"><span><strong>{{ $bookings->count() }}</strong> arrival(s)</span></div>
  </div>
@endsection
