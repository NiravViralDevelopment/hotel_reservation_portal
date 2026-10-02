<header class="app-header">
  <button class="header-toggle" data-sidebar-toggle aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
  </button>

  <div class="header-spacer flex-grow-1"></div>

  <div class="header-actions">
    @if (! empty($accessibleHotels) && $accessibleHotels->isNotEmpty())
      <div class="dropdown me-2">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-building"></i>
          <span class="text-truncate" style="max-width: 180px;">
            {{ ! empty($currentHotel) ? $currentHotel->name : 'All hotels' }}
          </span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><h6 class="dropdown-header">Switch hotel</h6></li>
          @foreach ($accessibleHotels as $hotel)
            <li>
              <form method="POST" action="{{ route('hotel-context.switch') }}">
                @csrf
                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                <button type="submit" class="dropdown-item {{ ! empty($currentHotel) && (int) $hotel->id === (int) $currentHotel->id ? 'active' : '' }}">
                  {{ $hotel->name }}
                  @if ($hotel->code)
                    <span class="text-secondary small">({{ $hotel->code }})</span>
                  @endif
                </button>
              </form>
            </li>
          @endforeach
          @if (auth()->user()?->hasRole('Administrator'))
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('hotel-context.clear') }}">
                @csrf
                <button type="submit" class="dropdown-item {{ empty($currentHotel) ? 'active' : '' }}">
                  <i class="bi bi-grid"></i> All hotels
                </button>
              </form>
            </li>
          @elseif (! empty($currentHotel))
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('hotel-context.clear') }}">
                @csrf
                <button type="submit" class="dropdown-item">
                  <i class="bi bi-grid"></i> Change hotel
                </button>
              </form>
            </li>
          @endif
        </ul>
      </div>
    @endif

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
          <div class="header-user-role">{{ auth()->user()->getRoleNames()->first() ?: 'User' }}</div>
        </div>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> My Profile</a>
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
