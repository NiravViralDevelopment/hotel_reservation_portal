@extends('layouts.app')

@section('title', 'Cancelled bookings')
@section('page', 'cancelled-bookings')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Cancelled bookings</li>
        </ol>
      </nav>
      <h1 class="page-title">Cancelled bookings</h1>
      <p class="page-subtitle">Cancelled group bookings, with the cancellation reason.</p>
    </div>
    <a
      href="{{ route('cancelled-bookings.export', request()->only(['q', 'hotel_id', 'travel_agency_id', 'month', 'sort', 'dir'])) }}"
      class="btn btn-outline-secondary btn-sm"
      title="Download the filtered list as Excel"
    >
      <i class="bi bi-download"></i> Export Excel
    </a>
  </div>

  <div class="card">
    <div class="table-toolbar">
      @include('stay-lists.filters', [
        'filterRoute' => 'cancelled-bookings.index',
        'monthInputId' => 'cancelled_booking_month',
        'monthValue' => $monthValue,
        'hotels' => $hotels,
        'travelAgencies' => $travelAgencies,
      ])
    </div>

    @include('group-bookings.partials.list-table', [
      'rows' => $bookings,
      'showCancellationReason' => true,
      'defaultSort' => 'updated_at',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No cancelled bookings found.',
    ])

    @include('partials.pagination-footer', ['paginator' => $bookings])
  </div>
@endsection
