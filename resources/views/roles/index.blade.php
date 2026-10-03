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
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <x-sortable-th column="name" label="Role" />
            <x-sortable-th column="users" label="Users" class="text-center" />
            <th>Permissions</th>
            <th class="text-end">Actions</th>
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
                  <span class="badge bg-light text-dark border me-1">{{ $role->permissions->count() }} assigned</span>
                  <span class="text-muted small">{{ $role->permissions->pluck('name')->take(4)->join(', ') }}{{ $role->permissions->count() > 4 ? '…' : '' }}</span>
                @endif
              </td>
              <td class="text-end text-nowrap">
                @can('roles.edit')
                  <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-shield-check fs-3"></i></div>
                <div>No roles found.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $roles])
  </div>
@endsection
