@php
  $nav = [
    [
      'section' => 'Main',
      'items' => [
        ['route' => 'dashboard', 'icon' => 'speedometer2', 'label' => 'Dashboard'],
        ['route' => 'hotels.index', 'icon' => 'building', 'label' => 'Hotels'],
        ['route' => 'companies.index', 'icon' => 'briefcase', 'label' => 'Companies'],
        ['route' => 'travel-agencies.index', 'icon' => 'airplane', 'label' => 'Travel Agencies'],
        ['route' => 'contacts.index', 'icon' => 'person-lines-fill', 'label' => 'Contacts'],
      ],
    ],
    [
      'section' => 'Bookings',
      'items' => [
        ['route' => 'group-bookings.index', 'icon' => 'calendar-check', 'label' => 'Group Bookings'],
        ['route' => 'enquiries.index', 'icon' => 'chat-square-text', 'label' => 'Enquiries'],
        ['route' => 'cancelled-bookings.index', 'icon' => 'x-circle', 'label' => 'Cancelled Bookings'],
        ['route' => 'arrivals.index', 'icon' => 'box-arrow-in-right', 'label' => 'Arrivals'],
        ['route' => 'departures.index', 'icon' => 'box-arrow-right', 'label' => 'Departures'],
        ['route' => 'calendar.index', 'icon' => 'calendar3', 'label' => 'Calendar'],
      ],
    ],
    [
      'section' => 'Finance',
      'items' => [
        ['route' => 'revenue.index', 'icon' => 'currency-pound', 'label' => 'Revenue'],
        ['route' => 'reports.index', 'icon' => 'file-earmark-bar-graph', 'label' => 'Reports'],
        ['route' => 'documents.index', 'icon' => 'folder2-open', 'label' => 'Documents'],
      ],
    ],
  ];

  $adminItems = [
    ['route' => 'users.index', 'icon' => 'people', 'label' => 'Users', 'can' => 'viewAny', 'model' => \App\Models\User::class],
    ['route' => 'roles.index', 'icon' => 'shield-check', 'label' => 'Roles', 'can' => 'viewAny', 'model' => \App\Models\User::class],
    ['route' => 'audit-logs.index', 'icon' => 'journal-text', 'label' => 'Audit Logs', 'can' => 'viewAny', 'model' => \App\Models\User::class],
    ['route' => 'settings.edit', 'icon' => 'gear', 'label' => 'Settings', 'can' => 'viewAny', 'model' => \App\Models\User::class],
  ];
@endphp

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="app-sidebar" id="appSidebar">
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon"><i class="bi bi-building"></i></div>
    <span class="sidebar-brand-text">HGBMS</span>
  </div>

  <nav class="sidebar-nav">
    @foreach ($nav as $group)
      <div class="nav-section-label">{{ $group['section'] }}</div>
      @foreach ($group['items'] as $item)
        @php
          $routeBase = \Illuminate\Support\Str::contains($item['route'], '.')
            ? \Illuminate\Support\Str::beforeLast($item['route'], '.').'.*'
            : $item['route'];
          $isActive = request()->routeIs($item['route'], $routeBase);
        @endphp
        <a href="{{ route($item['route']) }}" class="sidebar-link{{ $isActive ? ' active' : '' }}" title="{{ $item['label'] }}">
          <i class="bi bi-{{ $item['icon'] }}"></i>
          <span class="sidebar-link-text">{{ $item['label'] }}</span>
        </a>
      @endforeach
    @endforeach

    @can('viewAny', \App\Models\User::class)
      <div class="nav-section-label">Administration</div>
      @foreach ($adminItems as $item)
        @php
          $routeBase = \Illuminate\Support\Str::contains($item['route'], '.')
            ? \Illuminate\Support\Str::beforeLast($item['route'], '.').'.*'
            : $item['route'];
          $isActive = request()->routeIs($item['route'], $routeBase);
        @endphp
        <a href="{{ route($item['route']) }}" class="sidebar-link{{ $isActive ? ' active' : '' }}" title="{{ $item['label'] }}">
          <i class="bi bi-{{ $item['icon'] }}"></i>
          <span class="sidebar-link-text">{{ $item['label'] }}</span>
        </a>
      @endforeach
    @endcan
  </nav>

  <div class="sidebar-footer">
    <a href="{{ route('profile.edit') }}" class="sidebar-link{{ request()->routeIs('profile.*') ? ' active' : '' }}" title="Profile">
      <i class="bi bi-person-circle"></i><span class="sidebar-link-text">Profile</span>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="m-0">
      @csrf
      <button type="submit" class="sidebar-link border-0 bg-transparent w-100 text-start">
        <i class="bi bi-box-arrow-left"></i><span class="sidebar-link-text">Logout</span>
      </button>
    </form>
  </div>
</aside>
