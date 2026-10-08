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
    <p class="page-subtitle">Groups arriving in {{ $month->format('F Y') }}. They stay on this list until their departure date.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('arrivals.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <label for="arrival_month" class="form-label mb-0 small text-secondary">Month</label>
        <input type="month" name="month" id="arrival_month" class="form-control form-control-sm" style="width:auto" value="{{ $monthValue }}">
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
            <x-sortable-th column="check_in" label="Arrival" default="check_in" />
            <x-sortable-th column="ref" label="Ref" default="check_in" />
            <x-sortable-th column="group_name" label="Group" default="check_in" />
            <th>Hotel</th>
            <th>Agency</th>
            <x-sortable-th column="nights" label="Nights" default="check_in" />
            <th>Pax</th>
            <x-sortable-th column="status" label="Status" default="check_in" />
          </tr>
        </thead>
        <tbody>
          @forelse ($bookings as $booking)
            <tr>
              <td class="text-nowrap">{{ $booking->check_in?->format('d M Y') ?? '—' }}</td>
              <td><a href="{{ route('enquiries.show', $booking) }}">{{ $booking->ref ?: '—' }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->travelAgency?->name ?? '—' }}</td>
              <td>{{ $booking->nights ?? '—' }}</td>
              <td>{{ $booking->total_pax ?? '—' }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-secondary py-4">No groups arriving this month are still to depart.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="table-footer"><span><strong>{{ $bookings->count() }}</strong> arrival(s)</span></div>
  </div>
@endsection
