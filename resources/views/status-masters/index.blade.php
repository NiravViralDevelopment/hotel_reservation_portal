@extends('layouts.app')

@section('title', 'Status master')
@section('page', 'status-masters')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Status master</li>
        </ol>
      </nav>
      <h1 class="page-title">Status master</h1>
      <p class="page-subtitle">Manage enquiry statuses used across the system.</p>
    </div>
    @can('create', App\Models\StatusMaster::class)
      <a href="{{ route('status-masters.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add status</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('status-masters.index') }}" class="d-flex gap-2 align-items-center flex-nowrap">
        <div class="input-group input-group-sm" style="width: 240px; flex-shrink: 0;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="search" name="q" class="form-control" placeholder="Search status…" value="{{ request('q') }}">
        </div>
        <select name="status" class="form-select form-select-sm select2" style="width: 140px;">
          <option value="">All</option>
          <option value="active" @selected(request('status') === 'active')>Active</option>
          <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm flex-shrink-0"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->hasAny(['q', 'status']))
          <a href="{{ route('status-masters.index') }}" class="btn btn-outline-danger btn-sm flex-shrink-0"><i class="bi bi-x-circle"></i> Clear</a>
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
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <x-sortable-th column="title" label="Status title" />
            <x-sortable-th column="status" label="Active / Inactive" />
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($statusMasters as $statusMaster)
            <tr>
              <td class="fw-semibold">{{ ucwords(str_replace('_', ' ', $statusMaster->title)) }}</td>
              <td><x-badge-status :status="$statusMaster->status" /></td>
              <td class="text-end text-nowrap">
                @can('update', $statusMaster)
                  <a href="{{ route('status-masters.edit', $statusMaster) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                @endcan
                @can('delete', $statusMaster)
                  <form method="POST" action="{{ route('status-masters.destroy', $statusMaster) }}" class="d-inline" data-confirm-title="Delete status" data-confirm="{{ "Are you sure you want to delete the status \"{$statusMaster->title}\"?\n\nThis will permanently remove it and cannot be undone." }}" data-confirm-button="Delete">
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
            <tr><td colspan="3" class="text-center text-secondary py-4">No statuses found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $statusMasters])
  </div>
@endsection
