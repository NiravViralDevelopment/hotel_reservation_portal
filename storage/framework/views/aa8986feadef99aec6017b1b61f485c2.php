<?php
  $prefix = $dateInputPrefix ?? 'list';
  $searchId = $prefix.'_q';
  $hotelSelectId = $prefix.'_hotel_id';
  $statusSelectId = $prefix.'_status';
  $dateMonthId = $prefix.'_month';
  $dateFromId = $prefix.'_from';
  $dateToId = $prefix.'_to';
  $dateMonth = \App\Support\EnquiryIndexFilters::monthValue(request(), $dateFrom ?? null, $dateTo ?? null, 'date_month');
  $clearKeys = ! empty($statuses ?? null)
      ? ['q', 'hotel_id', 'status', 'date_from', 'date_to', 'date_month']
      : ['q', 'hotel_id', 'date_from', 'date_to', 'date_month'];
?>
<form method="GET" action="<?php echo e(route($filterRoute)); ?>" class="enquiry-filters">
  <div class="enquiry-filters-grid">
    <div class="enquiry-filter-field">
      <label for="<?php echo e($searchId); ?>" class="enquiry-filter-label">Search</label>
      <div class="input-group input-group-sm search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input
          type="search"
          name="q"
          id="<?php echo e($searchId); ?>"
          class="form-control form-control-sm border-start-0"
          placeholder="Search"
          value="<?php echo e(request('q')); ?>"
        >
      </div>
    </div>
    <div class="enquiry-filter-field">
      <label for="<?php echo e($hotelSelectId); ?>" class="enquiry-filter-label">Hotel</label>
      <select name="hotel_id" id="<?php echo e($hotelSelectId); ?>" class="form-select form-select-sm select2" data-width="100%">
        <option value="">All hotels</option>
        <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($hotel->id); ?>" <?php if((string) request('hotel_id') === (string) $hotel->id): echo 'selected'; endif; ?>><?php echo e($hotel->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <?php if(! empty($statuses ?? null)): ?>
      <div class="enquiry-filter-field">
        <label for="<?php echo e($statusSelectId); ?>" class="enquiry-filter-label">Status</label>
        <select name="status" id="<?php echo e($statusSelectId); ?>" class="form-select form-select-sm select2" data-width="100%">
          <option value="">All statuses</option>
          <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    <?php endif; ?>
    <div class="enquiry-filter-field enquiry-filter-month">
      <label for="<?php echo e($dateMonthId); ?>" class="enquiry-filter-label">Month</label>
      <input
        type="month"
        name="date_month"
        id="<?php echo e($dateMonthId); ?>"
        class="form-control form-control-sm"
        value="<?php echo e($dateMonth); ?>"
        title="Filter by month"
      >
    </div>
    <div class="enquiry-filter-field">
      <label for="<?php echo e($dateFromId); ?>" class="enquiry-filter-label">Start date</label>
      <div class="date-placeholder-wrap">
        <input type="date" name="date_from" id="<?php echo e($dateFromId); ?>" class="form-control form-control-sm" value="<?php echo e($dateFrom); ?>" title="Start date">
        <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
      </div>
    </div>
    <div class="enquiry-filter-field">
      <label for="<?php echo e($dateToId); ?>" class="enquiry-filter-label">End date</label>
      <div class="date-placeholder-wrap">
        <input type="date" name="date_to" id="<?php echo e($dateToId); ?>" class="form-control form-control-sm" value="<?php echo e($dateTo); ?>" title="End date" <?php if($dateFrom): ?> min="<?php echo e($dateFrom); ?>" <?php endif; ?>>
        <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
      </div>
    </div>
    <div class="enquiry-filter-actions">
      <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
      <?php if(request()->hasAny($clearKeys)): ?>
        <a href="<?php echo e(route($filterRoute)); ?>" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-x-circle"></i> Clear
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php if(request('sort')): ?>
    <input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>">
  <?php endif; ?>
  <?php if(request('dir')): ?>
    <input type="hidden" name="dir" value="<?php echo e(request('dir')); ?>">
  <?php endif; ?>
</form>
<script>
  (function () {
    var month = document.getElementById(<?php echo json_encode($dateMonthId, 15, 512) ?>);
    var start = document.getElementById(<?php echo json_encode($dateFromId, 15, 512) ?>);
    var end = document.getElementById(<?php echo json_encode($dateToId, 15, 512) ?>);
    if (!month || !start || !end) return;

    function monthBounds(value) {
      var parts = String(value || '').split('-');
      if (parts.length !== 2) return null;
      var year = parseInt(parts[0], 10);
      var mon = parseInt(parts[1], 10);
      if (!year || mon < 1 || mon > 12) return null;
      var from = year + '-' + String(mon).padStart(2, '0') + '-01';
      var last = new Date(year, mon, 0).getDate();
      var to = year + '-' + String(mon).padStart(2, '0') + '-' + String(last).padStart(2, '0');
      return { from: from, to: to };
    }

    function syncPlaceholder(input) {
      input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function syncMonthFromDates() {
      if (!start.value || !end.value) {
        month.value = '';
        return;
      }
      var bounds = monthBounds(start.value.slice(0, 7));
      if (bounds && start.value === bounds.from && end.value === bounds.to && start.value.slice(0, 7) === end.value.slice(0, 7)) {
        month.value = start.value.slice(0, 7);
      } else {
        month.value = '';
      }
    }

    month.addEventListener('change', function () {
      if (!month.value) return;
      var bounds = monthBounds(month.value);
      if (!bounds) return;
      start.value = bounds.from;
      end.value = bounds.to;
      end.min = bounds.from;
      syncPlaceholder(start);
      syncPlaceholder(end);
    });

    start.addEventListener('change', function () {
      end.min = start.value || '';
      if (start.value && end.value && end.value < start.value) {
        end.value = start.value;
        syncPlaceholder(end);
      }
      syncMonthFromDates();
    });

    end.addEventListener('change', syncMonthFromDates);
  })();
</script>
<?php /**PATH E:\Working\hotel_reservation_portal\resources\views/stay-lists/filters.blade.php ENDPATH**/ ?>