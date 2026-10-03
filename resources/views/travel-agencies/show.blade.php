@extends('layouts.app')

@section('title', $travelAgency->name)
@section('page', 'travel-agencies')

@push('styles')
<style>
  .agency-show-layout .agency-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .agency-show-layout .agency-section-title i { color: var(--brand-accent); }
</style>
@endpush

@section('content')
<div class="agency-show-layout">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('travel-agencies.index') }}">Travel agencies</a></li>
          <li class="breadcrumb-item active">{{ $travelAgency->code }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $travelAgency->name }}</h1>
      <p class="page-subtitle mb-0">
        <span class="me-2">{{ $travelAgency->code }}</span>
        <x-badge-status :status="$travelAgency->status" />
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('travel-agencies.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      @can('update', $travelAgency)
        <a href="{{ route('travel-agencies.edit', $travelAgency) }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">
        <h2 class="agency-section-title"><i class="bi bi-briefcase"></i> Agency details</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Code</div>
          <div class="info-card-value">{{ $travelAgency->code }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Name</div>
          <div class="info-card-value">{{ $travelAgency->name }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Status</div>
          <div class="info-card-value"><x-badge-status :status="$travelAgency->status" /></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="agency-section-title"><i class="bi bi-geo-alt"></i> Location</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">City</div>
          <div class="info-card-value">{{ $travelAgency->city ?: '—' }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Country</div>
          <div class="info-card-value">{{ $travelAgency->country ?: '—' }}</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="agency-section-title"><i class="bi bi-person-lines-fill"></i> Contact</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Contact name</div>
          <div class="info-card-value">{{ $travelAgency->contact_name ?: '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Email</div>
          <div class="info-card-value">{{ $travelAgency->email ?: '—' }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Phone</div>
          <div class="info-card-value">{{ $travelAgency->phone ?: '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="agency-section-title"><i class="bi bi-inbox"></i> Recent enquiries ({{ $travelAgency->enquiries->count() }})</h2>
        </div>
        <div class="table-wrapper">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Group</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($travelAgency->enquiries->take(10) as $enquiry)
                <tr>
                  <td class="fw-semibold"><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref }}</a></td>
                  <td>{{ $enquiry->group_name ?: '—' }}</td>
                  <td><x-badge-status :status="$enquiry->status" /></td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-secondary py-3">No enquiries.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="agency-section-title"><i class="bi bi-calendar2-check"></i> Confirmed bookings ({{ $travelAgency->confirmedBookings->count() }})</h2>
        </div>
        <div class="table-wrapper">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Ref</th>
                <th>Group</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($travelAgency->confirmedBookings->take(10) as $booking)
                <tr>
                  <td class="fw-semibold"><a href="{{ route('enquiries.show', $booking) }}">{{ $booking->ref ?: '—' }}</a></td>
                  <td>{{ $booking->group_name ?: '—' }}</td>
                  <td><x-badge-status :status="$booking->status" /></td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-secondary py-3">No confirmed bookings.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
