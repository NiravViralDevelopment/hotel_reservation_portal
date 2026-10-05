@extends('layouts.app')

@section('title', 'Enquiries')
@section('page', 'enquiries')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Enquiries</li>
        </ol>
      </nav>
      <h1 class="page-title">Enquiries</h1>
      <p class="page-subtitle">Watch Arrival, Option Date, and CXL Due Date highlights so deadlines are not missed.</p>
    </div>
    @can('create', App\Models\Enquiry::class)
      <a href="{{ route('enquiries.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add enquiry</a>
    @endcan
  </div>

  <div class="enquiry-reminder-grid">
    <div class="enquiry-reminder-card is-info">
      <div class="label">Arrivals (next 7 days)</div>
      <div class="value">{{ number_format($reminders['arrivals'] ?? 0) }}</div>
      <div class="meta">Highlighted in the Arrival Date column</div>
    </div>
    <div class="enquiry-reminder-card {{ ($reminders['options_overdue'] ?? 0) > 0 ? 'is-danger' : 'is-warning' }}">
      <div class="label">Option dates due</div>
      <div class="value">{{ number_format($reminders['options'] ?? 0) }}</div>
      <div class="meta">
        @if (($reminders['options_overdue'] ?? 0) > 0)
          {{ $reminders['options_overdue'] }} overdue
        @else
          Within 7 days / overdue
        @endif
      </div>
    </div>
    <div class="enquiry-reminder-card {{ ($reminders['cxl_overdue'] ?? 0) > 0 ? 'is-danger' : 'is-warning' }}">
      <div class="label">CXL due dates</div>
      <div class="value">{{ number_format($reminders['cxl'] ?? 0) }}</div>
      <div class="meta">
        @if (($reminders['cxl_overdue'] ?? 0) > 0)
          {{ $reminders['cxl_overdue'] }} overdue
        @else
          Within 7 days / overdue
        @endif
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('enquiries.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
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
        <select name="status" class="form-select form-select-sm select2" style="width:auto; min-width: 130px;">
          <option value="">All statuses</option>
          @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
          @endforeach
        </select>
        <input type="date" name="enquiry_date_from" class="form-control form-control-sm" style="width:auto;" value="{{ $enquiryDateFrom }}" title="Enquiry date from">
        <input type="date" name="enquiry_date_to" class="form-control form-control-sm" style="width:auto;" value="{{ $enquiryDateTo }}" title="Enquiry date to">
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->hasAny(['q', 'hotel_id', 'travel_agency_id', 'status', 'enquiry_date_from', 'enquiry_date_to']))
          <a href="{{ route('enquiries.index') }}" class="btn btn-outline-danger btn-sm">
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
      'defaultSort' => 'enquiry_date',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No enquiries found.',
    ])
    @include('partials.pagination-footer', ['paginator' => $enquiries])
  </div>
@endsection
