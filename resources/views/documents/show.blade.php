@extends('layouts.app')

@section('title', $document->name)
@section('page', 'documents')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
          <li class="breadcrumb-item active">{{ $document->name }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $document->name }}</h1>
      <p class="page-subtitle">{{ $document->category }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('documents.download', $document) }}" class="btn btn-accent btn-sm"><i class="bi bi-download"></i> Download</a>
      @can('update', $document)
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><div class="info-card-label">Uploaded by</div><div class="info-card-value">{{ $document->uploadedBy?->name ?? '—' }}</div></div>
        <div class="col-md-4"><div class="info-card-label">Uploaded</div><div class="info-card-value">{{ $document->created_at?->format('d M Y H:i') ?? '—' }}</div></div>
        <div class="col-md-4"><div class="info-card-label">MIME type</div><div class="info-card-value">{{ $document->mime_type ?? '—' }}</div></div>
        <div class="col-md-4"><div class="info-card-label">Size</div><div class="info-card-value">@if($document->size){{ number_format($document->size / 1024, 1) }} KB @else — @endif</div></div>
      </div>
    </div>
  </div>
@endsection
