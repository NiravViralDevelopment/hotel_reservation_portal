<?php $__env->startSection('title', ($enquiry->is_cancel && ! $enquiry->is_confirm) ? 'Edit cancelled inquiry' : 'Edit enquiry'); ?>
<?php $__env->startSection('page', ($enquiry->is_cancel && ! $enquiry->is_confirm) ? 'cancelled-inquiries' : 'enquiries'); ?>

<?php $__env->startPush('styles'); ?>
  <?php echo $__env->make('enquiries.partials.entry-form-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e($enquiry->is_cancel && ! $enquiry->is_confirm ? route('cancelled-inquiries.index') : route('enquiries.index')); ?>"><?php echo e($enquiry->is_cancel && ! $enquiry->is_confirm ? 'Cancelled Inquiry' : 'Enquiries'); ?></a></li>
        <li class="breadcrumb-item active">Edit enquiry</li>
      </ol>
    </nav>
    <h1 class="page-title"><?php echo e($enquiry->is_cancel && ! $enquiry->is_confirm ? 'Edit cancelled inquiry' : 'Edit enquiry'); ?></h1>
    <p class="page-subtitle mb-0"><?php echo e($enquiry->is_cancel && ! $enquiry->is_confirm ? 'This inquiry stays in Cancelled Inquiry. Set the status to Quoted to move it back to Enquiries.' : 'Save changes to this enquiry, open Group Bookings, or cancel the inquiry.'); ?></p>
  </div>

  <form method="POST" action="<?php echo e(route('enquiries.update', $enquiry)); ?>" class="enquiry-form" data-enquiry-create="1" novalidate data-existing-pairs='<?php echo json_encode($existingPairs, 15, 512) ?>'>
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <input type="hidden" name="form_context" value="enquiry">
    <?php echo $__env->make('enquiries.partials.entry-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="enquiry-sticky-actions">
      <button type="submit" class="btn btn-accent">
        <i class="bi bi-check-lg"></i> Save enquiry
      </button>
      <button type="button" class="btn btn-outline-secondary" id="show-group-booking">
        <i class="bi bi-calendar-check"></i> Group Bookings
      </button>
      <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelInquiryModal">
        <i class="bi bi-x-circle"></i> Cancel Inquiry
      </button>
    </div>
  </form>

  <?php
    $showGroupBooking = request()->boolean('group')
        || old('cancel_scope') === 'group'
        || old('form_context') === 'group';
  ?>
  <div id="group-booking-section" class="mt-2 <?php echo e($showGroupBooking ? '' : 'd-none'); ?>">
    <div class="page-header mb-3">
      <h2 class="page-title h4 mb-1">Group Bookings</h2>
      <p class="page-subtitle mb-0">Fill in the booking details below. Submitting sets the status to Confirmed.</p>
    </div>
    <?php echo $__env->make('enquiries.partials.group-booking-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
</div>

<div class="modal fade" id="cancelInquiryModal" tabindex="-1" aria-labelledby="cancelInquiryModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="<?php echo e(route('enquiries.cancel', $enquiry)); ?>">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="cancelInquiryModalTitle">Cancel Inquiry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-secondary">Cancel enquiry <strong><?php echo e($enquiry->group_name ?: 'this enquiry'); ?></strong>? It will move to Cancelled Inquiry.</p>
          <label for="inquiry_cxl_date" class="form-label">CXL Date</label>
          <div class="date-placeholder-wrap mb-3">
            <input type="date" name="cxl_date" id="inquiry_cxl_date" class="form-control <?php $__errorArgs = ['cxl_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('cxl_date')); ?>">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <?php $__errorArgs = ['cxl_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block mb-3"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          <label for="cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
          <textarea name="cancellation_reason" id="cancellation_reason" rows="4" class="form-control <?php $__errorArgs = ['cancellation_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required maxlength="2000" placeholder="Enter the cancellation reason"><?php echo e(old('cancellation_reason')); ?></textarea>
          <?php $__errorArgs = ['cancellation_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
          <button type="submit" class="btn btn-danger">Cancel Inquiry</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <?php echo $__env->make('enquiries.partials.entry-form-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('enquiries.partials.group-booking-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php if($errors->has('cancellation_reason') || $errors->has('cxl_date')): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var modalId = <?php echo json_encode(old('cancel_scope') === 'group' ? 'cancelGroupBookingModal' : 'cancelInquiryModal', 15, 512) ?>;
        var modalEl = document.getElementById(modalId);
        if (modalEl && window.bootstrap) {
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      });
    </script>
  <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/enquiries/edit.blade.php ENDPATH**/ ?>