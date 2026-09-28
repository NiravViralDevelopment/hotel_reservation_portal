@props([
    'column',
    'label',
    'default' => 'name',
    'defaultDir' => 'asc',
])

@php
    $currentSort = request('sort', $default);
    $currentDir = strtolower((string) request('dir', $defaultDir)) === 'desc' ? 'desc' : 'asc';
    $isActive = $currentSort === $column;
    $nextDir = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
    $query = array_merge(request()->except('page'), [
        'sort' => $column,
        'dir' => $nextDir,
    ]);
@endphp

<th {{ $attributes->merge(['class' => 'sortable-th']) }}>
  <a href="{{ url()->current() }}?{{ http_build_query($query) }}" class="sortable-link text-decoration-none text-reset d-inline-flex align-items-center gap-1">
    <span>{{ $label }}</span>
    @if ($isActive)
      <i class="bi bi-chevron-{{ $currentDir === 'asc' ? 'up' : 'down' }} sortable-icon active"></i>
    @else
      <i class="bi bi-arrow-down-up sortable-icon"></i>
    @endif
  </a>
</th>
