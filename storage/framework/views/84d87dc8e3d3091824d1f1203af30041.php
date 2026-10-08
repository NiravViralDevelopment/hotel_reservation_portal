<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['paginator']));

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

foreach (array_filter((['paginator']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages()): ?>
  <div class="table-footer">
    <span>
      Showing <strong><?php echo e($paginator->firstItem() ?? 0); ?></strong> to
      <strong><?php echo e($paginator->lastItem() ?? 0); ?></strong> of
      <strong><?php echo e($paginator->total()); ?></strong> entries
    </span>
    <?php echo e($paginator->links('pagination::bootstrap-5')); ?>

  </div>
<?php elseif($paginator instanceof \Illuminate\Contracts\Pagination\Paginator): ?>
  <div class="table-footer">
    <span>
      Showing <strong><?php echo e($paginator->count()); ?></strong> of
      <strong><?php echo e($paginator->total()); ?></strong> entries
    </span>
  </div>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/partials/pagination-footer.blade.php ENDPATH**/ ?>