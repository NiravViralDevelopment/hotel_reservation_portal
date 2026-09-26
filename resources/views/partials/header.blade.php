<header class="app-header">
  <button class="header-toggle" data-sidebar-toggle aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
  </button>

  <div class="header-search">
    <i class="bi bi-search"></i>
    <input type="search" placeholder="Search bookings, hotels, agencies…" aria-label="Global search" disabled>
  </div>

  <div class="header-actions">
    <button class="header-action-btn" data-theme-toggle data-bs-toggle="tooltip" title="Toggle theme" type="button">
      <i class="bi bi-moon-fill"></i>
    </button>

    <button class="header-action-btn" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas" title="Quick filters" type="button">
      <i class="bi bi-funnel"></i>
    </button>

    <div class="dropdown">
      <button class="header-action-btn" data-bs-toggle="dropdown" aria-label="Notifications" type="button">
        <i class="bi bi-bell"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
          <span class="fw-semibold">Notifications</span>
        </div>
        <div class="px-3 py-3 text-secondary small">No new notifications.</div>
      </div>
    </div>

    <div class="dropdown">
      <button class="header-user dropdown-toggle" data-bs-toggle="dropdown" type="button">
        <div class="header-user-avatar">{{ auth()->user()->initials() }}</div>
        <div class="header-user-info">
          <div class="header-user-name">{{ auth()->user()->name }}</div>
          <div class="header-user-role">{{ auth()->user()->job_title ?: 'User' }}</div>
        </div>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> My Profile</a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('settings.edit') }}"><i class="bi bi-gear"></i> Settings</a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="bi bi-box-arrow-left"></i> Sign Out
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</header>
