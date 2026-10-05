@props([
    'date' => null,
    'empty' => '—',
])

@php
    use Illuminate\Support\Carbon;

    $parsed = null;
    if ($date instanceof Carbon) {
        $parsed = $date->copy()->startOfDay();
    } elseif (filled($date)) {
        try {
            $parsed = Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            $parsed = null;
        }
    }

    $urgency = null;
    $hint = null;
    if ($parsed) {
        $today = now()->startOfDay();
        $days = (int) $today->diffInDays($parsed, false);

        if ($days < 0) {
            $urgency = 'overdue';
            $hint = abs($days).' day'.(abs($days) === 1 ? '' : 's').' overdue';
        } elseif ($days === 0) {
            $urgency = 'today';
            $hint = 'Due today';
        } elseif ($days <= 3) {
            $urgency = 'urgent';
            $hint = 'In '.$days.' day'.($days === 1 ? '' : 's');
        } elseif ($days <= 7) {
            $urgency = 'soon';
            $hint = 'In '.$days.' days';
        }
    }
@endphp

@if ($parsed === null)
  <span class="text-secondary">{{ $empty }}</span>
@elseif ($urgency)
  <span class="date-alert date-alert-{{ $urgency }}" title="{{ $hint }}">
    <span class="date-alert-dot" aria-hidden="true"></span>
    <span class="date-alert-value">{{ $parsed->format('d M Y') }}</span>
    <span class="date-alert-hint">{{ $hint }}</span>
  </span>
@else
  <span class="text-nowrap">{{ $parsed->format('d M Y') }}</span>
@endif
