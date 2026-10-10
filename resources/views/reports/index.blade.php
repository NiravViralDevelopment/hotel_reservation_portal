@extends('layouts.app')

@section('title', 'Reports')
@section('page', 'reports')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Reports</li>
      </ol>
    </nav>
    <h1 class="page-title">Reports</h1>
    <p class="page-subtitle">Choose a report and date range to view or export records.</p>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><i class="bi bi-inbox me-2"></i>Enquiries</div>
        <div class="card-body">
          <p class="text-secondary small">Enquiries received in the dates you choose.</p>
          <form method="POST" action="{{ route('reports.run') }}" class="row g-3">
            @csrf
            <input type="hidden" name="report" value="enquiries">
            <div class="col-12">
              <label for="enquiries_from" class="form-label">Start date <span class="text-danger">*</span></label>
              <div class="date-placeholder-wrap">
                <input type="date" name="date_from" id="enquiries_from" class="form-control @error('date_from') is-invalid @enderror" value="{{ old('date_from', $defaultFrom) }}" placeholder="DD/MM/YYYY" required>
                <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
              </div>
            </div>
            <div class="col-12">
              <label for="enquiries_to" class="form-label">End date <span class="text-danger">*</span></label>
              <div class="date-placeholder-wrap">
                <input type="date" name="date_to" id="enquiries_to" class="form-control @error('date_to') is-invalid @enderror" value="{{ old('date_to', $defaultTo) }}" placeholder="DD/MM/YYYY" required>
                <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-accent btn-sm">Run report</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><i class="bi bi-journal-check me-2"></i>Group bookings</div>
        <div class="card-body">
          <p class="text-secondary small">Confirmed group bookings by arrival date.</p>
          <form method="POST" action="{{ route('reports.run') }}" class="row g-3">
            @csrf
            <input type="hidden" name="report" value="group_bookings">
            <div class="col-12">
              <label for="group_from" class="form-label">Start date <span class="text-danger">*</span></label>
              <div class="date-placeholder-wrap">
                <input type="date" name="date_from" id="group_from" class="form-control" value="{{ old('date_from', $defaultFrom) }}" placeholder="DD/MM/YYYY" required>
                <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
              </div>
            </div>
            <div class="col-12">
              <label for="group_to" class="form-label">End date <span class="text-danger">*</span></label>
              <div class="date-placeholder-wrap">
                <input type="date" name="date_to" id="group_to" class="form-control" value="{{ old('date_to', $defaultTo) }}" placeholder="DD/MM/YYYY" required>
                <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-accent btn-sm">Run report</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><i class="bi bi-x-circle me-2"></i>Cancelled bookings</div>
        <div class="card-body">
          <p class="text-secondary small">Cancelled bookings updated in the dates you choose.</p>
          <form method="POST" action="{{ route('reports.run') }}" class="row g-3">
            @csrf
            <input type="hidden" name="report" value="cancelled_bookings">
            <div class="col-12">
              <label for="cancelled_from" class="form-label">Start date <span class="text-danger">*</span></label>
              <div class="date-placeholder-wrap">
                <input type="date" name="date_from" id="cancelled_from" class="form-control" value="{{ old('date_from', $defaultFrom) }}" placeholder="DD/MM/YYYY" required>
                <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
              </div>
            </div>
            <div class="col-12">
              <label for="cancelled_to" class="form-label">End date <span class="text-danger">*</span></label>
              <div class="date-placeholder-wrap">
                <input type="date" name="date_to" id="cancelled_to" class="form-control" value="{{ old('date_to', $defaultTo) }}" placeholder="DD/MM/YYYY" required>
                <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-accent btn-sm">Run report</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
