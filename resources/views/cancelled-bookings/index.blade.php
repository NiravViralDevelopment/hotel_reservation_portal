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
    <p class="page-subtitle">Cancelled group bookings, with the cancellation reason.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('cancelled-bookings.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto; min-width: 140px;">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm flex-shrink-0"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->filled('hotel_id'))
          <a href="{{ route('cancelled-bookings.index') }}" class="btn btn-outline-danger btn-sm flex-shrink-0">
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
