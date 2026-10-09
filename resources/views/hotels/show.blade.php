@extends('layouts.app')

@section('title', $hotel->name)
@section('page', 'hotels')

@push('styles')
<style>
  .hotel-show-layout .hotel-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .hotel-show-layout .hotel-section-title i { color: var(--brand-accent); }
</style>
@endpush

@section('content')
<div class="hotel-show-layout">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Hotels</a></li>
          <li class="breadcrumb-item active">{{ $hotel->code }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $hotel->name }}</h1>
      <p class="page-subtitle mb-0">
        <span class="me-2">{{ $hotel->code }}</span>
        <x-badge-status :status="$hotel->status" />
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      @can('update', $hotel)
        <a href="{{ route('hotels.edit', $hotel) }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-building"></i> Hotel details</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Code</div>
          <div class="info-card-value">{{ $hotel->code }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Company</div>
          <div class="info-card-value">
            @if ($hotel->company)
              <a href="{{ route('companies.show', $hotel->company) }}">{{ $hotel->company->name }}</a>
            @else
              —
            @endif
          </div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Rooms</div>
          <div class="info-card-value">{{ $hotel->rooms ?? '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Status</div>
          <div class="info-card-value"><x-badge-status :status="$hotel->status" /></div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Logo</div>
          <div class="info-card-value">
            @if ($hotel->hasLogo())
              <img src="{{ route('hotels.logo', $hotel) }}" alt="{{ $hotel->name }} logo" style="max-height:64px;max-width:200px;object-fit:contain;">
            @else
              —
            @endif
          </div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Document</div>
          <div class="info-card-value">
            @if ($hotel->hasDocument())
              <a href="{{ route('hotels.document.download', $hotel) }}">
                <i class="bi bi-download"></i> {{ $hotel->document_original_name }}
              </a>
            @else
              —
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-geo-alt"></i> Location</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">City</div>
          <div class="info-card-value">{{ $hotel->city ?: '—' }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Country</div>
          <div class="info-card-value">{{ $hotel->country ?: '—' }}</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-telephone"></i> Contact</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Manager</div>
          <div class="info-card-value">{{ $hotel->manager_name ?: '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Phone</div>
          <div class="info-card-value">{{ $hotel->phone ?: '—' }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Email</div>
          <div class="info-card-value">{{ $hotel->email ?: '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="hotel-section-title"><i class="bi bi-sticky"></i> Notes</h2>
        </div>
        <div class="card-body">
          <p class="mb-0 text-secondary">{{ $hotel->notes ?: 'No notes.' }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="hotel-section-title"><i class="bi bi-people"></i> Assigned users</h2>
        </div>
        <div class="card-body">
          @forelse ($hotel->users as $user)
            <span class="badge bg-light text-dark border me-1 mb-1">{{ $user->name }}</span>
          @empty
            <p class="mb-0 text-secondary">No users assigned to this hotel.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h2 class="hotel-section-title"><i class="bi bi-calendar2-week"></i> Confirmed bookings</h2>
      <span class="badge bg-light text-dark border">{{ $hotel->confirmedBookings->count() }}</span>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Group</th>
            <th>Arrival</th>
            <th class="text-end">Grand total</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($hotel->confirmedBookings->take(15) as $booking)
            <tr>
              <td class="fw-semibold text-nowrap">
                <a href="{{ route('enquiries.show', $booking) }}">{{ $booking->ref ?: '—' }}</a>
              </td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->check_in?->format('d M Y') ?? '—' }}</td>
              <td class="text-end">£{{ number_format((float) ($booking->grand_total ?? 0), 2) }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
              <td class="text-end">
                <a href="{{ route('enquiries.show', $booking) }}" class="btn btn-sm btn-outline-secondary" title="View">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-secondary py-4">No confirmed bookings for this hotel yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
