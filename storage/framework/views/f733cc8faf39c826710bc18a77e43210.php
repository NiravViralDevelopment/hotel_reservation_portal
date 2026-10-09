<?php $__env->startSection('title', 'Group bookings'); ?>
<?php $__env->startSection('page', 'group-bookings'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item active">Group bookings</li>
        </ol>
      </nav>
      <h1 class="page-title">Group bookings</h1>
      <p class="page-subtitle">Watch arrival, payment due, and CXL due date highlights so deadlines are not missed.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a
        href="<?php echo e(route('group-bookings.export', request()->only(['q', 'hotel_id', 'travel_agency_id', 'arrival_from', 'arrival_to', 'sort', 'dir']))); ?>"
        class="btn btn-outline-secondary btn-sm"
        title="Download the filtered list as Excel"
      >
        <i class="bi bi-download"></i> Export Excel
      </a>
      <button type="button" class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#importGroupBookingsModal">
        <i class="bi bi-upload"></i> Import Excel
      </button>
    </div>
  </div>

  <div class="enquiry-reminder-grid">
    <div class="enquiry-reminder-card is-info">
      <div class="label">Arrivals (next 7 days)</div>
      <div class="value"><?php echo e(number_format($reminders['arrivals'] ?? 0)); ?></div>
      <div class="meta">Highlighted in the Date of Arrival column</div>
    </div>
    <div class="enquiry-reminder-card <?php echo e(($reminders['payments_overdue'] ?? 0) > 0 ? 'is-danger' : 'is-warning'); ?>">
      <div class="label">Payment due dates</div>
      <div class="value"><?php echo e(number_format($reminders['payments'] ?? 0)); ?></div>
      <div class="meta">
        <?php if(($reminders['payments_overdue'] ?? 0) > 0): ?>
          <?php echo e($reminders['payments_overdue']); ?> overdue
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

  <div class="modal fade" id="importGroupBookingsModal" tabindex="-1" aria-labelledby="importGroupBookingsLabel" aria-hidden="true" <?php if($errors->has('file')): ?> data-open-on-load="1" <?php endif; ?>>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="<?php echo e(route('group-bookings.import')); ?>" enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <div class="modal-header">
            <h5 class="modal-title" id="importGroupBookingsLabel">Import group bookings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-secondary small mb-3">
              Upload an Excel file mapped to enquiry fields. Every imported row is saved with
              <strong>is_confirm = 1</strong> and shown here.
            </p>
            <div class="mb-3">
              <label for="import_file" class="form-label">Excel file <span class="text-danger">*</span></label>
              <input type="file" name="file" id="import_file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".xlsx,.xls" required>
              <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="mb-0">
              <label for="import_hotel_id" class="form-label">Hotel</label>
              <select name="hotel_id" id="import_hotel_id" class="form-select">
                <option value="">Current / first hotel</option>
                <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($hotel->id); ?>" <?php if((string) request('hotel_id') === (string) $hotel->id): echo 'selected'; endif; ?>><?php echo e($hotel->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-accent"><i class="bi bi-upload"></i> Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="<?php echo e(route('group-bookings.index')); ?>" class="d-flex flex-wrap gap-2 align-items-center w-100">
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
        <select name="travel_agency_id" class="form-select form-select-sm select2" style="width:auto; min-width: 160px;">
          <option value="">All agencies</option>
          <?php $__currentLoopData = $travelAgencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($agency->id); ?>" <?php if((string) request('travel_agency_id') === (string) $agency->id): echo 'selected'; endif; ?>><?php echo e($agency->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="date" name="arrival_from" class="form-control form-control-sm" style="width:auto;" value="<?php echo e(request('arrival_from')); ?>" title="Arrival from">
        <input type="date" name="arrival_to" class="form-control form-control-sm" style="width:auto;" value="<?php echo e(request('arrival_to')); ?>" title="Arrival to">
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        <?php if(request()->hasAny(['q', 'hotel_id', 'travel_agency_id', 'arrival_from', 'arrival_to'])): ?>
          <a href="<?php echo e(route('group-bookings.index')); ?>" class="btn btn-outline-danger btn-sm">
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
    </div>

    <?php echo $__env->make('group-bookings.partials.list-table', [
      'rows' => $bookings,
      'highlightDates' => true,
      'defaultSort' => 'check_in',
      'defaultDir' => 'asc',
      'emptyMessage' => 'No confirmed bookings found.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('partials.pagination-footer', ['paginator' => $bookings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
  var modalEl = document.getElementById('importGroupBookingsModal');
  if (modalEl && modalEl.getAttribute('data-open-on-load') === '1' && window.bootstrap) {
    new bootstrap.Modal(modalEl).show();
  }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\working\h_r_p\resources\views/group-bookings/index.blade.php ENDPATH**/ ?>