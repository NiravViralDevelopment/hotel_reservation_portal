@extends('layouts.app')

@section('title', 'Report results')
@section('page', 'reports')

@section('content')
  @php
    $reportKey = $validated['report'] ?? 'enquiries';
    $moduleSlug = match ($reportKey) {
      'group_bookings' => 'group-bookings',
      'cancelled_bookings' => 'cancelled-bookings',
      default => 'enquiries',
    };
    $titles = [
      'enquiries' => 'Enquiries',
      'group_bookings' => 'Group bookings',
      'cancelled_bookings' => 'Cancelled bookings',
    ];
    $pageTitle = $titles[$reportKey] ?? 'Report results';
    $creators = $creators ?? collect();
    $dateMonth = $dateMonth ?? null;
  @endphp

  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
          <li class="breadcrumb-item active">{{ $pageTitle }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $pageTitle }}</h1>
      <p class="page-subtitle mb-0">
        @if (!empty($validated['date_from']) && !empty($validated['date_to']))
          {{ \Illuminate\Support\Carbon::parse($validated['date_from'])->format('d M Y') }}
          —
          {{ \Illuminate\Support\Carbon::parse($validated['date_to'])->format('d M Y') }}
          · {{ $results->count() }} {{ \Illuminate\Support\Str::plural('record', $results->count()) }}
        @else
          All dates
        @endif
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a
        href="{{ route('reports.export', array_merge(['report' => $moduleSlug], request()->only(['q', 'hotel_id', 'created_by', 'date_from', 'date_to', 'date_month', 'sort', 'dir']))) }}"
        class="btn btn-outline-success btn-sm"
      >
        <i class="bi bi-download"></i> Export
      </a>
    </div>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('reports.module', $moduleSlug) }}" class="enquiry-filters">
        <div class="enquiry-filters-grid">
          <div class="enquiry-filter-field">
            <label for="report_q" class="enquiry-filter-label">Search</label>
            <div class="input-group input-group-sm search-input">
              <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input
                type="search"
                name="q"
                id="report_q"
                class="form-control form-control-sm border-start-0"
                placeholder="Search"
                value="{{ request('q') }}"
              >
            </div>
          </div>
          <div class="enquiry-filter-field">
            <label for="report_hotel_id" class="enquiry-filter-label">Hotel</label>
            <select name="hotel_id" id="report_hotel_id" class="form-select form-select-sm select2" data-width="100%">
              <option value="">All hotels</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
              @endforeach
            </select>
          </div>
          @if (auth()->user()?->hasRole('Administrator'))
          <div class="enquiry-filter-field">
            <label for="report_created_by" class="enquiry-filter-label">Created by</label>
            <select name="created_by" id="report_created_by" class="form-select form-select-sm select2" data-width="100%">
              <option value="">Created by</option>
              @foreach ($creators as $creator)
                <option value="{{ $creator->id }}" @selected((string) request('created_by') === (string) $creator->id)>{{ $creator->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          <div class="enquiry-filter-field enquiry-filter-month">
            <label for="report_month" class="enquiry-filter-label">Month</label>
            <input
              type="month"
              name="date_month"
              id="report_month"
              class="form-control form-control-sm"
              value="{{ $dateMonth }}"
              title="Filter by month"
            >
          </div>
          <div class="enquiry-filter-field">
            <label for="report_from" class="enquiry-filter-label">Start date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="date_from" id="report_from" class="form-control form-control-sm" value="{{ $validated['date_from'] }}" title="Start date">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
          </div>
          <div class="enquiry-filter-field">
            <label for="report_to" class="enquiry-filter-label">End date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="date_to" id="report_to" class="form-control form-control-sm" value="{{ $validated['date_to'] }}" title="End date" @if (!empty($validated['date_from'])) min="{{ $validated['date_from'] }}" @endif>
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
          </div>
          <div class="enquiry-filter-actions">
            <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            @if (request()->hasAny(['q', 'hotel_id', 'created_by', 'date_from', 'date_to', 'date_month']))
              <a href="{{ route('reports.module', $moduleSlug) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            @endif
          </div>
        </div>
        @if (request('sort'))
          <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if (request('dir'))
          <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif
      </form>
    </div>
    @if ($reportKey === 'enquiries')
      @include('enquiries.partials.list-table', [
        'rows' => $results,
        'variant' => 'enquiry',
        'defaultSort' => 'enquiry_date',
        'defaultDir' => 'desc',
        'emptyMessage' => 'No enquiries in this date range.',
        'readOnly' => true,
        'viewOnlyActions' => true,
        'showCreatedBy' => true,
      ])
    @elseif ($reportKey === 'group_bookings')
      @include('group-bookings.partials.list-table', [
        'rows' => $results,
        'defaultSort' => 'check_in',
        'defaultDir' => 'asc',
        'emptyMessage' => 'No group bookings in this date range.',
        'viewOnlyActions' => true,
        'showCreatedBy' => true,
      ])
    @else
      @include('group-bookings.partials.list-table', [
        'rows' => $results,
        'defaultSort' => 'updated_at',
        'defaultDir' => 'desc',
        'emptyMessage' => 'No cancelled bookings in this date range.',
        'showCancellationReason' => true,
        'viewOnlyActions' => true,
        'showCreatedBy' => true,
      ])
    @endif
  </div>
@endsection

@push('scripts')
  <script>
    (function () {
      var month = document.getElementById('report_month');
      var start = document.getElementById('report_from');
      var end = document.getElementById('report_to');
      if (!month || !start || !end) return;

      function monthBounds(value) {
        var parts = String(value || '').split('-');
        if (parts.length !== 2) return null;
        var year = parseInt(parts[0], 10);
        var mon = parseInt(parts[1], 10);
        if (!year || mon < 1 || mon > 12) return null;
        var from = year + '-' + String(mon).padStart(2, '0') + '-01';
        var last = new Date(year, mon, 0).getDate();
        var to = year + '-' + String(mon).padStart(2, '0') + '-' + String(last).padStart(2, '0');
        return { from: from, to: to };
      }

      function syncPlaceholder(input) {
        input.dispatchEvent(new Event('input', { bubbles: true }));
      }

      function syncMonthFromDates() {
        if (!start.value || !end.value) {
          month.value = '';
          return;
        }
        var bounds = monthBounds(start.value.slice(0, 7));
        if (bounds && start.value === bounds.from && end.value === bounds.to && start.value.slice(0, 7) === end.value.slice(0, 7)) {
          month.value = start.value.slice(0, 7);
        } else {
          month.value = '';
        }
      }

      month.addEventListener('change', function () {
        if (!month.value) return;
        var bounds = monthBounds(month.value);
        if (!bounds) return;
        start.value = bounds.from;
        end.value = bounds.to;
        end.min = bounds.from;
        syncPlaceholder(start);
        syncPlaceholder(end);
      });

      start.addEventListener('change', function () {
        end.min = start.value || '';
        if (start.value && end.value && end.value < start.value) {
          end.value = start.value;
          syncPlaceholder(end);
        }
        syncMonthFromDates();
      });

      end.addEventListener('change', syncMonthFromDates);
    })();
  </script>
@endpush
