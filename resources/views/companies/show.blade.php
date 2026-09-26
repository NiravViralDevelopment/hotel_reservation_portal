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
      <p class="page-subtitle"><x-badge-status :status="$company->status" /></p>
    </div>
    @can('update', $company)
      <a href="{{ route('companies.edit', $company) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
    @endcan
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">Company details</div>
        <div class="card-body">
          <div class="info-card mb-3"><div class="info-card-label">Reg. number</div><div class="info-card-value">{{ $company->reg_number ?? '—' }}</div></div>
          <div class="info-card mb-3"><div class="info-card-label">City</div><div class="info-card-value">{{ $company->city ?? '—' }}</div></div>
          <div class="info-card mb-3"><div class="info-card-label">Country</div><div class="info-card-value">{{ $company->country ?? '—' }}</div></div>
          <div class="info-card"><div class="info-card-label">Address</div><div class="info-card-value">{{ $company->address ?? '—' }}</div></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">Notes</div>
        <div class="card-body">
          <p class="mb-0 text-secondary">{{ $company->notes ?: 'No notes.' }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">Hotels ({{ $company->hotels->count() }})</div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead><tr><th>Code</th><th>Name</th><th>City</th><th>Status</th></tr></thead>
        <tbody>
          @forelse ($company->hotels as $hotel)
            <tr>
              <td><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->code }}</a></td>
              <td>{{ $hotel->name }}</td>
              <td>{{ $hotel->city }}</td>
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
    <div class="card-header">Contacts ({{ $company->contacts->count() }})</div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th></tr></thead>
        <tbody>
          @forelse ($company->contacts as $contact)
            <tr>
              <td><a href="{{ route('contacts.show', $contact) }}">{{ $contact->name }}</a></td>
              <td>{{ $contact->email ?? '—' }}</td>
              <td>{{ $contact->phone ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-secondary py-3">No contacts linked.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
