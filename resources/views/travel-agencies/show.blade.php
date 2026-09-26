@extends('layouts.app')

@section('title', $travelAgency->name)
@section('page', 'travel-agencies')

@section('content')
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
      <p class="page-subtitle"><x-badge-status :status="$travelAgency->status" /></p>
    </div>
    @can('update', $travelAgency)
      <a href="{{ route('travel-agencies.edit', $travelAgency) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
    @endcan
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><div class="info-card-label">Contact</div><div class="info-card-value">{{ $travelAgency->contact_name ?? '—' }}</div></div>
        <div class="col-md-4"><div class="info-card-label">Email</div><div class="info-card-value">{{ $travelAgency->email ?? '—' }}</div></div>
        <div class="col-md-4"><div class="info-card-label">Phone</div><div class="info-card-value">{{ $travelAgency->phone ?? '—' }}</div></div>
        <div class="col-md-6"><div class="info-card-label">City</div><div class="info-card-value">{{ $travelAgency->city ?? '—' }}</div></div>
        <div class="col-md-6"><div class="info-card-label">Country</div><div class="info-card-value">{{ $travelAgency->country ?? '—' }}</div></div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Contacts</div>
        <div class="table-wrapper">
          <table class="table table-sm table-hover mb-0">
            <tbody>
              @forelse ($travelAgency->contacts as $contact)
                <tr><td><a href="{{ route('contacts.show', $contact) }}">{{ $contact->name }}</a></td></tr>
              @empty
                <tr><td class="text-secondary py-3">No contacts.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Recent enquiries</div>
        <div class="table-wrapper">
          <table class="table table-sm table-hover mb-0">
            <tbody>
              @forelse ($travelAgency->enquiries->take(10) as $enquiry)
                <tr>
                  <td><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref }}</a></td>
                  <td><x-badge-status :status="$enquiry->status" /></td>
                </tr>
              @empty
                <tr><td class="text-secondary py-3">No enquiries.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
