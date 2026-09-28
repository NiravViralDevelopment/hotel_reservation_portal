@extends('layouts.app')

@section('title', 'Documents')
@section('page', 'documents')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Documents</li>
        </ol>
      </nav>
      <h1 class="page-title">Documents</h1>
      <p class="page-subtitle">Contracts, rooming lists, and uploaded files.</p>
    </div>
    @can('create', App\Models\Document::class)
      <a href="{{ route('documents.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-upload"></i> Upload</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <div class="input-group search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0 table-search" placeholder="Search documents…" data-table="documentsTable">
      </div>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0" id="documentsTable">
        <thead>
          <tr>
            <x-sortable-th column="name" label="Name" default="created_at" default-dir="desc" />
            <x-sortable-th column="category" label="Category" default="created_at" default-dir="desc" />
            <th>Booking</th>
            <th>Uploaded by</th>
            <x-sortable-th column="created_at" label="Date" default="created_at" default-dir="desc" />
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($documents as $document)
            <tr>
              <td class="fw-semibold"><a href="{{ route('documents.show', $document) }}">{{ $document->name }}</a></td>
              <td>{{ $document->category }}</td>
              <td>
                @if ($document->groupBooking)
                  <a href="{{ route('group-bookings.show', $document->groupBooking) }}">{{ $document->groupBooking->block_id }}</a>
                @else
                  —
                @endif
              </td>
              <td>{{ $document->uploadedBy?->name ?? '—' }}</td>
              <td>{{ $document->created_at?->format('d M Y') ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-secondary">Download</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary py-4">No documents found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $documents])
  </div>
@endsection
