@extends('layouts.app')

@section('title', $contact->name)
@section('page', 'contacts')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
          <li class="breadcrumb-item active">{{ $contact->name }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $contact->name }}</h1>
      <p class="page-subtitle">{{ $contact->position ?? 'Contact' }}</p>
    </div>
    @can('update', $contact)
      <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
    @endcan
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><div class="info-card-label">Email</div><div class="info-card-value">@if($contact->email)<a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>@else — @endif</div></div>
        <div class="col-md-4"><div class="info-card-label">Phone</div><div class="info-card-value">{{ $contact->phone ?? '—' }}</div></div>
        <div class="col-md-4"><div class="info-card-label">Country</div><div class="info-card-value">{{ $contact->country ?? '—' }}</div></div>
        <div class="col-md-6"><div class="info-card-label">Company</div><div class="info-card-value">@if($contact->company)<a href="{{ route('companies.show', $contact->company) }}">{{ $contact->company->name }}</a>@else — @endif</div></div>
        <div class="col-md-6"><div class="info-card-label">Travel agency</div><div class="info-card-value">@if($contact->travelAgency)<a href="{{ route('travel-agencies.show', $contact->travelAgency) }}">{{ $contact->travelAgency->name }}</a>@else — @endif</div></div>
        <div class="col-12"><div class="info-card-label">Notes</div><div class="info-card-value text-secondary">{{ $contact->notes ?: '—' }}</div></div>
      </div>
    </div>
  </div>
@endsection
