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
    <p class="page-subtitle">Run operational and revenue reports.</p>
  </div>

  <div class="card">
    <div class="card-header">Report parameters</div>
    <div class="card-body">
      <form method="POST" action="{{ route('reports.run') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label for="report" class="form-label">Report <span class="text-danger">*</span></label>
            <select name="report" id="report" class="form-select select2 @error('report') is-invalid @enderror" required>
              <option value="bookings_by_hotel" @selected(old('report') === 'bookings_by_hotel')>Bookings by hotel</option>
              <option value="arrivals_summary" @selected(old('report') === 'arrivals_summary')>Arrivals summary</option>
              <option value="revenue_by_agency" @selected(old('report') === 'revenue_by_agency')>Revenue by agency</option>
            </select>
            @error('report')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="date_from" class="form-label">Date from</label>
            <input type="date" name="date_from" id="date_from" class="form-control @error('date_from') is-invalid @enderror" value="{{ old('date_from') }}">
            @error('date_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="date_to" class="form-label">Date to</label>
            <input type="date" name="date_to" id="date_to" class="form-control @error('date_to') is-invalid @enderror" value="{{ old('date_to') }}">
            @error('date_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="hotel_id" class="form-label">Hotel (optional)</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror">
              <option value="">All hotels</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id') == $hotel->id)>{{ $hotel->name }}</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-accent">Run report</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
