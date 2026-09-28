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
      <div class="input-group search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0 table-search" placeholder="Search agencies…" data-table="agenciesTable">
      </div>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0" id="agenciesTable">
        <thead>
          <tr>
            <x-sortable-th column="code" label="Code" />
            <x-sortable-th column="name" label="Name" />
            <th>Contact</th>
            <x-sortable-th column="city" label="City" />
            <x-sortable-th column="enquiries" label="Enquiries" class="text-center" />
            <x-sortable-th column="bookings" label="Bookings" class="text-center" />
            <x-sortable-th column="status" label="Status" />
            <th></th>
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
              <td class="text-end">
                @can('update', $travelAgency)
                  <a href="{{ route('travel-agencies.edit', $travelAgency) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-secondary py-4">No travel agencies found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $travelAgencies])
  </div>
@endsection
