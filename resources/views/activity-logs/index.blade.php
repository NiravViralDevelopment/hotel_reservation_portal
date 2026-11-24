@extends('layouts.app')

@section('title', 'Activity logs')
@section('page', 'activity-logs')

@push('styles')
  <style>
    .activity-filters {
      flex: 1 1 100%;
      width: 100%;
      min-width: 0;
      container-type: inline-size;
    }

    .activity-filters-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 0.85rem 0.75rem;
      align-items: end;
    }

    .activity-filter-field > .form-control,
    .activity-filter-field .form-select,
    .activity-filter-field .input-group,
    .activity-filter-field .date-placeholder-wrap,
    .activity-filter-field .date-placeholder-wrap input {
      width: 100%;
    }

    .activity-filters .input-group {
      flex-wrap: nowrap;
    }

    .activity-filters .search-input {
      max-width: none;
      min-width: 0;
    }

    .activity-filters .select2-container {
      width: 100% !important;
      min-width: 0 !important;
      max-width: 100% !important;
    }

    .activity-filter-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      align-items: center;
    }

    @container (max-width: 860px) {
      .activity-filters-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @container (max-width: 480px) {
      .activity-filters-grid {
        grid-template-columns: minmax(0, 1fr);
      }
    }

    .activity-feed {
      padding: 0.75rem 1.25rem 1.25rem;
      background:
        linear-gradient(180deg, rgba(243, 246, 251, 0.65) 0%, rgba(255, 255, 255, 0) 120px),
        var(--bg-surface);
    }

    .activity-day {
      margin-top: 1.15rem;
    }

    .activity-day:first-child {
      margin-top: 0.35rem;
    }

    .activity-day-heading {
      position: sticky;
      top: 0;
      z-index: 2;
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      margin: 0 0 0.85rem;
      padding: 0.35rem 0.7rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid var(--border-color);
      box-shadow: var(--shadow-sm);
      backdrop-filter: blur(6px);
    }

    .activity-day-heading strong {
      font-size: 0.8125rem;
      font-weight: 700;
      color: var(--text-primary);
      letter-spacing: 0.01em;
    }

    .activity-day-heading span {
      font-size: 0.72rem;
      font-weight: 600;
      color: var(--text-muted);
    }

    .activity-list {
      display: flex;
      flex-direction: column;
      gap: 0.7rem;
    }

    .activity-card {
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: 0.95rem;
      align-items: start;
      padding: 1rem 1.05rem;
      border: 1px solid var(--border-color);
      border-radius: 0.85rem;
      background: var(--bg-surface);
      box-shadow: 0 1px 2px rgba(2, 34, 90, 0.03);
      transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }

    .activity-card:hover {
      border-color: rgba(26, 77, 156, 0.28);
      box-shadow: var(--shadow-md);
      transform: translateY(-1px);
    }

    .activity-icon {
      width: 2.55rem;
      height: 2.55rem;
      border-radius: 0.8rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.05rem;
      flex-shrink: 0;
      background: #eef3fb;
      color: var(--brand-accent);
    }

    .activity-icon.tone-success {
      background: #e7f7ee;
      color: #0d7a47;
    }

    .activity-icon.tone-info {
      background: #eaf2ff;
      color: #1a4d9c;
    }

    .activity-icon.tone-warning {
      background: #fff5e6;
      color: #a86200;
    }

    .activity-icon.tone-danger {
      background: #fdeceb;
      color: #b83227;
    }

    .activity-icon.tone-neutral {
      background: #eef2f7;
      color: #516074;
    }

    .activity-main {
      min-width: 0;
    }

    .activity-title {
      margin: 0 0 0.45rem;
      font-size: 0.95rem;
      font-weight: 600;
      line-height: 1.45;
      color: var(--text-primary);
      letter-spacing: -0.01em;
    }

    .activity-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 0.4rem;
      margin-bottom: 0.65rem;
    }

    .activity-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.18rem 0.55rem;
      border-radius: 0.4rem;
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.02em;
      text-transform: uppercase;
      background: #f3f6fb;
      color: var(--text-secondary);
      border: 1px solid transparent;
    }

    .activity-tag.module {
      background: rgba(2, 34, 90, 0.05);
      color: var(--brand-primary);
    }

    .activity-tag.action {
      background: rgba(26, 77, 156, 0.08);
      color: var(--brand-accent);
    }

    .activity-actor {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      min-width: 0;
    }

    .activity-avatar {
      width: 1.85rem;
      height: 1.85rem;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      color: #fff;
      background: linear-gradient(145deg, #1a4d9c, #02225a);
    }

    .activity-actor-copy {
      min-width: 0;
      line-height: 1.25;
    }

    .activity-actor-name {
      display: block;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-primary);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .activity-actor-email {
      display: block;
      font-size: 0.72rem;
      color: var(--text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .activity-when {
      text-align: right;
      min-width: 4.75rem;
      padding-top: 0.1rem;
    }

    .activity-when-time {
      display: block;
      font-size: 0.92rem;
      font-weight: 700;
      color: var(--text-primary);
      font-variant-numeric: tabular-nums;
      letter-spacing: -0.01em;
    }

    .activity-when-date {
      display: block;
      margin-top: 0.15rem;
      font-size: 0.7rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .activity-empty {
      padding: 3.5rem 1.5rem;
      text-align: center;
    }

    .activity-empty-icon {
      width: 3.5rem;
      height: 3.5rem;
      margin: 0 auto 1rem;
      border-radius: 1rem;
      display: grid;
      place-items: center;
      background: #eef3fb;
      color: var(--brand-accent);
      font-size: 1.45rem;
    }

    .activity-empty h2 {
      margin: 0 0 0.35rem;
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--text-primary);
    }

    .activity-empty p {
      margin: 0;
      color: var(--text-secondary);
      font-size: 0.875rem;
    }

    @media (max-width: 768px) {
      .activity-feed {
        padding: 0.65rem 0.85rem 1rem;
      }

      .activity-card {
        grid-template-columns: auto 1fr;
        gap: 0.75rem;
      }

      .activity-when {
        grid-column: 2;
        text-align: left;
        min-width: 0;
        display: flex;
        align-items: baseline;
        gap: 0.45rem;
        padding-top: 0;
        order: 3;
      }

      .activity-when-date {
        margin-top: 0;
      }

      .activity-main {
        order: 2;
      }
    }
  </style>
@endpush

@section('content')
  @php
    $hasFilters = request()->hasAny(['q', 'module', 'action', 'user_id', 'date_from', 'date_to']);
    $groupedLogs = $logs->getCollection()->groupBy(function ($log) {
      return $log->created_at?->toDateString() ?? 'unknown';
    });
  @endphp

  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Activity logs</li>
      </ol>
    </nav>
    <h1 class="page-title">Activity logs</h1>
    <p class="page-subtitle">
      @if ($canViewAll)
        Chronological history of actions across all users.
      @else
        A chronological history of your actions in the system.
      @endif
    </p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('activity-logs.index') }}" class="activity-filters">
        <div class="activity-filters-grid">
          <div class="activity-filter-field">
            <label for="activity_q" class="enquiry-filter-label">Search</label>
            <div class="input-group input-group-sm search-input">
              <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input
                type="search"
                name="q"
                id="activity_q"
                class="form-control form-control-sm border-start-0"
                placeholder="Search activity"
                value="{{ request('q') }}"
              >
            </div>
          </div>

          <div class="activity-filter-field">
            <label for="activity_module" class="enquiry-filter-label">Module</label>
            <select name="module" id="activity_module" class="form-select form-select-sm select2" data-width="100%">
              <option value="">All modules</option>
              @foreach ($modules as $module)
                <option value="{{ $module }}" @selected(request('module') === $module)>
                  {{ \App\Support\ActivityDescription::moduleLabel($module) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="activity-filter-field">
            <label for="activity_action" class="enquiry-filter-label">Action</label>
            <select name="action" id="activity_action" class="form-select form-select-sm select2" data-width="100%">
              <option value="">All actions</option>
              @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>
                  {{ ucfirst(str_replace('_', ' ', $action)) }}
                </option>
              @endforeach
            </select>
          </div>

          @if ($canViewAll)
            <div class="activity-filter-field">
              <label for="activity_user_id" class="enquiry-filter-label">User</label>
              <select name="user_id" id="activity_user_id" class="form-select form-select-sm select2" data-width="100%">
                <option value="">All users</option>
                @foreach ($users as $activityUser)
                  <option value="{{ $activityUser->id }}" @selected((string) request('user_id') === (string) $activityUser->id)>
                    {{ $activityUser->name }}
                  </option>
                @endforeach
              </select>
            </div>
          @endif

          <div class="activity-filter-field">
            <label for="activity_date_from" class="enquiry-filter-label">From</label>
            <div class="date-placeholder-wrap">
              <input
                type="date"
                name="date_from"
                id="activity_date_from"
                class="form-control form-control-sm"
                value="{{ request('date_from') }}"
                title="Activity from"
              >
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
          </div>

          <div class="activity-filter-field">
            <label for="activity_date_to" class="enquiry-filter-label">To</label>
            <div class="date-placeholder-wrap">
              <input
                type="date"
                name="date_to"
                id="activity_date_to"
                class="form-control form-control-sm"
                value="{{ request('date_to') }}"
                title="Activity to"
                @if (request('date_from')) min="{{ request('date_from') }}" @endif
              >
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
          </div>

          <div class="activity-filter-actions">
            <button type="submit" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-funnel"></i> Filter
            </button>
            @if ($hasFilters)
              <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            @endif
          </div>
        </div>
      </form>
    </div>

    @if ($logs->isEmpty())
      <div class="activity-empty">
        <div class="activity-empty-icon" aria-hidden="true">
          <i class="bi bi-clock-history"></i>
        </div>
        <h2>No activity to show</h2>
        <p>
          @if ($hasFilters)
            Try adjusting your filters to find matching activity.
          @else
            Activity will appear here as users work in the system.
          @endif
        </p>
      </div>
    @else
      <div class="activity-feed">
        @foreach ($groupedLogs as $dateKey => $dayLogs)
          @php
            $dayDate = $dateKey !== 'unknown' ? \Illuminate\Support\Carbon::parse($dateKey) : null;
            if ($dayDate?->isToday()) {
              $dayLabel = 'Today';
            } elseif ($dayDate?->isYesterday()) {
              $dayLabel = 'Yesterday';
            } else {
              $dayLabel = $dayDate?->format('l') ?? 'Unknown';
            }
            $daySubLabel = $dayDate?->format('d M Y') ?? '';
          @endphp

          <section class="activity-day" aria-label="{{ $dayLabel }}{{ $daySubLabel ? ', '.$daySubLabel : '' }}">
            <div class="activity-day-heading">
              <strong>{{ $dayLabel }}</strong>
              @if ($daySubLabel !== '')
                <span>{{ $daySubLabel }}</span>
              @endif
            </div>

            <div class="activity-list">
              @foreach ($dayLogs as $log)
                <article class="activity-card">
                  <div class="activity-icon tone-{{ $log->tone }}" aria-hidden="true">
                    <i class="bi bi-{{ $log->icon }}"></i>
                  </div>

                  <div class="activity-main">
                    <p class="activity-title">{{ $log->description }}</p>

                    <div class="activity-tags">
                      <span class="activity-tag module">
                        <i class="bi bi-folder2-open"></i>
                        {{ $log->module_label }}
                      </span>
                      <span class="activity-tag action">
                        <i class="bi bi-lightning-charge"></i>
                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                      </span>
                    </div>

                    @if ($canViewAll)
                      <div class="activity-actor">
                        <span class="activity-avatar" aria-hidden="true">
                          {{ $log->user?->initials() ?: 'SY' }}
                        </span>
                        <div class="activity-actor-copy">
                          <span class="activity-actor-name">{{ $log->user?->name ?? 'System' }}</span>
                          @if ($log->user?->email)
                            <span class="activity-actor-email">{{ $log->user->email }}</span>
                          @endif
                        </div>
                      </div>
                    @endif
                  </div>

                  <div class="activity-when">
                    <time datetime="{{ $log->created_at?->toIso8601String() }}">
                      <span class="activity-when-time">{{ $log->created_at?->format('H:i') ?? '—' }}</span>
                      <span class="activity-when-date">{{ $log->created_at?->format('d M') ?? '' }}</span>
                    </time>
                  </div>
                </article>
              @endforeach
            </div>
          </section>
        @endforeach
      </div>
    @endif

    @include('partials.pagination-footer', ['paginator' => $logs])
  </div>
@endsection
