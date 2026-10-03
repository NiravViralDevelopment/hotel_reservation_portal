@extends('layouts.app')

@section('title', 'Upload document')
@section('page', 'documents')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
        <li class="breadcrumb-item active">Upload</li>
      </ol>
    </nav>
    <h1 class="page-title">Upload document</h1>
  </div>

  <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Display name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter display name" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', 'contract') }}" placeholder="Enter category" required>
            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="file" class="form-label">File <span class="text-danger">*</span></label>
            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Max 10 MB.</div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent">Upload</button>
      <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
