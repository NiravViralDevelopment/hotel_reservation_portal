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
      href="<?php echo e(route('cancelled-inquiries.export', request()->only(['q', 'hotel_id', 'travel_agency_id', 'month', 'sort', 'dir']))); ?>"
      class="btn btn-outline-secondary btn-sm"
      title="Download the filtered list as Excel"
    >
      <i class="bi bi-download"></i> Export Excel
    </a>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <?php echo $__env->make('stay-lists.filters', [
        'filterRoute' => 'cancelled-inquiries.index',
        'monthInputId' => 'cancelled_inquiry_month',
        'monthValue' => $monthValue,
        'hotels' => $hotels,
        'travelAgencies' => $travelAgencies,
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/cancelled-inquiries/index.blade.php ENDPATH**/ ?>