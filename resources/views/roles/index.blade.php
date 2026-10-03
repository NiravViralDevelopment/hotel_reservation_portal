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
    @can('roles.edit')
      <a href="{{ route('roles.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add role</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('roles.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="max-width: 260px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="form-control"
            placeholder="Search role or permission…"
            aria-label="Search roles"
          >
        </div>

        <select name="permission" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All permissions</option>
          @foreach ($permissions as $permission)
            <option value="{{ $permission->name }}" @selected(request('permission') === $permission->name)>{{ $permission->name }}</option>
          @endforeach
        </select>

        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>

        @if (request()->hasAny(['q', 'permission']))
          <a href="{{ route('roles.index') }}" class="btn btn-outline-danger btn-sm">
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
                  @if ($role->name !== 'Administrator')
                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline" data-confirm-title="Delete role" data-confirm="{{ "Are you sure you want to delete the role \"{$role->name}\"?\n\nUsers with this role may lose their permissions. This cannot be undone." }}" data-confirm-button="Delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove role">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  @endif
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-shield-check fs-3"></i></div>
                <div>No roles match your filters.</div>
                @if (request()->hasAny(['q', 'permission']))
                  <a href="{{ route('roles.index') }}" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="bi bi-x-circle"></i> Clear filters
                  </a>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $roles])
  </div>
@endsection
