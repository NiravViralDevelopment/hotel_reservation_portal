<?php $__env->startSection('title', 'Edit group booking'); ?>
<?php $__env->startSection('page', 'group-bookings'); ?>

<?php $__env->startPush('styles'); ?>
  <?php echo $__env->make('enquiries.partials.entry-form-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('group-bookings.index')); ?>">Group Bookings</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit group booking</h1>
    <p class="page-subtitle mb-0"><?php echo e($enquiry->group_name ?: ($enquiry->block_id ?: 'Group booking')); ?></p>
  </div>

  <?php echo $__env->make('enquiries.partials.group-booking-form', [
    'groupBookingBackUrl' => route('group-bookings.show', $enquiry),
  ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <?php echo $__env->make('enquiries.partials.entry-form-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('enquiries.partials.group-booking-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php if($errors->has('cancellation_reason') && old('cancel_scope') === 'group'): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('cancelGroupBookingModal');
        if (modalEl && window.bootstrap) {
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      });
    </script>
  <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\working\h_r_p\resources\views/group-bookings/edit.blade.php ENDPATH**/ ?>