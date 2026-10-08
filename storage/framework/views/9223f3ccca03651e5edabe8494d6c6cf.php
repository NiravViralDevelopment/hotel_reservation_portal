<?php $__env->startSection('title', 'Arrivals'); ?>
<?php $__env->startSection('page', 'arrivals'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
        <li class="breadcrumb-item active">Arrivals</li>
      </ol>
    </nav>
    <h1 class="page-title">Arrivals</h1>
    <p class="page-subtitle">Groups arriving in <?php echo e($month->format('F Y')); ?>. They stay on this list until their departure date.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <?php echo $__env->make('stay-lists.filters', [
        'filterRoute' => 'arrivals.index',
        'monthInputId' => 'arrival_month',
        'monthValue' => $monthValue,
        'hotels' => $hotels,
        'travelAgencies' => $travelAgencies,
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php echo $__env->make('enquiries.partials.list-table', [
      'rows' => $bookings,
      'variant' => 'enquiry',
      'defaultSort' => 'check_in',
      'defaultDir' => 'asc',
      'emptyMessage' => 'No groups arriving this month are still to depart.',
      'readOnly' => true,
      'recordRoute' => 'arrivals.show',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="table-footer"><span><strong><?php echo e($bookings->count()); ?></strong> arrival(s)</span></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/arrivals/index.blade.php ENDPATH**/ ?>