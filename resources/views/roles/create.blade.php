@extends('layouts.app')

@section('title', 'Add role')
@section('page', 'roles')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
          <li class="breadcrumb-item active">Add role</li>
        </ol>
      </nav>
      <h1 class="page-title">Add role</h1>
      <p class="page-subtitle">Create a role and assign permissions.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('roles.store') }}">
    @csrf

    <div class="card mb-4">
      <div class="card-header">Role details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Role name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="125" placeholder="Enter role name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Permissions</div>
      <div class="card-body">
        <p class="small text-secondary mb-3">
          Tip: enable the <strong>.view</strong> permission for a module (e.g. <code>hotels.view</code>) so it appears in the sidebar.
        </p>
        @php $selected = old('permissions', []); @endphp
        <div class="row g-2">
          @foreach ($permissions as $permission)
            <div class="col-md-4 col-lg-3">
              <div class="form-check">
                <input class="form-check-input @error('permissions') is-invalid @enderror" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                  @checked(in_array($permission->name, $selected, true))>
                <label class="form-check-label small" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
              </div>
            </div>
          @endforeach
        </div>
        @error('permissions')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">Save role</button>
      <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
