@extends('layouts.app')

@section('title', 'Edit role')
@section('page', 'roles')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
          <li class="breadcrumb-item active">{{ $role->name }}</li>
        </ol>
      </nav>
      <h1 class="page-title">Edit role permissions</h1>
      <p class="page-subtitle">{{ $role->name }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('roles.update', $role) }}">
    @csrf
    @method('PUT')
    <div class="card mb-4">
      <div class="card-header">Permissions</div>
      <div class="card-body">
        @php
          $selected = old('permissions', $role->permissions->pluck('name')->all());
        @endphp
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
      <button type="submit" class="btn btn-accent">Save permissions</button>
      <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
