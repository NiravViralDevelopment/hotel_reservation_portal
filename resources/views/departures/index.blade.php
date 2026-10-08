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
    <p class="page-subtitle">Groups whose departure date is today or earlier, filtered by that date.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      @include('stay-lists.filters', [
        'filterRoute' => 'departures.index',
        'dateInputPrefix' => 'departure',
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'hotels' => $hotels,
      ])
    </div>
    @include('enquiries.partials.list-table', [
      'rows' => $bookings,
      'variant' => 'enquiry',
      'defaultSort' => 'check_out',
      'defaultDir' => 'asc',
      'emptyMessage' => 'No groups have a departure date in this range that is today or earlier.',
      'readOnly' => true,
      'recordRoute' => 'departures.show',
    ])
    <div class="table-footer"><span><strong>{{ $bookings->count() }}</strong> departure(s)</span></div>
  </div>
@endsection
