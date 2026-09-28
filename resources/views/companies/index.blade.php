@extends('layouts.app')

@section('title', 'Companies')
@section('page', 'companies')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Companies</li>
        </ol>
      </nav>
      <h1 class="page-title">Companies</h1>
      <p class="page-subtitle">Parent companies and hotel group entities.</p>
    </div>
    @can('create', App\Models\Company::class)
      <a href="{{ route('companies.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add company</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('companies.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="max-width: 260px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="form-control"
            placeholder="Search name, reg, city…"
            aria-label="Search companies"
          >
        </div>

        <select name="status" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All statuses</option>
          <option value="active" @selected(request('status') === 'active')>Active</option>
          <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>

        <select name="country" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All countries</option>
          @foreach ($countries as $country)
            <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
          @endforeach
        </select>

        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>

        @if (request()->hasAny(['q', 'status', 'country']))
          <a href="{{ route('companies.index') }}" class="btn btn-outline-danger btn-sm">
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
            <x-sortable-th column="name" label="Company" />
            <x-sortable-th column="city" label="Location" />
            <x-sortable-th column="hotels" label="Hotels" class="text-center" />
            <x-sortable-th column="contacts" label="Contacts" class="text-center" />
            <x-sortable-th column="status" label="Status" />
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($companies as $company)
            <tr>
              <td>
                <div class="fw-semibold"><a href="{{ route('companies.show', $company) }}">{{ $company->name }}</a></div>
                @if ($company->reg_number)
                  <div class="small text-secondary">Reg. {{ $company->reg_number }}</div>
                @endif
              </td>
              <td>
                @if ($company->city || $company->country)
                  {{ collect([$company->city, $company->country])->filter()->implode(', ') }}
                @else
                  <span class="text-secondary">—</span>
                @endif
              </td>
              <td class="text-center">{{ $company->hotels_count }}</td>
              <td class="text-center">{{ $company->contacts_count }}</td>
              <td><x-badge-status :status="$company->status" /></td>
              <td class="text-end text-nowrap">
                @can('update', $company)
                  <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary" title="Edit company">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                @endcan
                @can('delete', $company)
                  <form method="POST" action="{{ route('companies.destroy', $company) }}" class="d-inline" onsubmit="return confirm('Remove {{ $company->name }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove company">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-building fs-3"></i></div>
                <div>No companies match your filters.</div>
                @if (request()->hasAny(['q', 'status', 'country']))
                  <a href="{{ route('companies.index') }}" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="bi bi-x-circle"></i> Clear filters
                  </a>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $companies])
  </div>
@endsection
