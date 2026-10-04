@extends('layouts.app')

@section('title', 'Cancelled Inquiry')
@section('page', 'enquiries')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Cancelled Inquiry</li>
      </ol>
    </nav>
    <h1 class="page-title">Cancelled Inquiry</h1>
    <p class="page-subtitle">Enquiries cancelled from the edit screen, with the cancellation reason.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('cancelled-inquiries.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group search-input" style="min-width: 200px; max-width: 280px;">
          <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
          <input
            type="search"
            name="q"
            class="form-control border-start-0"
            placeholder="Search ref, group, client, email, mobile…"
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
        <select name="response" class="form-select form-select-sm select2" style="width:auto; min-width: 150px;">
          <option value="">All responses</option>
          <option value="awaiting" @selected(request('response') === 'awaiting')>Awaiting response</option>
          <option value="received" @selected(request('response') === 'received')>Response received</option>
        </select>
        <input type="date" name="enquiry_date_from" class="form-control form-control-sm" style="width:auto;" value="{{ request('enquiry_date_from') }}" title="Enquiry date from">
        <input type="date" name="enquiry_date_to" class="form-control form-control-sm" style="width:auto;" value="{{ request('enquiry_date_to') }}" title="Enquiry date to">
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->hasAny(['q', 'hotel_id', 'travel_agency_id', 'response', 'enquiry_date_from', 'enquiry_date_to']))
          <a href="{{ route('cancelled-inquiries.index') }}" class="btn btn-outline-danger btn-sm">
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
    @include('enquiries.partials.list-table', [
      'rows' => $enquiries,
      'variant' => 'enquiry',
      'showCancellationReason' => true,
      'defaultSort' => 'updated_at',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No cancelled inquiries found.',
    ])
    @include('partials.pagination-footer', ['paginator' => $enquiries])
  </div>
@endsection
