<?php $__env->startSection('title', 'Add enquiry'); ?>
<?php $__env->startSection('page', 'enquiries'); ?>

<?php $__env->startPush('styles'); ?>
  <?php echo $__env->make('enquiries.partials.entry-form-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('enquiries.index')); ?>">Enquiries</a></li>
        <li class="breadcrumb-item active">Add enquiry</li>
      </ol>
    </nav>
    <h1 class="page-title">Add enquiry</h1>
    <p class="page-subtitle mb-0">Work through the sections below. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="<?php echo e(route('enquiries.store')); ?>" class="enquiry-form" data-enquiry-create="1" novalidate data-existing-pairs='<?php echo json_encode($existingPairs, 15, 512) ?>'>
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('enquiries.partials.entry-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="enquiry-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Save enquiry</button>
      <a href="<?php echo e(route('enquiries.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <?php echo $__env->make('enquiries.partials.entry-form-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/enquiries/create.blade.php ENDPATH**/ ?>