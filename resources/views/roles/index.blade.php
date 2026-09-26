@extends('layouts.app')

@section('title', 'Roles')
@section('page', 'roles')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Roles</li>
        </ol>
      </nav>
      <h1 class="page-title">Roles</h1>
      <p class="page-subtitle">Role definitions and permission assignments.</p>
    </div>
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Role</th>
            <th class="text-center">Users</th>
            <th>Permissions</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roles as $role)
            <tr>
              <td class="fw-semibold">{{ $role->name }}</td>
              <td class="text-center">{{ $role->users_count }}</td>
              <td>
                @if ($role->permissions->isEmpty())
                  <span class="text-secondary">None</span>
                @else
                  <span class="text-muted small">{{ $role->permissions->pluck('name')->take(5)->join(', ') }}{{ $role->permissions->count() > 5 ? '…' : '' }}</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">Edit permissions</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-secondary py-4">No roles found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
