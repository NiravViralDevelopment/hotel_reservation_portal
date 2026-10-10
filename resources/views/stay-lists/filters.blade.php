@php
  $prefix = $dateInputPrefix ?? 'list';
  $searchId = $prefix.'_q';
  $hotelSelectId = $prefix.'_hotel_id';
  $statusSelectId = $prefix.'_status';
  $dateMonthId = $prefix.'_month';
  $dateFromId = $prefix.'_from';
  $dateToId = $prefix.'_to';
  $dateMonth = \App\Support\EnquiryIndexFilters::monthValue(request(), $dateFrom ?? null, $dateTo ?? null, 'date_month');
  $clearKeys = ! empty($statuses ?? null)
      ? ['q', 'hotel_id', 'status', 'date_from', 'date_to', 'date_month']
      : ['q', 'hotel_id', 'date_from', 'date_to', 'date_month'];
@endphp
<form method="GET" action="{{ route($filterRoute) }}" class="enquiry-filters">
  <div class="enquiry-filters-grid">
    <div class="enquiry-filter-field">
      <label for="{{ $searchId }}" class="enquiry-filter-label">Search</label>
      <div class="input-group input-group-sm search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input
          type="search"
          name="q"
          id="{{ $searchId }}"
          class="form-control form-control-sm border-start-0"
          placeholder="Search"
          value="{{ request('q') }}"
        >
      </div>
    </div>
    <div class="enquiry-filter-field">
      <label for="{{ $hotelSelectId }}" class="enquiry-filter-label">Hotel</label>
      <select name="hotel_id" id="{{ $hotelSelectId }}" class="form-select form-select-sm select2" data-width="100%">
        <option value="">All hotels</option>
        @foreach ($hotels as $hotel)
          <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
        @endforeach
      </select>
    </div>
    @if (! empty($statuses ?? null))
      <div class="enquiry-filter-field">
        <label for="{{ $statusSelectId }}" class="enquiry-filter-label">Status</label>
        <select name="status" id="{{ $statusSelectId }}" class="form-select form-select-sm select2" data-width="100%">
          <option value="">All statuses</option>
          @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
          @endforeach
        </select>
      </div>
    @endif
    <div class="enquiry-filter-field enquiry-filter-month">
      <label for="{{ $dateMonthId }}" class="enquiry-filter-label">Month</label>
      <input
        type="month"
        name="date_month"
        id="{{ $dateMonthId }}"
        class="form-control form-control-sm"
        value="{{ $dateMonth }}"
        title="Filter by month"
      >
    </div>
    <div class="enquiry-filter-field">
      <label for="{{ $dateFromId }}" class="enquiry-filter-label">Start date</label>
      <div class="date-placeholder-wrap">
        <input type="date" name="date_from" id="{{ $dateFromId }}" class="form-control form-control-sm" value="{{ $dateFrom }}" title="Start date">
        <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
      </div>
    </div>
    <div class="enquiry-filter-field">
      <label for="{{ $dateToId }}" class="enquiry-filter-label">End date</label>
      <div class="date-placeholder-wrap">
        <input type="date" name="date_to" id="{{ $dateToId }}" class="form-control form-control-sm" value="{{ $dateTo }}" title="End date" @if ($dateFrom) min="{{ $dateFrom }}" @endif>
        <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
      </div>
    </div>
    <div class="enquiry-filter-actions">
      <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
      @if (request()->hasAny($clearKeys))
        <a href="{{ route($filterRoute) }}" class="btn btn-outline-danger btn-sm">
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
<script>
  (function () {
    var month = document.getElementById(@json($dateMonthId));
    var start = document.getElementById(@json($dateFromId));
    var end = document.getElementById(@json($dateToId));
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
