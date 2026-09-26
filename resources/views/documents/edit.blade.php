@extends('layouts.app')

@section('title', 'Edit document')
@section('page', 'documents')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
        <li class="breadcrumb-item active">{{ $document->name }}</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit document</h1>
  </div>

  <form method="POST" action="{{ route('documents.update', $document) }}">
    @csrf
    @method('PUT')
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Display name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $document->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $document->category) }}" required>
            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-12">
            <label for="group_booking_id" class="form-label">Group booking</label>
            <select name="group_booking_id" id="group_booking_id" class="form-select @error('group_booking_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($groupBookings as $booking)
                <option value="{{ $booking->id }}" @selected(old('group_booking_id', $document->group_booking_id) == $booking->id)>{{ $booking->block_id }} — {{ $booking->group_name }}</option>
              @endforeach
            </select>
            @error('group_booking_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent">Update</button>
      <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
