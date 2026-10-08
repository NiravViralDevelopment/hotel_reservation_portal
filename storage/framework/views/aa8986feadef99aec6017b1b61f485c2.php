<form method="GET" action="<?php echo e(route($filterRoute)); ?>" class="d-flex flex-wrap gap-2 align-items-center w-100">
  <div class="input-group search-input" style="min-width: 200px; max-width: 280px;">
    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
    <input
      type="search"
      name="q"
      class="form-control border-start-0"
      placeholder="Search ref, group, client, email, mobile…"
      value="<?php echo e(request('q')); ?>"
    >
  </div>
  <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto; min-width: 140px;">
    <option value="">All hotels</option>
    <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($hotel->id); ?>" <?php if((string) request('hotel_id') === (string) $hotel->id): echo 'selected'; endif; ?>><?php echo e($hotel->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
  <?php if(! empty($statuses ?? null)): ?>
    <select name="status" class="form-select form-select-sm select2" style="width:auto; min-width: 130px;">
      <option value="">All statuses</option>
      <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  <?php endif; ?>
  <?php
    $dateFromId = ($dateInputPrefix ?? 'list').'_from';
    $dateToId = ($dateInputPrefix ?? 'list').'_to';
  ?>
  <div class="d-flex align-items-center gap-1">
    <label for="<?php echo e($dateFromId); ?>" class="form-label mb-0 small text-secondary">Start date</label>
    <div class="date-placeholder-wrap">
      <input type="date" name="date_from" id="<?php echo e($dateFromId); ?>" class="form-control form-control-sm" style="width:auto;" value="<?php echo e($dateFrom); ?>" title="Start date">
      <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-1">
    <label for="<?php echo e($dateToId); ?>" class="form-label mb-0 small text-secondary">End date</label>
    <div class="date-placeholder-wrap">
      <input type="date" name="date_to" id="<?php echo e($dateToId); ?>" class="form-control form-control-sm" style="width:auto;" value="<?php echo e($dateTo); ?>" title="End date" <?php if($dateFrom): ?> min="<?php echo e($dateFrom); ?>" <?php endif; ?>>
      <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
    </div>
  </div>
  <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
  <?php if(request()->hasAny(! empty($statuses ?? null) ? ['q', 'hotel_id', 'status', 'date_from', 'date_to'] : ['q', 'hotel_id', 'date_from', 'date_to'])): ?>
    <a href="<?php echo e(route($filterRoute)); ?>" class="btn btn-outline-danger btn-sm">
      <i class="bi bi-x-circle"></i> Clear
    </a>
  <?php endif; ?>
  <?php if(request('sort')): ?>
    <input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>">
  <?php endif; ?>
  <?php if(request('dir')): ?>
    <input type="hidden" name="dir" value="<?php echo e(request('dir')); ?>">
  <?php endif; ?>
</form>
<script>
  document.getElementById(<?php echo json_encode($dateFromId, 15, 512) ?>)?.addEventListener('change', function () {
    var end = document.getElementById(<?php echo json_encode($dateToId, 15, 512) ?>);
    if (!end) return;
    end.min = this.value || '';
    if (this.value && end.value && end.value < this.value) {
      end.value = this.value;
    }
  });
</script>
<?php /**PATH E:\Working\hotel_reservation_portal\resources\views/stay-lists/filters.blade.php ENDPATH**/ ?>