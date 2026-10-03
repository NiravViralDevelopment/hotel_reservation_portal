@extends('layouts.app')

@section('title', $enquiry->ref)
@section('page', 'enquiries')

@push('styles')
<style>
  .enq-show .enq-hero {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: var(--card-radius);
    padding: 1.25rem 1.5rem;
    box-shadow: var(--shadow-sm);
  }
  .enq-show .enq-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1rem;
    align-items: center;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }
  .enq-show .enq-meta a { text-decoration: none; }
  .enq-show .enq-meta-sep { color: var(--border-color); }
  .enq-show .enq-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
  }
  @media (max-width: 991.98px) {
    .enq-show .enq-stat-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 575.98px) {
    .enq-show .enq-stat-grid { grid-template-columns: 1fr; }
  }
  .enq-show .enq-stat .stat-card-value { font-size: 1.35rem; }
  .enq-show .enq-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .enq-show .enq-section-title i { color: var(--brand-accent); }
  .enq-show .enq-dl {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.85rem 1.25rem;
  }
  @media (max-width: 575.98px) {
    .enq-show .enq-dl { grid-template-columns: 1fr; }
  }
  .enq-show .enq-dl-item { min-width: 0; }
  .enq-show .enq-dl-label {
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 0.2rem;
  }
  .enq-show .enq-dl-value {
    font-weight: 600;
    color: var(--text-primary);
    word-break: break-word;
  }
  .enq-show .enq-dl-value.muted { font-weight: 500; color: var(--text-secondary); }
  .enq-show .room-tile {
    background: var(--bg-body);
    border-radius: 0.5rem;
    padding: 1rem;
    height: 100%;
    border: 1px solid transparent;
  }
  .enq-show .room-tile-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--text-secondary);
    margin-bottom: 0.35rem;
  }
  .enq-show .room-tile-value { font-size: 1.05rem; font-weight: 700; }
  .enq-show .room-tile-sub { font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem; }
  .enq-show .response-timeline { list-style: none; margin: 0; padding: 0; }
  .enq-show .response-timeline-item {
    position: relative;
    padding: 0 0 1.25rem 1.5rem;
    border-left: 2px solid var(--border-color);
  }
  .enq-show .response-timeline-item:last-child { padding-bottom: 0; border-left-color: transparent; }
  .enq-show .response-timeline-item::before {
    content: '';
    position: absolute;
    left: -0.4rem;
    top: 0.25rem;
    width: 0.7rem;
    height: 0.7rem;
    border-radius: 50%;
    background: var(--brand-accent);
    border: 2px solid var(--bg-surface);
    box-shadow: 0 0 0 1px var(--brand-accent);
  }
  .enq-show .response-date { font-weight: 700; color: var(--text-primary); }
  .enq-show .response-meta { font-size: 0.8125rem; color: var(--text-secondary); }
  .enq-show .response-body {
    margin-top: 0.5rem;
    padding: 0.85rem 1rem;
    background: var(--bg-body);
    border-radius: 0.5rem;
    color: var(--text-primary);
    white-space: pre-wrap;
  }
  .enq-show .linked-booking {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem 1rem;
    padding: 0.9rem 1.15rem;
    border-radius: var(--card-radius);
    border: 1px solid rgba(13, 148, 136, 0.25);
    background: rgba(13, 148, 136, 0.08);
  }
  .enq-show .awaiting-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #b45309;
    background: rgba(201, 162, 39, 0.15);
    border-radius: 999px;
    padding: 0.25rem 0.7rem;
  }
</style>
@endpush

@section('content')
@php
  $statusLabel = ucwords(str_replace('_', ' ', (string) $enquiry->status));
  $hasResponse = $enquiry->responses->isNotEmpty() || filled($enquiry->client_response);
  $singleTotal = (float) ($enquiry->single_rooms ?? 0) * (float) ($enquiry->single_rate ?? 0);
  $doubleTotal = (float) ($enquiry->double_rooms ?? 0) * (float) ($enquiry->double_rate ?? 0);
  $tripleTotal = (float) ($enquiry->triple_rooms ?? 0) * (float) ($enquiry->triple_rate ?? 0);
@endphp

