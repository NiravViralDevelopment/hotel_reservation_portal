@extends('layouts.app')

@section('title', $enquiry->ref)
@section('page', 'enquiries')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
          <li class="breadcrumb-item active">{{ $enquiry->ref }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $enquiry->group_name }}</h1>
      <p class="page-subtitle">{{ $enquiry->ref }} · <x-badge-status :status="$enquiry->status" /></p>
    </div>
    <div class="d-flex gap-2">
      @can('convert', $enquiry)
        <form method="POST" action="{{ route('enquiries.convert', $enquiry) }}" onsubmit="return confirm('Convert this enquiry to a group booking?');">
          @csrf
          <button type="submit" class="btn btn-accent btn-sm"><i class="bi bi-arrow-right-circle"></i> Convert to booking</button>
        </form>
      @endcan
      @can('update', $enquiry)
        <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
      @endcan
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">Enquiry</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Date</div><div class="info-card-value">{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }} {{ $enquiry->day ? "({$enquiry->day})" : '' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Assigned to</div><div class="info-card-value">{{ $enquiry->assignedTo?->name ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Email</div><div class="info-card-value">{{ $enquiry->email ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Partners</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Agency</div><div class="info-card-value">{{ $enquiry->travelAgency?->name ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Hotel</div><div class="info-card-value">{{ $enquiry->hotel?->name ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Contact</div><div class="info-card-value">{{ $enquiry->contact?->name ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Commercial</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Nights</div><div class="info-card-value">{{ $enquiry->nights ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Rooms / night</div><div class="info-card-value">{{ $enquiry->rooms_per_night ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Total revenue</div><div class="info-card-value">£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</div></div>
      </div>
    </div>
  </div>

  @if ($enquiry->convertedBooking)
    <div class="alert alert-info">
      Converted to booking <a href="{{ route('group-bookings.show', $enquiry->convertedBooking) }}">{{ $enquiry->convertedBooking->block_id }}</a>.
    </div>
  @endif

  @if ($enquiry->remarks)
    <div class="card mb-4">
      <div class="card-header">Remarks</div>
      <div class="card-body"><p class="mb-0 text-secondary">{{ $enquiry->remarks }}</p></div>
    </div>
  @endif
@endsection
