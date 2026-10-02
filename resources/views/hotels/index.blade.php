@extends('layouts.app')

@section('title', 'Hotels')
@section('page', 'hotels')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Hotels</li>
        </ol>
      </nav>
      <h1 class="page-title">Hotels</h1>
      <p class="page-subtitle">Property portfolio and managers.</p>
    </div>
    @can('create', App\Models\Hotel::class)
      <a href="{{ route('hotels.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add hotel</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('hotels.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="max-width: 260px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="form-control"
            placeholder="Search name, code, city…"
            aria-label="Search hotels"
          >
        </div>

        <select name="status" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All statuses</option>
          <option value="active" @selected(request('status') === 'active')>Active</option>
          <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>

        <select name="company_id" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All companies</option>
          @foreach ($companies as $company)
            <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->name }}</option>
          @endforeach
        </select>

        <select name="city" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All cities</option>
          @foreach ($cities as $city)
            <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
          @endforeach
        </select>

        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>

        @if (request()->hasAny(['q', 'status', 'company_id', 'city']))
          <a href="{{ route('hotels.index') }}" class="btn btn-outline-danger btn-sm">
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
    </div>

    <div class="table-wrapper">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <x-sortable-th column="name" label="Hotel" />
            <th>Company</th>
            <x-sortable-th column="city" label="Location" />
            <x-sortable-th column="rooms" label="Rooms" class="text-center" />
            <x-sortable-th column="manager" label="Manager" />
            <x-sortable-th column="status" label="Status" />
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($hotels as $hotel)
            <tr>
              <td>
                <div class="fw-semibold"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->name }}</a></div>
                <div class="small text-secondary">{{ $hotel->code }}</div>
              </td>
              <td>{{ $hotel->company?->name ?? '—' }}</td>
              <td>
                @if ($hotel->city || $hotel->country)
                  {{ collect([$hotel->city, $hotel->country])->filter()->implode(', ') }}
                @else
                  <span class="text-secondary">—</span>
                @endif
              </td>
              <td class="text-center">{{ $hotel->rooms ?? '—' }}</td>
              <td>{{ $hotel->manager_name ?? '—' }}</td>
              <td><x-badge-status :status="$hotel->status" /></td>
              <td class="text-end text-nowrap">
                @can('update', $hotel)
                  <a href="{{ route('hotels.edit', $hotel) }}" class="btn btn-sm btn-outline-secondary" title="Edit hotel">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                @endcan
                @can('delete', $hotel)
                  <form method="POST" action="{{ route('hotels.destroy', $hotel) }}" class="d-inline" onsubmit="return confirm('Delete {{ $hotel->name }}? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete hotel">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-building fs-3"></i></div>
                <div>No hotels match your filters.</div>
                @if (request()->hasAny(['q', 'status', 'company_id', 'city']))
                  <a href="{{ route('hotels.index') }}" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="bi bi-x-circle"></i> Clear filters
                  </a>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $hotels])
  </div>
@endsection
