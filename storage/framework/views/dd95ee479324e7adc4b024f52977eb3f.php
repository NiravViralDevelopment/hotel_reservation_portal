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
        href="<?php echo e(route('enquiries.export', array_merge(request()->only(['q', 'hotel_id', 'travel_agency_id', 'status', 'sort', 'dir']), ['month' => $enquiryMonth]))); ?>"
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
      <form method="GET" action="<?php echo e(route('enquiries.index')); ?>" class="d-flex flex-wrap gap-2 align-items-center w-100">
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
        <select name="status" class="form-select form-select-sm select2" style="width:auto; min-width: 130px;">
          <option value="">All statuses</option>
          <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <div class="d-flex align-items-center gap-1">
          <label for="enquiry_month" class="form-label mb-0 small text-secondary">Month</label>
          <input type="month" name="month" id="enquiry_month" class="form-control form-control-sm" style="width:auto;" value="<?php echo e($enquiryMonth); ?>" title="Enquiry month">
        </div>
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        <?php if(request()->hasAny(['q', 'hotel_id', 'travel_agency_id', 'status', 'month'])): ?>
          <a href="<?php echo e(route('enquiries.index')); ?>" class="btn btn-outline-danger btn-sm">
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
    <?php echo $__env->make('enquiries.partials.list-table', [
      'rows' => $enquiries,
      'variant' => 'enquiry',
      'defaultSort' => 'enquiry_date',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No enquiries found.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.pagination-footer', ['paginator' => $enquiries], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\working\h_r_p\resources\views/enquiries/index.blade.php ENDPATH**/ ?>