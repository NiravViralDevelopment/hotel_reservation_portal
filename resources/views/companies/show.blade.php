@extends('layouts.app')

@section('title', $company->name)
@section('page', 'companies')

@section('content')
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
      <p class="page-subtitle">
        @if ($company->reg_number)
          Reg. {{ $company->reg_number }} ·
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
      <div class="card-header">Company details</div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Registration number</div>
          <div class="info-card-value">{{ $company->reg_number ?? '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">City</div>
          <div class="info-card-value">{{ $company->city ?? '—' }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Country</div>
          <div class="info-card-value">{{ $company->country ?? '—' }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Address</div>
          <div class="info-card-value">{{ $company->address ?? '—' }}</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Summary</div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Hotels</div>
          <div class="info-card-value">{{ $company->hotels->count() }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Contacts</div>
          <div class="info-card-value">{{ $company->contacts->count() }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Status</div>
          <div class="info-card-value"><x-badge-status :status="$company->status" /></div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Notes</div>
      <div class="card-body">
        <p class="mb-0 text-secondary">{{ $company->notes ?: 'No notes.' }}</p>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">Hotels ({{ $company->hotels->count() }})</div>
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

  <div class="card mb-4">
    <div class="card-header">Contacts ({{ $company->contacts->count() }})</div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Position</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($company->contacts as $contact)
            <tr>
              <td class="fw-semibold"><a href="{{ route('contacts.show', $contact) }}">{{ $contact->name }}</a></td>
              <td>{{ $contact->email ?? '—' }}</td>
              <td>{{ $contact->phone ?? '—' }}</td>
              <td>{{ $contact->position ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-secondary py-3">No contacts linked.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Recent group bookings ({{ $company->groupBookings->count() }})</div>
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
@endsection
