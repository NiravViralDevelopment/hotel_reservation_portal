<?php $__env->startSection('title', 'Enquiries'); ?>
<?php $__env->startSection('page', 'enquiries'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item active">Enquiries</li>
        </ol>
      </nav>
      <h1 class="page-title">Enquiries</h1>
      <p class="page-subtitle">Watch Arrival, Option Date, and CXL Due Date highlights so deadlines are not missed.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a
        href="<?php echo e(route('enquiries.export', array_merge(request()->only(['q', 'hotel_id', 'status', 'created_by', 'sort', 'dir', 'enquiry_from', 'enquiry_to', 'enquiry_month']), (! request()->exists('enquiry_from') && ! request()->exists('enquiry_to') && ! request()->exists('enquiry_month')) ? ['enquiry_from' => $enquiryFrom, 'enquiry_to' => $enquiryTo] : []))); ?>"
        class="btn btn-outline-secondary btn-sm"
        title="Download the filtered list as Excel"
      >
        <i class="bi bi-download"></i> Export Excel
      </a>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Enquiry::class)): ?>
        <a href="<?php echo e(route('enquiries.create')); ?>" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add enquiry</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="enquiry-reminder-grid">
    <div class="enquiry-reminder-card is-info">
      <div class="label">Arrivals (next 7 days)</div>
      <div class="value"><?php echo e(number_format($reminders['arrivals'] ?? 0)); ?></div>
      <div class="meta">Highlighted in the Arrival Date column</div>
    </div>
    <div class="enquiry-reminder-card <?php echo e(($reminders['options_overdue'] ?? 0) > 0 ? 'is-danger' : 'is-warning'); ?>">
      <div class="label">Option dates due</div>
      <div class="value"><?php echo e(number_format($reminders['options'] ?? 0)); ?></div>
      <div class="meta">
        <?php if(($reminders['options_overdue'] ?? 0) > 0): ?>
          <?php echo e($reminders['options_overdue']); ?> overdue
        <?php else: ?>
          Within 7 days / overdue
        <?php endif; ?>
      </div>
    </div>
    <div class="enquiry-reminder-card <?php echo e(($reminders['cxl_overdue'] ?? 0) > 0 ? 'is-danger' : 'is-warning'); ?>">
      <div class="label">CXL due dates</div>
      <div class="value"><?php echo e(number_format($reminders['cxl'] ?? 0)); ?></div>
      <div class="meta">
        <?php if(($reminders['cxl_overdue'] ?? 0) > 0): ?>
          <?php echo e($reminders['cxl_overdue']); ?> overdue
        <?php else: ?>
          Within 7 days / overdue
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="<?php echo e(route('enquiries.index')); ?>" class="enquiry-filters">
        <div class="enquiry-filters-grid">
          <div class="enquiry-filter-field">
            <label for="enquiry_q" class="enquiry-filter-label">Search</label>
            <div class="input-group input-group-sm search-input">
              <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input
                type="search"
                name="q"
                id="enquiry_q"
                class="form-control form-control-sm border-start-0"
                placeholder="Search"
                value="<?php echo e(request('q')); ?>"
              >
            </div>
          </div>
          <div class="enquiry-filter-field">
            <label for="enquiry_hotel_id" class="enquiry-filter-label">Hotel</label>
            <select name="hotel_id" id="enquiry_hotel_id" class="form-select form-select-sm select2" data-width="100%">
              <option value="">All hotels</option>
              <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($hotel->id); ?>" <?php if((string) request('hotel_id') === (string) $hotel->id): echo 'selected'; endif; ?>><?php echo e($hotel->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="enquiry-filter-field">
            <label for="enquiry_status" class="enquiry-filter-label">Status</label>
            <select name="status" id="enquiry_status" class="form-select form-select-sm select2" data-width="100%">
              <option value="">All statuses</option>
              <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="enquiry-filter-field">
            <label for="enquiry_created_by" class="enquiry-filter-label">Created by</label>
            <select name="created_by" id="enquiry_created_by" class="form-select form-select-sm select2" data-width="100%">
              <option value="">Created by</option>
              <?php $__currentLoopData = $creators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $creator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($creator->id); ?>" <?php if((string) request('created_by') === (string) $creator->id): echo 'selected'; endif; ?>><?php echo e($creator->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="enquiry-filter-field enquiry-filter-month">
            <label for="enquiry_month" class="enquiry-filter-label">Month</label>
            <input
              type="month"
              name="enquiry_month"
              id="enquiry_month"
              class="form-control form-control-sm"
              value="<?php echo e($enquiryMonth); ?>"
              title="Filter by enquiry month"
            >
          </div>
          <div class="enquiry-filter-field">
            <label for="enquiry_from" class="enquiry-filter-label">Start date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="enquiry_from" id="enquiry_from" class="form-control form-control-sm" value="<?php echo e($enquiryFrom); ?>" title="Enquiry date from">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
          </div>
          <div class="enquiry-filter-field">
            <label for="enquiry_to" class="enquiry-filter-label">End date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="enquiry_to" id="enquiry_to" class="form-control form-control-sm" value="<?php echo e($enquiryTo); ?>" title="Enquiry date to" <?php if($enquiryFrom): ?> min="<?php echo e($enquiryFrom); ?>" <?php endif; ?>>
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
          </div>
          <div class="enquiry-filter-actions">
            <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            <?php if(request()->hasAny(['q', 'hotel_id', 'status', 'created_by', 'enquiry_from', 'enquiry_to', 'enquiry_month'])): ?>
              <a href="<?php echo e(route('enquiries.index')); ?>" class="btn btn-outline-danger btn-sm">
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
    </div>
    <?php echo $__env->make('enquiries.partials.list-table', [
      'rows' => $enquiries,
      'variant' => 'enquiry',
      'defaultSort' => 'enquiry_date',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No enquiries found.',
      'showCreatedBy' => true,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.pagination-footer', ['paginator' => $enquiries], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <script>
    (function () {
      var month = document.getElementById('enquiry_month');
      var start = document.getElementById('enquiry_from');
      var end = document.getElementById('enquiry_to');
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/enquiries/index.blade.php ENDPATH**/ ?>