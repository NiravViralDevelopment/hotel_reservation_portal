<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'column',
    'label',
    'default' => 'name',
    'defaultDir' => 'asc',
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
    'column',
    'label',
    'default' => 'name',
    'defaultDir' => 'asc',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $currentSort = request('sort', $default);
    $currentDir = strtolower((string) request('dir', $defaultDir)) === 'desc' ? 'desc' : 'asc';
    $isActive = $currentSort === $column;
    $nextDir = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
    $query = array_merge(request()->except('page'), [
        'sort' => $column,
        'dir' => $nextDir,
    ]);
?>

<th <?php echo e($attributes->merge(['class' => 'sortable-th'])); ?>>
  <a href="<?php echo e(url()->current()); ?>?<?php echo e(http_build_query($query)); ?>" class="sortable-link text-decoration-none text-reset d-inline-flex align-items-center gap-1">
    <span><?php echo e($label); ?></span>
    <?php if($isActive): ?>
      <i class="bi bi-chevron-<?php echo e($currentDir === 'asc' ? 'up' : 'down'); ?> sortable-icon active"></i>
    <?php else: ?>
      <i class="bi bi-arrow-down-up sortable-icon"></i>
    <?php endif; ?>
  </a>
</th>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/components/sortable-th.blade.php ENDPATH**/ ?>