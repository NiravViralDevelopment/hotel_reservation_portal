@extends('layouts.app')

@section('title', $company->name)
@section('page', 'companies')

@push('styles')
<style>
  .company-show-layout .company-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .company-show-layout .company-section-title i { color: var(--brand-accent); }
</style>
@endpush

@section('content')
<div class="company-show-layout">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
          <li class="breadcrumb-item active">{{ $company->name }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $company->name }}</h1>
      <p class="page-subtitle mb-0">
        @if ($company->reg_number)
          <span class="me-2">Reg. {{ $company->reg_number }}</span>
        @endif
        <x-badge-status :status="$company->status" />
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      @can('update', $company)
        <a href="{{ route('companies.edit', $company) }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">
        <h2 class="company-section-title"><i class="bi bi-building"></i> Company details</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Registration number</div>
          <div class="info-card-value">{{ $company->reg_number ?: '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">VAT number</div>
          <div class="info-card-value">{{ $company->vat_number ?: '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">City</div>
          <div class="info-card-value">{{ $company->city ?: '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Country</div>
          <div class="info-card-value">{{ $company->country ?: '—' }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Status</div>
          <div class="info-card-value"><x-badge-status :status="$company->status" /></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="company-section-title"><i class="bi bi-geo-alt"></i> Addresses</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Registered address</div>
          <div class="info-card-value">{{ $company->registered_address ?: ($company->address ?: '—') }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Trading address</div>
          <div class="info-card-value">{{ $company->trading_address ?: '—' }}</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="company-section-title"><i class="bi bi-bar-chart"></i> Summary</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Hotels</div>
          <div class="info-card-value">{{ $company->hotels->count() }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Group bookings</div>
          <div class="info-card-value">{{ $company->groupBookings->count() }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Notes</div>
          <div class="info-card-value text-secondary">{{ $company->notes ?: 'No notes.' }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h2 class="company-section-title"><i class="bi bi-houses"></i> Hotels ({{ $company->hotels->count() }})</h2>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>City</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($company->hotels as $hotel)
            <tr>
              <td class="fw-semibold"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->code }}</a></td>
              <td>{{ $hotel->name }}</td>
              <td>{{ $hotel->city ?? '—' }}</td>
              <td><x-badge-status :status="$hotel->status" /></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-secondary py-3">No hotels linked.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h2 class="company-section-title"><i class="bi bi-calendar2-check"></i> Recent group bookings ({{ $company->groupBookings->count() }})</h2>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Block ID</th>
            <th>Group</th>
            <th>Hotel</th>
            <th>Arrival</th>
            <th>Revenue</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($company->groupBookings as $booking)
            <tr>
              <td class="fw-semibold"><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
              <td>{{ $booking->group_name }}</td>
              <td>{{ $booking->hotel?->code ?? '—' }}</td>
              <td>{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
              <td>£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
              <td><x-badge-status :status="$booking->status" /></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary py-3">No group bookings.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
