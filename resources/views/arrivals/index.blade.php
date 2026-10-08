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
    <p class="page-subtitle">Groups still to depart, filtered by arrival date. They stay on this list until their departure date.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      @include('stay-lists.filters', [
        'filterRoute' => 'arrivals.index',
        'dateInputPrefix' => 'arrival',
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'hotels' => $hotels,
      ])
    </div>
    @include('enquiries.partials.list-table', [
      'rows' => $bookings,
      'variant' => 'enquiry',
      'defaultSort' => 'check_in',
      'defaultDir' => 'asc',
      'emptyMessage' => 'No groups arriving in this date range are still to depart.',
      'readOnly' => true,
      'recordRoute' => 'arrivals.show',
    ])
    <div class="table-footer"><span><strong>{{ $bookings->count() }}</strong> arrival(s)</span></div>
  </div>
@endsection
