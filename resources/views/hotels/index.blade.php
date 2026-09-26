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
      <div class="input-group search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0 table-search" placeholder="Search hotels…" data-table="hotelsTable">
      </div>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0" id="hotelsTable">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Company</th>
            <th>City</th>
            <th>Manager</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($hotels as $hotel)
            <tr>
              <td class="fw-semibold"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->code }}</a></td>
              <td>{{ $hotel->name }}</td>
              <td>{{ $hotel->company?->name ?? '—' }}</td>
              <td>{{ $hotel->city }}</td>
              <td>{{ $hotel->managerUser?->name ?? $hotel->manager_name ?? '—' }}</td>
              <td><x-badge-status :status="$hotel->status" /></td>
              <td class="text-end">
                @can('update', $hotel)
                  <a href="{{ route('hotels.edit', $hotel) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">No hotels found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $hotels])
  </div>
@endsection
