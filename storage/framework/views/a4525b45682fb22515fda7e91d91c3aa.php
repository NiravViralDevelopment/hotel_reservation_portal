<?php $__env->startSection('title', 'Cancelled Inquiry'); ?>
<?php $__env->startSection('page', 'enquiries'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item active">Cancelled Inquiry</li>
        </ol>
      </nav>
      <h1 class="page-title">Cancelled Inquiry</h1>
      <p class="page-subtitle">Enquiries cancelled from the edit screen, with the cancellation reason.</p>
    </div>
    <a
      href="<?php echo e(route('cancelled-inquiries.export', request()->only(['q', 'hotel_id', 'travel_agency_id', 'response', 'enquiry_date_from', 'enquiry_date_to', 'sort', 'dir']))); ?>"
      class="btn btn-outline-secondary btn-sm"
      title="Download the filtered list as Excel"
    >
      <i class="bi bi-download"></i> Export Excel
    </a>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="<?php echo e(route('cancelled-inquiries.index')); ?>" class="d-flex flex-wrap gap-2 align-items-center w-100">
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
        <select name="response" class="form-select form-select-sm select2" style="width:auto; min-width: 150px;">
          <option value="">All responses</option>
          <option value="awaiting" <?php if(request('response') === 'awaiting'): echo 'selected'; endif; ?>>Awaiting response</option>
          <option value="received" <?php if(request('response') === 'received'): echo 'selected'; endif; ?>>Response received</option>
        </select>
        <input type="date" name="enquiry_date_from" class="form-control form-control-sm" style="width:auto;" value="<?php echo e(request('enquiry_date_from')); ?>" title="Enquiry date from">
        <input type="date" name="enquiry_date_to" class="form-control form-control-sm" style="width:auto;" value="<?php echo e(request('enquiry_date_to')); ?>" title="Enquiry date to">
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        <?php if(request()->hasAny(['q', 'hotel_id', 'travel_agency_id', 'response', 'enquiry_date_from', 'enquiry_date_to'])): ?>
          <a href="<?php echo e(route('cancelled-inquiries.index')); ?>" class="btn btn-outline-danger btn-sm">
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
      'showCancellationReason' => true,
      'defaultSort' => 'updated_at',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No cancelled inquiries found.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.pagination-footer', ['paginator' => $enquiries], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/cancelled-inquiries/index.blade.php ENDPATH**/ ?>