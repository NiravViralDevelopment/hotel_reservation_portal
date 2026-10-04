@extends('layouts.app')

@section('title', 'Contracts')
@section('page', 'companies')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
          <li class="breadcrumb-item active">Contracts</li>
        </ol>
      </nav>
      <h1 class="page-title">Contracts</h1>
      <p class="page-subtitle">PDF contracts for {{ $company->name }}.</p>
    </div>
    @can('update', $company)
      <a href="{{ route('companies.contracts.create', $company) }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('companies.contracts.index', $company) }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="max-width: 260px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="form-control"
            placeholder="Search title or file…"
            aria-label="Search contracts"
          >
        </div>
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->filled('q'))
          <a href="{{ route('companies.contracts.index', $company) }}" class="btn btn-outline-danger btn-sm">
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
            <x-sortable-th column="title" label="Title" default="created_at" default-dir="desc" />
            <th>PDF</th>
            <x-sortable-th column="created_at" label="Uploaded" default="created_at" default-dir="desc" />
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($contracts as $contract)
            <tr>
              <td class="fw-semibold">{{ $contract->title }}</td>
              <td>
                <a href="{{ $contract->previewUrl() }}" target="_blank" rel="noopener" class="text-danger fs-4" title="{{ $contract->original_name }}">
                  <i class="bi bi-file-earmark-pdf"></i>
                </a>
              </td>
              <td>
                <div>{{ $contract->created_at?->format('d M Y') }}</div>
                @if ($contract->uploadedBy)
                  <div class="small text-secondary">{{ $contract->uploadedBy->name }}</div>
                @endif
              </td>
              <td class="text-end text-nowrap">
                <a href="{{ route('companies.contracts.download', [$company, $contract]) }}" class="btn btn-sm btn-outline-secondary" title="Download">
                  <i class="bi bi-download"></i>
                </a>
                @can('update', $company)
                  <form method="POST" action="{{ route('companies.contracts.destroy', [$company, $contract]) }}" class="d-inline" data-confirm-title="Delete contract" data-confirm="{{ "Are you sure you want to delete the contract \"{$contract->title}\"?\n\nThis will permanently remove it and cannot be undone." }}" data-confirm-button="Delete">
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
              <td colspan="4" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-file-earmark-pdf fs-3"></i></div>
                <div>No contracts yet.</div>
                @can('update', $company)
                  <a href="{{ route('companies.contracts.create', $company) }}" class="btn btn-accent btn-sm mt-2">
                    <i class="bi bi-plus-lg"></i> Add
                  </a>
                @endcan
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $contracts])
  </div>
@endsection
