@extends('layouts.app')

@php
  $isArrival = $list === 'arrivals';
  $listLabel = $isArrival ? 'Arrivals' : 'Departures';
  $listRoute = $isArrival ? 'arrivals.index' : 'departures.index';
@endphp

@section('title', $enquiry->group_name ?: ($enquiry->ref ?: $listLabel))
@section('page', $list)

@push('styles')
<style>
  .stay-show .stay-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1rem;
    align-items: center;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }
  .stay-show .stay-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .stay-show .stay-section-title i { color: var(--brand-accent); }
  .stay-show .stay-dl {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.85rem 1.25rem;
  }
  @media (max-width: 575.98px) {
    .stay-show .stay-dl { grid-template-columns: 1fr; }
  }
  .stay-show .stay-dl-item { min-width: 0; }
  .stay-show .stay-dl-label {
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 0.2rem;
  }
  .stay-show .stay-dl-value {
    font-weight: 600;
    color: var(--text-primary);
    word-break: break-word;
  }
  .stay-show .stay-dl-value.muted { font-weight: 500; color: var(--text-secondary); }
  .stay-show .revenue-value { font-size: 1.35rem; font-weight: 650; }
  .stay-show .response-timeline { list-style: none; margin: 0; padding: 0; }
  .stay-show .response-timeline-item {
    position: relative;
    padding: 0 0 1.25rem 1.5rem;
    border-left: 2px solid var(--border-color);
  }
  .stay-show .response-timeline-item:last-child { padding-bottom: 0; border-left-color: transparent; }
  .stay-show .response-timeline-item::before {
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
  .stay-show .response-date { font-weight: 700; color: var(--text-primary); }
  .stay-show .response-meta { font-size: 0.8125rem; color: var(--text-secondary); }
  .stay-show .response-body {
    margin-top: 0.5rem;
    padding: 0.85rem 1rem;
    background: var(--bg-body);
    border-radius: 0.5rem;
    color: var(--text-primary);
    white-space: pre-wrap;
  }
</style>
@endpush

@section('content')
@php
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
  $text = function ($value) {
      return filled($value) ? $value : '—';
  };
@endphp

<div class="stay-show">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route($listRoute) }}">{{ $listLabel }}</a></li>
          <li class="breadcrumb-item active">{{ $enquiry->group_name ?: ($enquiry->ref ?: $listLabel) }}</li>
        </ol>
      </nav>
      <h1 class="page-title mb-2">{{ $enquiry->group_name ?: $listLabel }}</h1>
      <div class="stay-meta">
        <span>{{ $enquiry->ref ?: 'No ref no' }}</span>
        <x-badge-status :status="$enquiry->status" />
      </div>
    </div>
    <a href="{{ route($listRoute) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="stay-section-title"><i class="bi bi-calendar3"></i> Dates</h2>
    </div>
    <div class="card-body">
      <div class="stay-dl">
        <div class="stay-dl-item">
          <div class="stay-dl-label">Enquiry Date</div>
          <div class="stay-dl-value">{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Response Date</div>
          <div class="stay-dl-value">{{ $enquiry->response_date?->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Arrival Date</div>
          <div class="stay-dl-value"><x-date-alert :date="$enquiry->check_in" /></div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Departure Date</div>
          <div class="stay-dl-value">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Day</div>
          <div class="stay-dl-value">{{ $text($enquiry->day ?: $enquiry->check_in_day) }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Nights</div>
          <div class="stay-dl-value">{{ $enquiry->nights ?? '—' }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Total Room per Night</div>
          <div class="stay-dl-value">{{ $enquiry->rooms_per_night ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="stay-section-title"><i class="bi bi-people"></i> Group</h2>
    </div>
    <div class="card-body">
      <div class="stay-dl">
        <div class="stay-dl-item">
          <div class="stay-dl-label">Hotel</div>
          <div class="stay-dl-value">{{ $text($enquiry->hotel?->name) }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Group Name</div>
          <div class="stay-dl-value">{{ $text($enquiry->group_name) }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Ref No</div>
          <div class="stay-dl-value">{{ $text($enquiry->ref) }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Email ID</div>
          <div class="stay-dl-value">
            @if ($enquiry->email)
              <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>
            @else
              <span class="muted">—</span>
            @endif
          </div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Status</div>
          <div class="stay-dl-value"><x-badge-status :status="$enquiry->status" /></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="stay-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
    </div>
    <div class="card-body">
      @if ($enquiry->check_in && $enquiry->check_out && $enquiry->check_out->gte($enquiry->check_in))
        @php
          $dailyByDate = [];
          foreach (($enquiry->daily_room_rates ?? []) as $row) {
              if (is_array($row) && ! empty($row['date'])) {
                  $dailyByDate[\Illuminate\Support\Carbon::parse($row['date'])->toDateString()] = $row;
              }
          }
          $stayCursor = $enquiry->check_in->copy()->startOfDay();
          $stayEnd = $enquiry->check_out->copy()->startOfDay();
          $stayCount = 0;
        @endphp
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th rowspan="2">Date</th>
                <th colspan="2">Single Room &amp; Rate</th>
                <th colspan="2">Double Room &amp; Rate</th>
                <th colspan="2">Triple Room &amp; Rate</th>
              </tr>
              <tr>
                <th>Rooms</th>
                <th>Rate</th>
                <th>Rooms</th>
                <th>Rate</th>
                <th>Rooms</th>
                <th>Rate</th>
              </tr>
            </thead>
            <tbody>
              @while ($stayCursor->lt($stayEnd) && $stayCount < 400)
                @php
                  $dayKey = $stayCursor->toDateString();
                  $dayRow = $dailyByDate[$dayKey] ?? null;
                  $singleRooms = $dayRow['single_rooms'] ?? $enquiry->single_rooms;
                  $singleRate = $dayRow['single_rate'] ?? $enquiry->single_rate;
                  $doubleRooms = $dayRow['double_rooms'] ?? $enquiry->double_rooms;
                  $doubleRate = $dayRow['double_rate'] ?? $enquiry->double_rate;
                  $tripleRooms = $dayRow['triple_rooms'] ?? $enquiry->triple_rooms;
                  $tripleRate = $dayRow['triple_rate'] ?? $enquiry->triple_rate;
                @endphp
                <tr>
                  <td class="fw-semibold text-nowrap">{{ $stayCursor->format('d-m-Y') }}</td>
                  <td>{{ $singleRooms ?? '—' }}</td>
                  <td>{{ $money($singleRate) }}</td>
                  <td>{{ $doubleRooms ?? '—' }}</td>
                  <td>{{ $money($doubleRate) }}</td>
                  <td>{{ $tripleRooms ?? '—' }}</td>
                  <td>{{ $money($tripleRate) }}</td>
                </tr>
                @php
                  $stayCursor->addDay();
                  $stayCount++;
                @endphp
              @endwhile
            </tbody>
          </table>
        </div>
      @else
        <p class="text-secondary mb-0">Arrival and departure dates are not set.</p>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h2 class="stay-section-title"><i class="bi bi-currency-pound"></i> Revenue and terms</h2>
    </div>
    <div class="card-body">
      <div class="stay-dl">
        <div class="stay-dl-item">
          <div class="stay-dl-label">Basis</div>
          <div class="stay-dl-value">{{ $text($enquiry->basis) }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Option Date</div>
          <div class="stay-dl-value"><x-date-alert :date="$enquiry->option_date" /></div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">CXL Due Date</div>
          <div class="stay-dl-value"><x-date-alert :date="$enquiry->cxl_due_date" /></div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">CXL Policy</div>
          <div class="stay-dl-value">{{ $text($enquiry->cxl_policy) }}</div>
        </div>
        <div class="stay-dl-item">
          <div class="stay-dl-label">Total Revenue</div>
          <div class="stay-dl-value revenue-value">{{ $money($enquiry->total_revenue) }}</div>
        </div>
        <div class="stay-dl-item" style="grid-column: 1 / -1;">
          <div class="stay-dl-label">Remarks</div>
          <div class="stay-dl-value {{ $enquiry->remarks ? '' : 'muted' }}" style="white-space: pre-wrap;">{{ $text($enquiry->remarks) }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h2 class="stay-section-title"><i class="bi bi-clock-history"></i> Remark history</h2>
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
          No remarks yet.
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
