@extends('layouts.app')

@section('title', 'Users')
@section('page', 'users')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Users</li>
        </ol>
      </nav>
      <h1 class="page-title">Users</h1>
      <p class="page-subtitle">Manage accounts, roles, and hotel access.</p>
    </div>
    @can('create', App\Models\User::class)
      <a href="{{ route('users.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add user</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('users.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="max-width: 260px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="form-control"
            placeholder="Search name, email, phone…"
            aria-label="Search users"
          >
        </div>

        <select name="status" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All statuses</option>
          <option value="active" @selected(request('status') === 'active')>Active</option>
          <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>

        <select name="role" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All roles</option>
          @foreach ($roles as $role)
            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
          @endforeach
        </select>

        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>

        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>

        @if (request()->hasAny(['q', 'status', 'role', 'hotel_id']))
          <a href="{{ route('users.index') }}" class="btn btn-outline-danger btn-sm">
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
            <x-sortable-th column="name" label="User" />
            <x-sortable-th column="email" label="Contact" />
            <th>Role</th>
            <th>Hotels</th>
            <x-sortable-th column="status" label="Status" />
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              <td>
                <div class="fw-semibold">{{ $user->name }}</div>
                @if ($user->job_title)
                  <div class="small text-secondary">{{ $user->job_title }}</div>
                @endif
              </td>
              <td>
                <div><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></div>
                @if ($user->phone)
                  <div class="small text-secondary">{{ $user->phone }}</div>
                @endif
              </td>
              <td>
                @forelse ($user->roles as $role)
                  <span class="badge bg-secondary me-1 mb-1">{{ $role->name }}</span>
                @empty
                  <span class="text-secondary">—</span>
                @endforelse
              </td>
              <td>
                @forelse ($user->hotels as $hotel)
                  <span class="badge bg-light text-dark border me-1 mb-1">{{ $hotel->name }}</span>
                @empty
                  <span class="text-secondary">No hotels assigned</span>
                @endforelse
              </td>
              <td><x-badge-status :status="$user->status" /></td>
              <td class="text-end text-nowrap">
                @can('update', $user)
                  <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit user">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                  @if ($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="d-inline" data-confirm-title="{{ $user->status === 'active' ? 'Deactivate user' : 'Activate user' }}" data-confirm="{{ $user->status === 'active' ? "Are you sure you want to deactivate \"{$user->name}\"?\n\nThey will not be able to sign in until activated again." : "Are you sure you want to activate \"{$user->name}\"?\n\nThey will be able to sign in again." }}" data-confirm-button="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}" data-confirm-variant="warning" data-confirm-icon="bi-person-slash">
                      @csrf
                      @method('PATCH')
                      @if ($user->status === 'active')
                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Deactivate user">
                          <i class="bi bi-person-dash"></i>
                        </button>
                      @else
                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activate user">
                          <i class="bi bi-person-check"></i>
                        </button>
                      @endif
                    </form>
                  @endif
                @endcan
                @can('delete', $user)
                  @if ($user->canBeDeleted())
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" data-confirm-title="Delete user" data-confirm="{{ "Are you sure you want to delete the user \"{$user->name}\"?\n\nThis will permanently remove their account and cannot be undone." }}" data-confirm-button="Delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete user">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  @endif
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-people fs-3"></i></div>
                <div>No users match your filters.</div>
                @if (request()->hasAny(['q', 'status', 'role', 'hotel_id']))
                  <a href="{{ route('users.index') }}" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="bi bi-x-circle"></i> Clear filters
                  </a>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $users])
  </div>
@endsection
