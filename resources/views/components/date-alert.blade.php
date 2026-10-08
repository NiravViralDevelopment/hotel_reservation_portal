@props([
    'date' => null,
    'empty' => '—',
    'window' => 30,
    'scale' => null,
])

@php
    use Illuminate\Support\Carbon;

    $parsed = null;
    if ($date instanceof \DateTimeInterface) {
        $parsed = Carbon::instance(\DateTimeImmutable::createFromInterface($date))->startOfDay();
    } elseif (filled($date)) {
        try {
            $parsed = Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            $parsed = null;
        }
    }

    $urgency = null;
    $hint = null;
    $daysLeft = null;

    if ($parsed) {
        $today = now()->startOfDay();
        // Signed day difference: negative = past, 0 = today, positive = future.
        $daysLeft = (int) $today->diffInDays($parsed, false);
        $window = max(1, (int) $window);

        if ($scale === 'option') {
            if ($daysLeft < 0) {
                $overdue = abs($daysLeft);
                $urgency = 'overdue';
                $hint = $overdue.' day'.($overdue === 1 ? '' : 's').' overdue';
            } elseif ($daysLeft <= 10) {
                $urgency = 'red';
                $hint = $daysLeft === 0
                    ? 'Today'
                    : $daysLeft.' day'.($daysLeft === 1 ? '' : 's').' left';
            } elseif ($daysLeft <= 15) {
                $urgency = 'yellow';
                $hint = $daysLeft.' days left';
            } elseif ($daysLeft <= 30) {
                $urgency = 'green';
                $hint = $daysLeft.' days left';
            }
        } elseif ($daysLeft < 0) {
            $overdue = abs($daysLeft);
            $urgency = 'overdue';
            $hint = $overdue.' day'.($overdue === 1 ? '' : 's').' overdue';
        } elseif ($daysLeft === 0) {
            $urgency = 'today';
            $hint = 'Today';
        } elseif ($daysLeft <= 3) {
            $urgency = 'urgent';
            $hint = $daysLeft.' day'.($daysLeft === 1 ? '' : 's').' left';
        } elseif ($daysLeft <= 7) {
            $urgency = 'soon';
            $hint = $daysLeft.' days left';
        } elseif ($daysLeft <= $window) {
            $urgency = 'upcoming';
            $hint = $daysLeft.' days left';
        }
    }
@endphp

@if ($parsed === null)
  <span class="text-secondary">{{ $empty }}</span>
@elseif ($urgency)
  <span class="date-alert date-alert-{{ $urgency }}" title="{{ $parsed->format('d M Y') }} · {{ $hint }}">
    <span class="date-alert-dot" aria-hidden="true"></span>
    <span class="date-alert-value">{{ $parsed->format('d M Y') }}</span>
    <span class="date-alert-hint">{{ $hint }}</span>
  </span>
@else
  <span class="text-nowrap">{{ $parsed->format('d M Y') }}</span>
@endif
