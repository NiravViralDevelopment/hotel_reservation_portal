<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'date' => null,
    'empty' => '—',
    'window' => 30,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'date' => null,
    'empty' => '—',
    'window' => 30,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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

        if ($daysLeft < 0) {
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
?>

<?php if($parsed === null): ?>
  <span class="text-secondary"><?php echo e($empty); ?></span>
<?php elseif($urgency): ?>
  <span class="date-alert date-alert-<?php echo e($urgency); ?>" title="<?php echo e($parsed->format('d M Y')); ?> · <?php echo e($hint); ?>">
    <span class="date-alert-dot" aria-hidden="true"></span>
    <span class="date-alert-value"><?php echo e($parsed->format('d M Y')); ?></span>
    <span class="date-alert-hint"><?php echo e($hint); ?></span>
  </span>
<?php else: ?>
  <span class="text-nowrap"><?php echo e($parsed->format('d M Y')); ?></span>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/components/date-alert.blade.php ENDPATH**/ ?>