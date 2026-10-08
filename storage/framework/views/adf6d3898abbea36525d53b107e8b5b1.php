<?php $__env->startSection('title', 'Departures'); ?>
<?php $__env->startSection('page', 'departures'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
        <li class="breadcrumb-item active">Departures</li>
      </ol>
    </nav>
    <h1 class="page-title">Departures</h1>
    <p class="page-subtitle">Groups whose departure date is today or earlier, filtered by that date.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <?php echo $__env->make('stay-lists.filters', [
        'filterRoute' => 'departures.index',
        'dateInputPrefix' => 'departure',
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'hotels' => $hotels,
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php echo $__env->make('enquiries.partials.list-table', [
      'rows' => $bookings,
      'variant' => 'enquiry',
      'defaultSort' => 'check_out',
      'defaultDir' => 'asc',
      'emptyMessage' => 'No groups have a departure date in this range that is today or earlier.',
      'readOnly' => true,
      'recordRoute' => 'departures.show',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="table-footer"><span><strong><?php echo e($bookings->count()); ?></strong> departure(s)</span></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/departures/index.blade.php ENDPATH**/ ?>