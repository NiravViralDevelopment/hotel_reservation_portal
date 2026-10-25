@extends('layouts.app')

@section('title', $enquiry->group_name ?: ($enquiry->ref ?: 'Enquiry'))
@section('page', ! empty($cancelledContext) ? 'cancelled-inquiries' : 'enquiries')

@push('styles')
<style>
  .enq-show .enq-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1rem;
    align-items: center;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }
  .enq-show .enq-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .enq-show .enq-section-title i {
    width: 1.85rem;
    height: 1.85rem;
    border-radius: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(26, 77, 156, 0.1);
    color: var(--brand-accent);
    font-size: 0.95rem;
    flex-shrink: 0;
  }
  .enq-show .enq-dl {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
  }
  .enq-show .enq-dl.enq-dl-wide {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  @media (max-width: 1199.98px) {
    .enq-show .enq-dl.enq-dl-wide { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 575.98px) {
    .enq-show .enq-dl,
    .enq-show .enq-dl.enq-dl-wide { grid-template-columns: 1fr; }
  }
  .enq-show .enq-dl-item {
    min-width: 0;
    background: var(--bg-body);
    border-radius: 0.65rem;
    padding: 0.8rem 0.9rem;
  }
  .enq-show .enq-dl-item.is-highlight {
    background: linear-gradient(135deg, rgba(2, 34, 90, 0.06), rgba(26, 77, 156, 0.12));
    border: 1px solid rgba(26, 77, 156, 0.16);
  }
  .enq-show .enq-dl-label {
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
  }
  .enq-show .enq-dl-value {
    font-weight: 600;
    color: var(--text-primary);
    word-break: break-word;
  }
  .enq-show .enq-dl-value.muted { font-weight: 500; color: var(--text-secondary); }
  .enq-show .room-tile {
    background: var(--bg-body);
    border-radius: 0.75rem;
    padding: 1rem;
    height: 100%;
    border: 1px solid var(--border-color);
  }
  .enq-show .room-tile-label {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  .enq-show .room-tile-label i { color: var(--brand-accent); }
  .enq-show .revenue-value {
    font-size: 1.35rem;
    font-weight: 650;
  }
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

<div class="enq-show">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ ! empty($cancelledContext) ? route('cancelled-inquiries.index') : route('enquiries.index') }}">{{ ! empty($cancelledContext) ? 'Cancelled Inquiry' : 'Enquiries' }}</a></li>
          <li class="breadcrumb-item active">{{ $enquiry->group_name ?: ($enquiry->ref ?: 'Enquiry') }}</li>
        </ol>
      </nav>
      <h1 class="page-title mb-2">{{ $enquiry->group_name ?: 'Enquiry' }}</h1>
      <div class="enq-meta">
        <span>{{ $enquiry->ref ?: 'No ref no' }}</span>
        <x-badge-status :status="$enquiry->status" />
      </div>
    </div>
    <div class="show-actions">
      <a href="{{ ! empty($cancelledContext) ? route('cancelled-inquiries.index') : route('enquiries.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      @can('convert', $enquiry)
        @if (! $enquiry->is_confirm && ! $enquiry->is_cancel)
          <form method="POST" action="{{ route('enquiries.convert', $enquiry) }}" data-confirm-title="Confirm enquiry" data-confirm="{{ sprintf('Confirm enquiry "%s"? It will move to Confirm Group Bookings.', $enquiry->group_name ?? 'this enquiry') }}" data-confirm-button="Confirm" data-confirm-variant="primary" data-confirm-icon="bi-check-circle">
            @csrf
            <button type="submit" class="btn btn-accent btn-sm">
              <i class="bi bi-check2-circle"></i> Confirm
            </button>
          </form>
        @endif
      @endcan
      @can('update', $enquiry)
        <a href="{{ $enquiry->is_cancel && ! $enquiry->is_confirm ? route('cancelled-inquiries.edit', $enquiry) : route('enquiries.edit', $enquiry) }}" class="btn btn-outline-secondary btn-sm">
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

  @if (! empty($cancelledContext))
    <div class="cancel-banner">
      <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
      <div>
        <div class="fw-semibold mb-1">Cancellation reason</div>
        <div class="mb-1">{{ $enquiry->cancellation_reason ?: '—' }}</div>
        <div class="small text-secondary mb-0">CXL date: {{ $enquiry->cxl_date?->format('d M Y') ?? '—' }}</div>
      </div>
    </div>
  @endif

  <div class="detail-kpis">
    <div class="detail-kpi">
      <div class="detail-kpi-label">Arrival</div>
      <div class="detail-kpi-value">{{ $enquiry->check_in?->format('d M Y') ?? '—' }}</div>
    </div>
    <div class="detail-kpi">
      <div class="detail-kpi-label">Departure</div>
      <div class="detail-kpi-value">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</div>
    </div>
    <div class="detail-kpi">
      <div class="detail-kpi-label">Nights</div>
      <div class="detail-kpi-value">{{ $enquiry->nights ?? '—' }}</div>
    </div>
    <div class="detail-kpi">
      <div class="detail-kpi-label">Rooms / night</div>
      <div class="detail-kpi-value">{{ $enquiry->rooms_per_night ?? '—' }}</div>
    </div>
    <div class="detail-kpi is-primary">
      <div class="detail-kpi-label">Total revenue</div>
      <div class="detail-kpi-value">{{ $money($enquiry->total_revenue) }}</div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-lg-7 d-flex">
  <div class="card mb-0 h-100 w-100">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-calendar3"></i> Dates</h2>
    </div>
    <div class="card-body">
      <div class="enq-dl">
        <div class="enq-dl-item">
          <div class="enq-dl-label">Enquiry Date</div>
          <div class="enq-dl-value">{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Response Date</div>
          <div class="enq-dl-value">{{ $enquiry->response_date?->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Arrival Date</div>
          <div class="enq-dl-value"><x-date-alert :date="$enquiry->check_in" /></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Departure Date</div>
          <div class="enq-dl-value">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Day</div>
          <div class="enq-dl-value">{{ $text($enquiry->day ?: $enquiry->check_in_day) }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Nights</div>
          <div class="enq-dl-value">{{ $enquiry->nights ?? '—' }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Total Room per Night</div>
          <div class="enq-dl-value">{{ $enquiry->rooms_per_night ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>
    </div>
    <div class="col-lg-5 d-flex">
  <div class="card mb-0 h-100 w-100">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-people"></i> Group</h2>
    </div>
    <div class="card-body">
      <div class="enq-dl">
        <div class="enq-dl-item">
          <div class="enq-dl-label">Group Name</div>
          <div class="enq-dl-value">{{ $text($enquiry->group_name) }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Ref No</div>
          <div class="enq-dl-value">{{ $text($enquiry->ref) }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Email ID</div>
          <div class="enq-dl-value">
            @if ($enquiry->email)
              <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>
            @else
              <span class="muted">—</span>
            @endif
          </div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Status</div>
          <div class="enq-dl-value"><x-badge-status :status="$enquiry->status" /></div>
        </div>
      </div>
    </div>
  </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
    </div>
    <div class="card-body">
      @if (is_array($enquiry->stay_segments) && $enquiry->stay_segments !== [])
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Day</th>
                <th class="text-end">Nights</th>
                <th class="text-end">Single RNs</th>
                <th class="text-end">Single Rate</th>
                <th class="text-end">Double RNs</th>
                <th class="text-end">Double Rate</th>
                <th class="text-end">Triple RNs</th>
                <th class="text-end">Triple Rate</th>
                <th class="text-end">Total Room Nights</th>
                <th class="text-end">Total Rev</th>
              </tr>
            </thead>
            <tbody>
              @php
                $segmentRevenueTotal = 0.0;
                $segmentRoomNightsTotal = 0;
              @endphp
              @foreach ($enquiry->stay_segments as $segment)
                @php
                  $segmentNights = (int) ($segment['nights'] ?? 0);
                  $segmentSingle = (int) ($segment['single_rooms'] ?? 0);
                  $segmentSingleRate = (float) ($segment['single_rate'] ?? 0);
                  $segmentDouble = (int) ($segment['double_rooms'] ?? 0);
                  $segmentDoubleRate = (float) ($segment['double_rate'] ?? 0);
                  $segmentTriple = (int) ($segment['triple_rooms'] ?? 0);
                  $segmentTripleRate = (float) ($segment['triple_rate'] ?? 0);
                  $segmentRoomNights = $segmentNights * ($segmentSingle + $segmentDouble + $segmentTriple);
                  $segmentRevenue = $segmentNights * (($segmentSingle * $segmentSingleRate) + ($segmentDouble * $segmentDoubleRate) + ($segmentTriple * $segmentTripleRate));
                  $segmentRevenueTotal += $segmentRevenue;
                  $segmentRoomNightsTotal += $segmentRoomNights;
                @endphp
                <tr>
                  <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($segment['check_in'])->format('d-m-Y') }}</td>
                  <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($segment['check_out'])->format('d-m-Y') }}</td>
                  <td>{{ $segment['day'] ?? '—' }}</td>
                  <td class="text-end">{{ $segmentNights }}</td>
                  <td class="text-end">{{ $segmentSingle }}</td>
                  <td class="text-end">{{ $money($segmentSingleRate) }}</td>
                  <td class="text-end">{{ $segmentDouble }}</td>
                  <td class="text-end">{{ $money($segmentDoubleRate) }}</td>
                  <td class="text-end">{{ $segmentTriple }}</td>
                  <td class="text-end">{{ $money($segmentTripleRate) }}</td>
                  <td class="text-end fw-semibold">{{ $segmentRoomNights }}</td>
                  <td class="text-end fw-semibold">{{ $money($segmentRevenue) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th scope="row" colspan="10">Total</th>
                <td class="text-end fw-semibold">{{ $segmentRoomNightsTotal }}</td>
                <td class="text-end fw-semibold">{{ $money($segmentRevenueTotal) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      @elseif ($enquiry->check_in && $enquiry->check_out && $enquiry->check_out->gte($enquiry->check_in))
        @php
          $dailyByDate = [];
          foreach (($enquiry->daily_room_rates ?? []) as $row) {
              if (is_array($row) && ! empty($row['date'])) {
                  $dailyByDate[\Illuminate\Support\Carbon::parse($row['date'])->toDateString()] = $row;
              }
          }
          $hasDailyRooms = $dailyByDate !== [];
          $stayCursor = $enquiry->check_in->copy()->startOfDay();
          $stayEnd = $enquiry->check_out->copy()->startOfDay();
          $stayCount = 0;
          $roomTotals = [
              'single_rooms' => 0,
              'double_rooms' => 0,
              'triple_rooms' => 0,
          ];
          $revenueTotals = ['single' => 0.0, 'double' => 0.0, 'triple' => 0.0];
          $dayRoomsTotal = 0;
          $dayRevenueTotal = 0.0;
          $showBreakfast = in_array($enquiry->basis, ['BB', 'DBB'], true);
          $showDinner = $enquiry->basis === 'DBB';
          $breakfastRate = (float) ($enquiry->breakfast_rate ?? 0);
          $dinnerRate = (float) ($enquiry->dinner_rate ?? 0);
          $bbTotal = 0.0;
          $dinnerTotal = 0.0;
          $stayDays = [];
          while ($stayCursor->lt($stayEnd) && $stayCount < 400) {
              $dayKey = $stayCursor->toDateString();
              $dayRow = $dailyByDate[$dayKey] ?? null;
              $singleRooms = (int) ($dayRow['single_rooms'] ?? ($hasDailyRooms ? 0 : ($enquiry->single_rooms ?? 0)));
              $singleRate = (float) ($dayRow['single_rate'] ?? ($hasDailyRooms ? 0 : ($enquiry->single_rate ?? 0)));
              $doubleRooms = (int) ($dayRow['double_rooms'] ?? ($hasDailyRooms ? 0 : ($enquiry->double_rooms ?? 0)));
              $doubleRate = (float) ($dayRow['double_rate'] ?? ($hasDailyRooms ? 0 : ($enquiry->double_rate ?? 0)));
              $tripleRooms = (int) ($dayRow['triple_rooms'] ?? ($hasDailyRooms ? 0 : ($enquiry->triple_rooms ?? 0)));
              $tripleRate = (float) ($dayRow['triple_rate'] ?? ($hasDailyRooms ? 0 : ($enquiry->triple_rate ?? 0)));
              $dayRooms = $singleRooms + $doubleRooms + $tripleRooms;
              $dayRevenue = ($singleRooms * $singleRate) + ($doubleRooms * $doubleRate) + ($tripleRooms * $tripleRate);
              $covers = ($singleRooms * 1) + ($doubleRooms * 2) + ($tripleRooms * 3);
              $roomTotals['single_rooms'] += $singleRooms;
              $roomTotals['double_rooms'] += $doubleRooms;
              $roomTotals['triple_rooms'] += $tripleRooms;
              $revenueTotals['single'] += $singleRooms * $singleRate;
              $revenueTotals['double'] += $doubleRooms * $doubleRate;
              $revenueTotals['triple'] += $tripleRooms * $tripleRate;
              $dayRoomsTotal += $dayRooms;
              $dayRevenueTotal += $dayRevenue;
              $bbTotal += $covers * $breakfastRate;
              $dinnerTotal += $covers * $dinnerRate;
              $stayDays[] = [
                  'label' => $stayCursor->format('d-m-Y'),
                  'single_rooms' => $singleRooms,
                  'single_rate' => $singleRate,
                  'double_rooms' => $doubleRooms,
                  'double_rate' => $doubleRate,
                  'triple_rooms' => $tripleRooms,
                  'triple_rate' => $tripleRate,
                  'rooms' => $dayRooms,
                  'average' => $dayRooms > 0 ? $dayRevenue / $dayRooms : 0,
                  'revenue' => $dayRevenue,
                  'bb' => $covers * $breakfastRate,
                  'dinner' => $covers * $dinnerRate,
              ];
              $stayCursor->addDay();
              $stayCount++;
          }
          $weighted = function (int $rooms, float $revenue): float {
              return $rooms > 0 ? $revenue / $rooms : 0;
          };
        @endphp
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th rowspan="2">Date</th>
                <th colspan="2">Single Room &amp; Rate</th>
                <th colspan="2">Double Room &amp; Rate</th>
                <th colspan="2">Triple Room &amp; Rate</th>
                <th rowspan="2" class="text-end">Total rooms</th>
                <th rowspan="2" class="text-end">Avg rate</th>
                <th rowspan="2" class="text-end">Revenue</th>
                @if ($showBreakfast)
                  <th rowspan="2" class="text-end">BB</th>
                @endif
                @if ($showDinner)
                  <th rowspan="2" class="text-end">Dinner</th>
                @endif
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
              @foreach ($stayDays as $day)
                <tr>
                  <td class="fw-semibold text-nowrap">{{ $day['label'] }}</td>
                  <td>{{ $day['single_rooms'] }}</td>
                  <td>{{ $money($day['single_rate']) }}</td>
                  <td>{{ $day['double_rooms'] }}</td>
                  <td>{{ $money($day['double_rate']) }}</td>
                  <td>{{ $day['triple_rooms'] }}</td>
                  <td>{{ $money($day['triple_rate']) }}</td>
                  <td class="text-end fw-semibold">{{ $day['rooms'] }}</td>
                  <td class="text-end fw-semibold">{{ $money($day['average']) }}</td>
                  <td class="text-end fw-semibold">{{ $money($day['revenue']) }}</td>
                  @if ($showBreakfast)
                    <td class="text-end fw-semibold">{{ $money($day['bb']) }}</td>
                  @endif
                  @if ($showDinner)
                    <td class="text-end fw-semibold">{{ $money($day['dinner']) }}</td>
                  @endif
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th scope="row">Total</th>
                <td class="fw-semibold">{{ $roomTotals['single_rooms'] }}</td>
                <td class="fw-semibold">{{ $money($weighted($roomTotals['single_rooms'], $revenueTotals['single'])) }}</td>
                <td class="fw-semibold">{{ $roomTotals['double_rooms'] }}</td>
                <td class="fw-semibold">{{ $money($weighted($roomTotals['double_rooms'], $revenueTotals['double'])) }}</td>
                <td class="fw-semibold">{{ $roomTotals['triple_rooms'] }}</td>
                <td class="fw-semibold">{{ $money($weighted($roomTotals['triple_rooms'], $revenueTotals['triple'])) }}</td>
                <td class="text-end fw-semibold">{{ $dayRoomsTotal }}</td>
                <td class="text-end fw-semibold">{{ $money($weighted($dayRoomsTotal, $dayRevenueTotal)) }}</td>
                <td class="text-end fw-semibold">{{ $money($dayRevenueTotal) }}</td>
                @if ($showBreakfast)
                  <td class="text-end fw-semibold">{{ $money($bbTotal) }}</td>
                @endif
                @if ($showDinner)
                  <td class="text-end fw-semibold">{{ $money($dinnerTotal) }}</td>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>
      @else
        <p class="text-secondary mb-0">Arrival and departure dates are not set.</p>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-currency-pound"></i> Revenue and terms</h2>
    </div>
    <div class="card-body">
      <div class="enq-dl enq-dl-wide">
        <div class="enq-dl-item">
          <div class="enq-dl-label">Basis</div>
          <div class="enq-dl-value">{{ $text($enquiry->basis) }}</div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Option Date</div>
          <div class="enq-dl-value"><x-date-alert :date="$enquiry->option_date" /></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">CXL Due Date</div>
          <div class="enq-dl-value"><x-date-alert :date="$enquiry->cxl_due_date" /></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">CXL Policy</div>
          <div class="enq-dl-value">{{ $text($enquiry->cxl_policy) }}</div>
        </div>
        <div class="enq-dl-item is-highlight">
          <div class="enq-dl-label">Total Revenue</div>
          <div class="enq-dl-value revenue-value">{{ $money($enquiry->total_revenue) }}</div>
        </div>
        <div class="enq-dl-item" style="grid-column: 1 / -1;">
          <div class="enq-dl-label">Remarks</div>
          <div class="enq-dl-value {{ $enquiry->remarks ? '' : 'muted' }}" style="white-space: pre-wrap;">{{ $text($enquiry->remarks) }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    @can('update', $enquiry)
      <div class="col-lg-5">
        <div class="card h-100" id="client-response">
          <div class="card-header">
            <h2 class="enq-section-title"><i class="bi bi-reply"></i> Add remark</h2>
          </div>
          <div class="card-body">
            <p class="small text-secondary mb-3">
              Record a remark for this enquiry. Each save is kept in the history for <strong>{{ $enquiry->group_name }}</strong>@if ($enquiry->ref) ({{ $enquiry->ref }})@endif.
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
