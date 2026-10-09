<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status' => '']));

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

foreach (array_filter((['status' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $raw = is_object($status) && enum_exists($status::class) ? $status->value : (string) $status;
    $normalized = strtolower(preg_replace('/[\s_\-]+/', '', $raw));
    $map = [
        'confirmed' => 'confirmed',
        'active' => 'active',
        'paid' => 'paid',
        'provisional' => 'pending',
        'pending' => 'pending',
        'partial' => 'partial',
        'unpaid' => 'unpaid',
        'cancelled' => 'cancelled',
        'new' => 'enquiry',
        'followup' => 'pending',
        'quoted' => 'partial',
        'lost' => 'cancelled',
        'inactive' => 'inactive',
        'def' => 'confirmed',
        'open' => 'enquiry',
        'closed' => 'inactive',
    ];
    $cls = $map[$normalized] ?? 'pending';
    $display = ucwords(str_replace(['_', '-'], ' ', $raw));
?>

<span <?php echo e($attributes->merge(['class' => 'badge-status badge-'.$cls])); ?>><?php echo e($display); ?></span>
<?php /**PATH D:\working\h_r_p\resources\views/components/badge-status.blade.php ENDPATH**/ ?>