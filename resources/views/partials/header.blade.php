<header class="app-header">
  <button class="header-toggle" data-sidebar-toggle aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
  </button>

  <div class="header-spacer flex-grow-1"></div>

  <div class="header-actions">
    @if (isset($accessibleHotels) && $accessibleHotels->isNotEmpty())
      <form method="POST" action="{{ route('hotel-context.switch') }}" class="d-flex align-items-center gap-2 me-2">
        @csrf
        <label for="current_hotel_id" class="form-label mb-0 small text-secondary text-nowrap">Current Hotel</label>
        <select name="hotel_id" id="current_hotel_id" class="form-select form-select-sm select2" style="min-width: 180px; width:auto" data-width="style">
          @foreach ($accessibleHotels as $hotel)
            <option value="{{ $hotel->id }}" @selected((int) $currentHotelId === (int) $hotel->id)>
              {{ $hotel->name }}
            </option>
          @endforeach
        </select>
      </form>
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
