@extends('layouts.app')

@section('title', 'Group bookings')
@section('page', 'group-bookings')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Group bookings</li>
        </ol>
      </nav>
      <h1 class="page-title">Group bookings</h1>
      <p class="page-subtitle">Active group blocks and contracts.</p>
    </div>
    @can('create', App\Models\GroupBooking::class)
      <a href="{{ route('group-bookings.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add booking</a>
    @endcan
  </div>

    <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('group-bookings.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="width: 240px; flex-shrink: 0;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            class="form-control"
            placeholder="Search block, group, email…"
            value="{{ request('q') }}"
          >
        </div>
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto; min-width: 140px;">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <select name="travel_agency_id" class="form-select form-select-sm select2" style="width:auto; min-width: 160px;">
          <option value="">All agencies</option>
          @foreach ($travelAgencies as $agency)
            <option value="{{ $agency->id }}" @selected((string) request('travel_agency_id') === (string) $agency->id)>{{ $agency->name }}</option>
          @endforeach
        </select>
        <select name="status" class="form-select form-select-sm select2" style="width:auto; min-width: 130px;">
          <option value="">All statuses</option>
          @foreach ($statuses as $status)
            @continue($status === \App\Enums\BookingStatus::Cancelled->value)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
          @endforeach
        </select>
        <input type="date" name="arrival_from" class="form-control form-control-sm" style="width:auto;" value="{{ request('arrival_from') }}" title="Arrival from">
        <input type="date" name="arrival_to" class="form-control form-control-sm" style="width:auto;" value="{{ request('arrival_to') }}" title="Arrival to">
        <button type="submit" class="btn btn-outline-secondary btn-sm flex-shrink-0"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->hasAny(['q', 'hotel_id', 'travel_agency_id', 'status', 'arrival_from', 'arrival_to']))
          <a href="{{ route('group-bookings.index') }}" class="btn btn-outline-danger btn-sm flex-shrink-0">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        @endif
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
            <x-sortable-th column="block_id" label="Block ID" default="arrival" />
            <x-sortable-th column="group_name" label="Group" default="arrival" />
            <th>Hotel</th>
            <th>Agency</th>
            <x-sortable-th column="arrival" label="Arrival" default="arrival" />
            <x-sortable-th column="nights" label="Nights" default="arrival" />
            <x-sortable-th column="revenue" label="Revenue" default="arrival" />
            <x-sortable-th column="status" label="Status" default="arrival" />
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($bookings as $booking)
            <tr>
              <td class="fw-semibold"><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->travelAgency?->name ?? '—' }}</td>
              <td>{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
              <td>{{ $booking->nights ?? '—' }}</td>
              <td>£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
              <td class="text-end">
                @can('update', $booking)
                  <a href="{{ route('group-bookings.edit', $booking) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-secondary py-4">No bookings found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $bookings])
  </div>
@endsection