<div class="enq-show">
  {{-- Header --}}
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
          <li class="breadcrumb-item active">{{ $enquiry->ref }}</li>
        </ol>
      </nav>
      <h1 class="page-title mb-2">{{ $enquiry->group_name }}</h1>
      <div class="enq-meta">
        <span class="fw-semibold text-primary">{{ $enquiry->ref }}</span>
        <span class="enq-meta-sep">·</span>
        <x-badge-status :status="$enquiry->status" />
        @if ($enquiry->year)
          <span class="enq-meta-sep">·</span>
          <span>{{ $enquiry->year }}</span>
        @endif
        @if (! $hasResponse)
          <span class="awaiting-pill"><i class="bi bi-hourglass-split"></i> Awaiting remark</span>
        @endif
      </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('enquiries.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      @can('convert', $enquiry)
        <form method="POST" action="{{ route('enquiries.convert', $enquiry) }}" data-confirm-title="Confirm enquiry" data-confirm="{{ sprintf('Confirm enquiry "%s" and create a group booking?', $enquiry->group_name ?? 'this enquiry') }}" data-confirm-button="Confirm booking" data-confirm-variant="primary" data-confirm-icon="bi-check-circle">
          @csrf
          <button type="submit" class="btn btn-accent btn-sm">
            <i class="bi bi-check2-circle"></i> Confirm to booking
          </button>
        </form>
      @endcan
      @can('update', $enquiry)
        <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="#client-response" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-chat-left-text"></i> Add remark
        </a>
      @endcan
      @can('delete', $enquiry)
        <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}" data-confirm-title="Delete enquiry" data-confirm="{{ sprintf("Are you sure you want to delete enquiry \"%s\"?\n\nThis will permanently remove it and cannot be undone.", $enquiry->group_name ?? 'this enquiry') }}" data-confirm-button="Delete">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
            <i class="bi bi-trash"></i>
          </button>
        </form>
      @endcan
    </div>
  </div>

  @if ($enquiry->convertedBooking)
    <div class="linked-booking mb-4">
      <i class="bi bi-link-45deg fs-5 text-success"></i>
      <div class="flex-grow-1">
        <div class="fw-semibold">Converted to group booking</div>
        <div class="small text-secondary">This enquiry is linked to an active booking record.</div>
      </div>
      <a href="{{ route('group-bookings.show', $enquiry->convertedBooking) }}" class="btn btn-sm btn-accent">
        Open {{ $enquiry->convertedBooking->block_id }}
      </a>
      @if (($enquiry->convertedBooking->status?->value ?? $enquiry->convertedBooking->status) === 'Cancelled')
        <a href="{{ route('cancelled-bookings.index') }}" class="btn btn-sm btn-outline-secondary">Cancelled bookings</a>
      @endif
    </div>
  @endif

  {{-- Key figures --}}
  <div class="enq-stat-grid mb-4">
    <div class="stat-card enq-stat">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="stat-card-label">Nights</div>
        <div class="stat-card-icon success"><i class="bi bi-moon-stars"></i></div>
      </div>
      <div class="stat-card-value">{{ $enquiry->nights ?? '—' }}</div>
      <div class="small text-secondary mt-1">
        @if ($enquiry->check_in && $enquiry->check_out)
          {{ $enquiry->check_in->format('d M') }} → {{ $enquiry->check_out->format('d M Y') }}
        @else
          Stay dates not set
        @endif
      </div>
    </div>
    <div class="stat-card enq-stat">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="stat-card-label">Rooms / night</div>
        <div class="stat-card-icon primary"><i class="bi bi-door-closed"></i></div>
      </div>
      <div class="stat-card-value">{{ $enquiry->rooms_per_night ?? '—' }}</div>
      <div class="small text-secondary mt-1">
        {{ (int) ($enquiry->single_rooms ?? 0) }}S · {{ (int) ($enquiry->double_rooms ?? 0) }}D · {{ (int) ($enquiry->triple_rooms ?? 0) }}T
      </div>
    </div>
    <div class="stat-card enq-stat">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="stat-card-label">Total revenue</div>
        <div class="stat-card-icon warning"><i class="bi bi-currency-pound"></i></div>
      </div>
      <div class="stat-card-value">£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</div>
      <div class="small text-secondary mt-1">Status: {{ $statusLabel }}</div>
    </div>
    <div class="stat-card enq-stat">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="stat-card-label">Tax</div>
        <div class="stat-card-icon info"><i class="bi bi-percent"></i></div>
      </div>
      <div class="stat-card-value">
        @if ($enquiry->has_tax)
          £{{ number_format((float) ($enquiry->tax_revenue ?? 0), 2) }}
        @else
          —
        @endif
      </div>
      <div class="small text-secondary mt-1">
        {{ $enquiry->has_tax ? ((float) ($enquiry->tax_percentage ?? 0)).'% applied' : 'No tax on this enquiry' }}
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    {{-- Overview --}}
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="enq-section-title"><i class="bi bi-file-earmark-text"></i> Enquiry overview</h2>
        </div>
        <div class="card-body">
          <div class="enq-dl">
            <div class="enq-dl-item">
              <div class="enq-dl-label">Enquiry date</div>
              <div class="enq-dl-value">
                {{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}
                @if ($enquiry->day)
                  <span class="muted">({{ $enquiry->day }})</span>
                @endif
              </div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Response date</div>
              <div class="enq-dl-value">{{ $enquiry->response_date?->format('d M Y') ?? '—' }}</div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Year</div>
              <div class="enq-dl-value">{{ $enquiry->year ?? '—' }}</div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Email</div>
              <div class="enq-dl-value">
                @if ($enquiry->email)
                  <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>
                @else
                  <span class="muted">—</span>
                @endif
              </div>
            </div>
          </div>
          @if ($enquiry->client_response)
            <hr class="my-3">
            <div class="enq-dl-label">Latest remark</div>
            <div class="response-body mt-2 mb-0">{{ $enquiry->client_response }}</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Partners --}}
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="enq-section-title"><i class="bi bi-people"></i> Partners</h2>
        </div>
        <div class="card-body">
          <div class="enq-dl" style="grid-template-columns: 1fr;">
            <div class="enq-dl-item">
              <div class="enq-dl-label">Travel agency</div>
              <div class="enq-dl-value">
                @if ($enquiry->travelAgency)
                  <a href="{{ route('travel-agencies.show', $enquiry->travelAgency) }}">{{ $enquiry->travelAgency->name }}</a>
                  @if ($enquiry->travelAgency->code)
                    <div class="small text-secondary fw-normal">{{ $enquiry->travelAgency->code }}</div>
                  @endif
                @else
                  <span class="muted">—</span>
                @endif
              </div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Hotel</div>
              <div class="enq-dl-value">
                @if ($enquiry->hotel)
                  <a href="{{ route('hotels.show', $enquiry->hotel) }}">{{ $enquiry->hotel->name }}</a>
                  @if ($enquiry->hotel->code)
                    <div class="small text-secondary fw-normal">{{ $enquiry->hotel->code }}</div>
                  @endif
                @else
                  <span class="muted">—</span>
                @endif
              </div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Contact</div>
              <div class="enq-dl-value">
                @if ($enquiry->contact)
                  <a href="{{ route('contacts.show', $enquiry->contact) }}">{{ $enquiry->contact->name }}</a>
                @else
                  <span class="muted">—</span>
                @endif
              </div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Assigned to</div>
              <div class="enq-dl-value {{ $enquiry->assignedTo ? '' : 'muted' }}">
                {{ $enquiry->assignedTo?->name ?? '—' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Stay --}}
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="enq-section-title"><i class="bi bi-calendar2-week"></i> Stay details</h2>
        </div>
        <div class="card-body">
          <div class="enq-dl">
            <div class="enq-dl-item">
              <div class="enq-dl-label">Check-in</div>
              <div class="enq-dl-value">{{ $enquiry->check_in?->format('d M Y') ?? '—' }}</div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Day</div>
              <div class="enq-dl-value">{{ $enquiry->check_in_day ?: '—' }}</div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Check-out</div>
              <div class="enq-dl-value">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Nights</div>
              <div class="enq-dl-value">{{ $enquiry->nights ?? '—' }}</div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Rooms / night</div>
              <div class="enq-dl-value">{{ $enquiry->rooms_per_night ?? '—' }}</div>
            </div>
            <div class="enq-dl-item">
              <div class="enq-dl-label">Total revenue</div>
              <div class="enq-dl-value">£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Room breakdown --}}
  <div class="card mb-4">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-grid-3x3-gap"></i> Room breakdown</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="room-tile">
            <div class="room-tile-label">Single</div>
            <div class="room-tile-value">{{ (int) ($enquiry->single_rooms ?? 0) }} rooms × £{{ number_format((float) ($enquiry->single_rate ?? 0), 2) }}</div>
            <div class="room-tile-sub">Subtotal £{{ number_format($singleTotal, 2) }} / night</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="room-tile">
            <div class="room-tile-label">Double</div>
            <div class="room-tile-value">{{ (int) ($enquiry->double_rooms ?? 0) }} rooms × £{{ number_format((float) ($enquiry->double_rate ?? 0), 2) }}</div>
            <div class="room-tile-sub">Subtotal £{{ number_format($doubleTotal, 2) }} / night</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="room-tile">
            <div class="room-tile-label">Triple</div>
            <div class="room-tile-value">{{ (int) ($enquiry->triple_rooms ?? 0) }} rooms × £{{ number_format((float) ($enquiry->triple_rate ?? 0), 2) }}</div>
            <div class="room-tile-sub">Subtotal £{{ number_format($tripleTotal, 2) }} / night</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($enquiry->remarks)
    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enq-section-title"><i class="bi bi-chat-quote"></i> Remarks</h2>
      </div>
      <div class="card-body">
        <p class="mb-0" style="white-space: pre-wrap;">{{ $enquiry->remarks }}</p>
      </div>
    </div>
  @endif

  <div class="row g-4 mb-4">
    {{-- Add response --}}
    @can('update', $enquiry)
      <div class="col-lg-5">
        <div class="card h-100" id="client-response">
          <div class="card-header">
            <h2 class="enq-section-title"><i class="bi bi-reply"></i> Add remark</h2>
          </div>
          <div class="card-body">
            <p class="small text-secondary mb-3">
              Record a remark for this enquiry. Each save is kept in the history for <strong>{{ $enquiry->group_name }}</strong> ({{ $enquiry->ref }}).
            </p>
            <form method="POST" action="{{ route('enquiries.response', $enquiry) }}" class="enquiry-form" novalidate>
              @csrf
              <div class="mb-3">
                <label for="response_date" class="form-label">Response date <span class="text-danger">*</span></label>
                <input
                  type="date"
                  name="response_date"
                  id="response_date"
                  class="form-control @error('response_date') is-invalid @enderror"
                  value="{{ old('response_date') }}"
                  @if ($enquiry->enquiry_date)
                    min="{{ $enquiry->enquiry_date->format('Y-m-d') }}"
                    data-min-date="{{ $enquiry->enquiry_date->format('Y-m-d') }}"
                    data-min-date-fixed="1"
                  @endif
                >
                @error('response_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label for="client_response" class="form-label">Remark <span class="text-danger">*</span></label>
                <textarea
                  name="client_response"
                  id="client_response"
                  rows="4"
                  class="form-control @error('client_response') is-invalid @enderror"
                  placeholder="Enter remark"
                  maxlength="2000"
                >{{ old('client_response') }}</textarea>
                @error('client_response')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <button type="submit" class="btn btn-accent">
                <i class="bi bi-check-lg"></i> Save remark
              </button>
            </form>
          </div>
        </div>
      </div>
    @endcan

    {{-- History --}}
    <div class="{{ auth()->user()->can('update', $enquiry) ? 'col-lg-7' : 'col-12' }}">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 class="enq-section-title"><i class="bi bi-clock-history"></i> Remark history</h2>
          <span class="badge bg-light text-dark border">{{ $enquiry->responses->count() }}</span>
        </div>
        <div class="card-body">
          @if ($enquiry->responses->isNotEmpty())
            <ul class="response-timeline">
              @foreach ($enquiry->responses as $response)
                <li class="response-timeline-item">
                  <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div class="response-date">{{ $response->response_date?->format('d M Y') ?? 'No date' }}</div>
                    <div class="response-meta">
                      {{ $response->user?->name ?? 'System' }}
                      · {{ $response->created_at?->format('d M Y H:i') }}
                    </div>
                  </div>
                  <div class="response-body">{{ $response->client_response }}</div>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-center text-secondary py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No remarks yet for this enquiry.
              @can('update', $enquiry)
                <div class="mt-2">
                  <a href="#client-response" class="btn btn-sm btn-outline-secondary">Add the first remark</a>
                </div>
              @endcan
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/enquiry-validation.js') }}?v={{ @filemtime(public_path('assets/js/enquiry-validation.js')) }}"></script>
@endpush
