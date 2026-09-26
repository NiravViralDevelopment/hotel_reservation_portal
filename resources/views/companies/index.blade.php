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
      <div class="input-group search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0 table-search" placeholder="Search companies…" data-table="companiesTable">
      </div>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0" id="companiesTable">
        <thead>
          <tr>
            <th>Company name</th>
            <th>Reg. number</th>
            <th>City</th>
            <th>Country</th>
            <th class="text-center">Hotels</th>
            <th class="text-center">Contacts</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($companies as $company)
            <tr>
              <td class="fw-semibold"><a href="{{ route('companies.show', $company) }}">{{ $company->name }}</a></td>
              <td>{{ $company->reg_number ?? '—' }}</td>
              <td>{{ $company->city ?? '—' }}</td>
              <td>{{ $company->country ?? '—' }}</td>
              <td class="text-center">{{ $company->hotels_count }}</td>
              <td class="text-center">{{ $company->contacts_count }}</td>
              <td><x-badge-status :status="$company->status" /></td>
              <td class="text-end">
                @can('update', $company)
                  <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-secondary py-4">No companies found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $companies])
  </div>
@endsection
