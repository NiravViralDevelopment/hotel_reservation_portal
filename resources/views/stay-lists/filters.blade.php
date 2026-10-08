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
  <select name="travel_agency_id" class="form-select form-select-sm select2" style="width:auto; min-width: 160px;">
    <option value="">All agencies</option>
    @foreach ($travelAgencies as $agency)
      <option value="{{ $agency->id }}" @selected((string) request('travel_agency_id') === (string) $agency->id)>{{ $agency->name }}</option>
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
  <div class="d-flex align-items-center gap-1">
    <label for="{{ $monthInputId }}" class="form-label mb-0 small text-secondary">Month</label>
    <input type="month" name="month" id="{{ $monthInputId }}" class="form-control form-control-sm" style="width:auto;" value="{{ $monthValue }}">
  </div>
  <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
  @if (request()->hasAny(! empty($statuses ?? null) ? ['q', 'hotel_id', 'travel_agency_id', 'status', 'month'] : ['q', 'hotel_id', 'travel_agency_id', 'month']))
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
