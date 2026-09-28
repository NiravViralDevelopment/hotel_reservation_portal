@extends('layouts.app')

@section('title', 'Cancelled bookings')
@section('page', 'cancelled-bookings')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Cancelled bookings</li>
      </ol>
    </nav>
    <h1 class="page-title">Cancelled bookings</h1>
    <p class="page-subtitle">Historical cancellations and revenue lost.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('cancelled-bookings.index') }}" class="d-flex gap-2 align-items-center">
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected(request('hotel_id') == $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
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
            <x-sortable-th column="block_id" label="Block ID" default="cancelled_at" default-dir="desc" />
            <x-sortable-th column="group_name" label="Group" default="cancelled_at" default-dir="desc" />
            <th>Hotel</th>
            <x-sortable-th column="cancelled_at" label="Cancelled" default="cancelled_at" default-dir="desc" />
            <x-sortable-th column="revenue" label="Revenue lost" default="cancelled_at" default-dir="desc" />
            <x-sortable-th column="reason" label="Reason" default="cancelled_at" default-dir="desc" />
          </tr>
        </thead>
        <tbody>
          @forelse ($bookings as $booking)
            <tr>
              <td class="fw-semibold"><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->cancelled_at?->format('d M Y') ?? $booking->cxl_date?->format('d M Y') ?? '—' }}</td>
              <td>£{{ number_format((float) ($booking->revenue_lost ?? 0), 2) }}</td>
              <td class="text-truncate" style="max-width:200px">{{ $booking->cancellation_reason ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary py-4">No cancelled bookings.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $bookings])
  </div>
@endsection
