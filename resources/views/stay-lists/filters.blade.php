<form method="GET" action="{{ route($filterRoute) }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
  <div class="input-group search-input" style="min-width: 200px; max-width: 280px;">
    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
    <input
      type="search"
      name="q"
      class="form-control border-start-0"
      placeholder="Search ref, group, client, email, mobile…"
      value="{{ request('q') }}"
    >
  </div>
  <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto; min-width: 140px;">
    <option value="">All hotels</option>
    @foreach ($hotels as $hotel)
      <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
    @endforeach
  </select>
  @if (! empty($statuses ?? null))
    <select name="status" class="form-select form-select-sm select2" style="width:auto; min-width: 130px;">
      <option value="">All statuses</option>
      @foreach ($statuses as $status)
        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
      @endforeach
    </select>
  @endif
  @php
    $dateFromId = ($dateInputPrefix ?? 'list').'_from';
    $dateToId = ($dateInputPrefix ?? 'list').'_to';
  @endphp
  <div class="d-flex align-items-center gap-1">
    <label for="{{ $dateFromId }}" class="form-label mb-0 small text-secondary">Start date</label>
    <div class="date-placeholder-wrap">
      <input type="date" name="date_from" id="{{ $dateFromId }}" class="form-control form-control-sm" style="width:auto;" value="{{ $dateFrom }}" title="Start date">
      <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-1">
    <label for="{{ $dateToId }}" class="form-label mb-0 small text-secondary">End date</label>
    <div class="date-placeholder-wrap">
      <input type="date" name="date_to" id="{{ $dateToId }}" class="form-control form-control-sm" style="width:auto;" value="{{ $dateTo }}" title="End date" @if ($dateFrom) min="{{ $dateFrom }}" @endif>
      <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
    </div>
  </div>
  <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
  @if (request()->hasAny(! empty($statuses ?? null) ? ['q', 'hotel_id', 'status', 'date_from', 'date_to'] : ['q', 'hotel_id', 'date_from', 'date_to']))
    <a href="{{ route($filterRoute) }}" class="btn btn-outline-danger btn-sm">
      <i class="bi bi-x-circle"></i> Clear
    </a>
  @endif
  @if (request('sort'))
    <input type="hidden" name="sort" value="{{ request('sort') }}">
  @endif
  @if (request('dir'))
    <input type="hidden" name="dir" value="{{ request('dir') }}">
  @endif
</form>
<script>
  document.getElementById(@json($dateFromId))?.addEventListener('change', function () {
    var end = document.getElementById(@json($dateToId));
    if (!end) return;
    end.min = this.value || '';
    if (this.value && end.value && end.value < this.value) {
      end.value = this.value;
    }
  });
</script>
