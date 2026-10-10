<?php $__env->startSection('title', 'Cancelled bookings'); ?>
<?php $__env->startSection('page', 'cancelled-bookings'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item active">Cancelled bookings</li>
        </ol>
      </nav>
      <h1 class="page-title">Cancelled bookings</h1>
      <p class="page-subtitle">Cancelled group bookings, with the cancellation reason.</p>
    </div>
    <a
      href="<?php echo e(route('cancelled-bookings.export', array_merge(request()->only(['q', 'hotel_id', 'sort', 'dir', 'date_from', 'date_to', 'date_month']), (! request()->exists('date_from') && ! request()->exists('date_to') && ! request()->exists('date_month')) ? ['date_from' => $dateFrom, 'date_to' => $dateTo] : []))); ?>"
      class="btn btn-outline-secondary btn-sm"
      title="Download the filtered list as Excel"
    >
      <i class="bi bi-download"></i> Export Excel
    </a>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <?php echo $__env->make('stay-lists.filters', [
        'filterRoute' => 'cancelled-bookings.index',
        'dateInputPrefix' => 'cancelled_booking',
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'hotels' => $hotels,
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <?php echo $__env->make('group-bookings.partials.list-table', [
      'rows' => $bookings,
      'showCancellationReason' => true,
      'defaultSort' => 'updated_at',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No cancelled bookings found.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('partials.pagination-footer', ['paginator' => $bookings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/cancelled-bookings/index.blade.php ENDPATH**/ ?>