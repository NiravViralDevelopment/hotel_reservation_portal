@php
  $nav = [
    [
      'section' => 'Main',
      'items' => [
        ['route' => 'dashboard', 'icon' => 'speedometer2', 'label' => 'Dashboard', 'permission' => 'dashboard.view'],
        ['route' => 'hotels.index', 'icon' => 'building', 'label' => 'Hotels', 'permission' => 'hotels.view'],
        ['route' => 'companies.index', 'icon' => 'briefcase', 'label' => 'Companies', 'permission' => 'companies.view'],
        ['route' => 'travel-agencies.index', 'icon' => 'airplane', 'label' => 'Travel Agencies', 'permission' => 'agencies.view'],
      ],
    ],
    [
      'section' => 'Bookings',
      'items' => [
        ['route' => 'group-bookings.index', 'icon' => 'calendar-check', 'label' => 'Group Bookings', 'permission' => 'bookings.view'],
        ['route' => 'enquiries.index', 'icon' => 'chat-square-text', 'label' => 'Enquiries', 'permission' => 'enquiries.view'],
        ['route' => 'cancelled-inquiries.index', 'icon' => 'x-octagon', 'label' => 'Cancelled Inquiry', 'permission' => 'enquiries.view'],
        ['route' => 'cancelled-bookings.index', 'icon' => 'x-circle', 'label' => 'Cancelled Bookings', 'permission' => 'bookings.view'],
        ['route' => 'arrivals.index', 'icon' => 'box-arrow-in-right', 'label' => 'Arrivals', 'permission' => 'bookings.view'],
        ['route' => 'departures.index', 'icon' => 'box-arrow-right', 'label' => 'Departures', 'permission' => 'bookings.view'],
        ['route' => 'calendar.index', 'icon' => 'calendar3', 'label' => 'Calendar', 'permission' => 'bookings.view'],
      ],
    ],
    [
      'section' => 'Finance',
      'items' => [
        ['route' => 'revenue.index', 'icon' => 'currency-pound', 'label' => 'Revenue', 'permission' => 'revenue.view'],
        ['route' => 'reports.index', 'icon' => 'file-earmark-bar-graph', 'label' => 'Reports', 'permission' => 'reports.view', 'exact' => true],
      ],
    ],
    [
      'section' => 'Reports',
      'items' => [
        ['route' => 'reports.module', 'params' => ['report' => 'group-bookings'], 'icon' => 'journal-check', 'label' => 'Group bookings report', 'permission' => 'reports.view'],
        ['route' => 'reports.module', 'params' => ['report' => 'enquiries'], 'icon' => 'inbox', 'label' => 'Enquiries report', 'permission' => 'reports.view'],
        ['route' => 'reports.module', 'params' => ['report' => 'cancelled-bookings'], 'icon' => 'x-circle', 'label' => 'Cancelled bookings report', 'permission' => 'reports.view'],
      ],
    ],
  ];

  $adminItems = [
    ['route' => 'status-masters.index', 'icon' => 'list-check', 'label' => 'Status Master', 'permission' => 'statuses.view'],
    ['route' => 'users.index', 'icon' => 'people', 'label' => 'Users', 'permission' => 'users.view'],
    ['route' => 'roles.index', 'icon' => 'shield-check', 'label' => 'Roles', 'permission' => 'roles.view'],
    ['route' => 'audit-logs.index', 'icon' => 'journal-text', 'label' => 'Audit Logs', 'permission' => 'audit.view'],
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
      @php
        $visibleItems = collect($group['items'])->filter(fn ($item) => auth()->user()->can($item['permission']));
      @endphp
      @if ($visibleItems->isNotEmpty())
        <div class="nav-section-label">{{ $group['section'] }}</div>
        @foreach ($visibleItems as $item)
          @php
            $params = $item['params'] ?? [];
            if ($params !== []) {
              $isActive = request()->routeIs($item['route'])
                && collect($params)->every(fn ($value, $key) => (string) request()->route($key) === (string) $value);
            } elseif (! empty($item['exact'])) {
              $isActive = request()->routeIs($item['route']);
            } else {
              $routeBase = \Illuminate\Support\Str::contains($item['route'], '.')
                ? \Illuminate\Support\Str::beforeLast($item['route'], '.').'.*'
                : $item['route'];
              $isActive = request()->routeIs($item['route'], $routeBase);
            }
          @endphp
          <a href="{{ route($item['route'], $params) }}" class="sidebar-link{{ $isActive ? ' active' : '' }}" title="{{ $item['label'] }}">
            <i class="bi bi-{{ $item['icon'] }}"></i>
            <span class="sidebar-link-text">{{ $item['label'] }}</span>
          </a>
        @endforeach
      @endif
    @endforeach

    @php
      $visibleAdminItems = collect($adminItems)->filter(fn ($item) => auth()->user()->can($item['permission']));
    @endphp
    @if ($visibleAdminItems->isNotEmpty())
      <div class="nav-section-label">Administration</div>
      @foreach ($visibleAdminItems as $item)
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
    @endif
  </nav>
</aside>
