@extends('layouts.app')

@section('title', 'Travel agencies')
@section('page', 'travel-agencies')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Travel agencies</li>
        </ol>
      </nav>
      <h1 class="page-title">Travel agencies</h1>
      <p class="page-subtitle">Agency partners and booking sources.</p>
    </div>
    @can('create', App\Models\TravelAgency::class)
      <a href="{{ route('travel-agencies.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add agency</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('travel-agencies.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="width: 260px; flex-shrink: 0;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="form-control"
            placeholder="Search code, name, city…"
            aria-label="Search travel agencies"
          >
        </div>

        <select name="status" class="form-select form-select-sm select2" style="width:auto; min-width: 130px;">
          <option value="">All statuses</option>
          <option value="active" @selected(request('status') === 'active')>Active</option>
          <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>

        <select name="country" class="form-select form-select-sm select2" style="width:auto; min-width: 150px;">
          <option value="">All countries</option>
          @foreach ($countries as $country)
            <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
          @endforeach
        </select>

        <button type="submit" class="btn btn-outline-secondary btn-sm flex-shrink-0"><i class="bi bi-funnel"></i> Filter</button>

        @if (request()->hasAny(['q', 'status', 'country']))
          <a href="{{ route('travel-agencies.index') }}" class="btn btn-outline-danger btn-sm flex-shrink-0">
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
            <x-sortable-th column="code" label="Code" />
            <x-sortable-th column="name" label="Name" />
            <th>Contact</th>
            <x-sortable-th column="city" label="City" />
            <x-sortable-th column="enquiries" label="Enquiries" class="text-center" />
            <x-sortable-th column="bookings" label="Bookings" class="text-center" />
            <x-sortable-th column="status" label="Status" />
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($travelAgencies as $travelAgency)
            <tr>
              <td class="fw-semibold"><a href="{{ route('travel-agencies.show', $travelAgency) }}">{{ $travelAgency->code }}</a></td>
              <td>{{ $travelAgency->name }}</td>
              <td>{{ $travelAgency->contact_name ?? '—' }}</td>
              <td>{{ $travelAgency->city ?? '—' }}</td>
              <td class="text-center">{{ $travelAgency->enquiries_count }}</td>
              <td class="text-center">{{ $travelAgency->group_bookings_count }}</td>
              <td><x-badge-status :status="$travelAgency->status" /></td>
              <td class="text-end text-nowrap">
                @can('view', $travelAgency)
                  <a href="{{ route('travel-agencies.show', $travelAgency) }}" class="btn btn-sm btn-outline-secondary" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                @endcan
                @can('update', $travelAgency)
                  <a href="{{ route('travel-agencies.edit', $travelAgency) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                @endcan
                @can('delete', $travelAgency)
                  <form method="POST" action="{{ route('travel-agencies.destroy', $travelAgency) }}" class="d-inline" data-confirm-title="Delete travel agency" data-confirm="{{ "Are you sure you want to delete the travel agency \"{$travelAgency->name}\"?\n\nThis will permanently remove it and cannot be undone." }}" data-confirm-button="Delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-airplane fs-3"></i></div>
                <div>No travel agencies match your filters.</div>
                @if (request()->hasAny(['q', 'status', 'country']))
                  <a href="{{ route('travel-agencies.index') }}" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="bi bi-x-circle"></i> Clear filters
                  </a>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $travelAgencies])
  </div>
@endsection
