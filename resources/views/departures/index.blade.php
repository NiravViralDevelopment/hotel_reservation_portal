@extends('layouts.app')

@section('title', 'Departures')
@section('page', 'departures')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Departures</li>
      </ol>
    </nav>
    <h1 class="page-title">Departures</h1>
    <p class="page-subtitle">Groups whose departure date in {{ $month->format('F Y') }} is today or earlier.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('departures.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <label for="departure_month" class="form-label mb-0 small text-secondary">Month</label>
        <input type="month" name="month" id="departure_month" class="form-control form-control-sm" style="width:auto" value="{{ $monthValue }}">
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected(request('hotel_id') == $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm">Apply</button>
        @if (request('sort'))
          <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if (request('dir'))
          <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif
      </form>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <x-sortable-th column="check_out" label="Departure" default="check_out" />
            <x-sortable-th column="ref" label="Ref" default="check_out" />
            <x-sortable-th column="group_name" label="Group" default="check_out" />
            <th>Hotel</th>
            <th>Agency</th>
            <x-sortable-th column="nights" label="Nights" default="check_out" />
            <x-sortable-th column="status" label="Status" default="check_out" />
          </tr>
        </thead>
        <tbody>
          @forelse ($bookings as $booking)
            <tr>
              <td class="text-nowrap">{{ $booking->check_out?->format('d M Y') ?? '—' }}</td>
              <td><a href="{{ route('enquiries.show', $booking) }}">{{ $booking->ref ?: '—' }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->travelAgency?->name ?? '—' }}</td>
              <td>{{ $booking->nights ?? '—' }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">No groups have reached their departure date this month.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="table-footer"><span><strong>{{ $bookings->count() }}</strong> departure(s)</span></div>
  </div>
@endsection
