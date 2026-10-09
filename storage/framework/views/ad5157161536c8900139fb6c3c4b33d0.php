<?php
  $v = function (string $key, $fallback = '') use ($enquiry) {
      if (old($key) !== null) {
          return old($key);
      }
      $value = $enquiry->{$key} ?? null;
      if ($value instanceof \DateTimeInterface) {
          return $value->format('Y-m-d');
      }

      return $value === null || $value === '' ? $fallback : $value;
  };
  $contactValue = old('contact_name');
  if ($contactValue === null) {
      $contactValue = $enquiry->contact_name ?: ($enquiry->contact?->name ?? '');
  }
  $selectedBasis = old('basis');
  if ($selectedBasis === null) {
      $selectedBasis = in_array($enquiry->basis, ['BB', 'DBB'], true) ? $enquiry->basis : '';
  }
  $selectedDay = old('day', $enquiry->day);
?>

<form method="POST" action="<?php echo e(route('enquiries.group-booking.store', $enquiry)); ?>" class="enquiry-form" id="group-booking-form">
  <?php echo csrf_field(); ?>
  <input type="hidden" name="form_context" value="group">

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-calendar3"></i> Stay</h2>
      <span class="enquiry-section-hint">Day is filled in from the date of arrival.</span>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-3">
          <label for="gb_check_in" class="form-label">Date of Arrival <span class="text-danger">*</span></label>
          <div class="date-placeholder-wrap">
            <input type="date" name="check_in" id="gb_check_in" class="form-control <?php $__errorArgs = ['check_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('check_in')); ?>" min="<?php echo e(now()->toDateString()); ?>" required>
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <?php $__errorArgs = ['check_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_check_out" class="form-label">Date of Departure <span class="text-danger">*</span></label>
          <div class="date-placeholder-wrap">
            <input type="date" name="check_out" id="gb_check_out" class="form-control <?php $__errorArgs = ['check_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('check_out')); ?>" required>
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <?php $__errorArgs = ['check_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_day" class="form-label">Day <span class="field-auto-badge">Auto</span></label>
          <select name="day" id="gb_day" class="form-select day-auto <?php $__errorArgs = ['day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" tabindex="-1" aria-readonly="true">
            <option value="">From arrival date</option>
            <?php $__currentLoopData = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($weekday); ?>" <?php if($selectedDay === $weekday): echo 'selected'; endif; ?>><?php echo e($weekday); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_nights" class="form-label">No. of Nights <span class="text-danger">*</span></label>
          <input type="text" name="nights" id="gb_nights" inputmode="numeric" class="form-control js-digits gb-calc <?php $__errorArgs = ['nights'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('nights')); ?>" required>
          <?php $__errorArgs = ['nights'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_block_id" class="form-label">Block ID <span class="text-danger">*</span></label>
          <input type="text" name="block_id" id="gb_block_id" class="form-control <?php $__errorArgs = ['block_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('block_id')); ?>" maxlength="255" required>
          <?php $__errorArgs = ['block_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-person-vcard"></i> Client</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="gb_client" class="form-label">Client</label>
          <input type="text" name="client" id="gb_client" class="form-control <?php $__errorArgs = ['client'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('client')); ?>" maxlength="255">
          <?php $__errorArgs = ['client'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6">
          <label for="gb_agency_ref" class="form-label">Agency - Ref</label>
          <input type="text" name="agency_ref" id="gb_agency_ref" class="form-control <?php $__errorArgs = ['agency_ref'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('agency_ref')); ?>" maxlength="255">
          <?php $__errorArgs = ['agency_ref'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6">
          <label for="gb_contact_name" class="form-label">Contact</label>
          <input type="text" name="contact_name" id="gb_contact_name" class="form-control <?php $__errorArgs = ['contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($contactValue); ?>" maxlength="255">
          <?php $__errorArgs = ['contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6">
          <label for="gb_email" class="form-label">Email ID</label>
          <input type="text" name="email" id="gb_email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('email')); ?>" maxlength="255" inputmode="email">
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-file-earmark-text"></i> Contract and payment</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-4">
          <label for="gb_contract_sent_on" class="form-label">Contract Sent On</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="contract_sent_on" id="gb_contract_sent_on" class="form-control <?php $__errorArgs = ['contract_sent_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('contract_sent_on')); ?>">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <?php $__errorArgs = ['contract_sent_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_contract_received_on" class="form-label">Contract Recd On</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="contract_received_on" id="gb_contract_received_on" class="form-control <?php $__errorArgs = ['contract_received_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('contract_received_on')); ?>">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <?php $__errorArgs = ['contract_received_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_saved_to_doc" class="form-label">Saved to Doc</label>
          <input type="text" name="saved_to_doc" id="gb_saved_to_doc" class="form-control <?php $__errorArgs = ['saved_to_doc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('saved_to_doc')); ?>" maxlength="255">
          <?php $__errorArgs = ['saved_to_doc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php
          $termSubmitted = old('form_context') === 'group';
          $selectedPaymentTerm = $termSubmitted ? old('payment_term') : ($enquiry->payment_term ?? null);
          $paymentTermDays = $termSubmitted ? old('payment_term_days') : ($enquiry->payment_term_days ?? '');
        ?>
        <div class="col-md-6 col-xl-4">
          <span class="form-label d-block">Payment Term</span>
          <div class="form-check">
            <input class="form-check-input js-payment-term" type="checkbox" name="payment_term" id="gb_payment_term_pre" value="Pre Arrival" <?php if($selectedPaymentTerm === 'Pre Arrival'): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="gb_payment_term_pre">Pre Arrival</label>
          </div>
          <div class="form-check mb-0">
            <input class="form-check-input js-payment-term" type="checkbox" name="payment_term" id="gb_payment_term_post" value="Post Departure" <?php if($selectedPaymentTerm === 'Post Departure'): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="gb_payment_term_post">Post Departure</label>
          </div>
          <?php $__errorArgs = ['payment_term'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          <div id="gb_payment_term_days_wrap" class="mt-2" <?php if (! (in_array($selectedPaymentTerm, ['Pre Arrival', 'Post Departure'], true))): ?> hidden <?php endif; ?>>
            <label for="gb_payment_term_days" class="form-label">Number of days</label>
            <input type="text" name="payment_term_days" id="gb_payment_term_days" inputmode="numeric" class="form-control js-digits <?php $__errorArgs = ['payment_term_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($paymentTermDays); ?>" placeholder="Enter days" maxlength="3">
            <?php $__errorArgs = ['payment_term_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_payment_due_date" class="form-label">Due Date <span class="field-auto-badge">Auto</span></label>
          <div class="date-placeholder-wrap">
            <input type="date" name="payment_due_date" id="gb_payment_due_date" class="form-control cxl-due-auto <?php $__errorArgs = ['payment_due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('payment_due_date')); ?>" readonly tabindex="-1" aria-readonly="true">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <div class="form-text" id="gb_payment_due_hint">Select a payment term and enter the number of days.</div>
          <?php $__errorArgs = ['payment_due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_payment_status" class="form-label">Payment Status</label>
          <input type="text" name="payment_status" id="gb_payment_status" class="form-control <?php $__errorArgs = ['payment_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('payment_status')); ?>" maxlength="255">
          <?php $__errorArgs = ['payment_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-shield-exclamation"></i> Cancellation and commission</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-3">
          <label for="gb_cxl_policy" class="form-label">CXL Policy</label>
          <input type="text" name="cxl_policy" id="gb_cxl_policy" class="form-control <?php $__errorArgs = ['cxl_policy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('cxl_policy')); ?>" maxlength="255">
          <?php $__errorArgs = ['cxl_policy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_cxl_due_date" class="form-label">CXL Due Date</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="cxl_due_date" id="gb_cxl_due_date" class="form-control <?php $__errorArgs = ['cxl_due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('cxl_due_date')); ?>">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <?php $__errorArgs = ['cxl_due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php
          $commissionSubmitted = old('form_context') === 'group';
          $selectedHasCommission = $commissionSubmitted
              ? old('has_commission')
              : ($enquiry->has_commission === null ? null : ($enquiry->has_commission ? '1' : '0'));
          $selectedCommissionPayable = $commissionSubmitted
              ? old('commission_payable_status')
              : ($enquiry->commission_payable_status ?? '');
        ?>
        <div class="col-md-6 col-xl-3">
          <span class="form-label d-block">Commission</span>
          <div class="form-check">
            <input class="form-check-input js-has-commission" type="checkbox" name="has_commission" id="gb_has_commission_yes" value="1" <?php if($selectedHasCommission === '1' || $selectedHasCommission === 1 || $selectedHasCommission === true): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="gb_has_commission_yes">Yes</label>
          </div>
          <div class="form-check mb-0">
            <input class="form-check-input js-has-commission" type="checkbox" name="has_commission" id="gb_has_commission_no" value="0" <?php if($selectedHasCommission === '0' || $selectedHasCommission === 0 || $selectedHasCommission === false): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="gb_has_commission_no">No</label>
          </div>
          <?php $__errorArgs = ['has_commission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
      <span class="enquiry-section-hint">Room counts, rates, and nights update Total RNs, Total Rev, BB Revenue, and Nett Rev.</span>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <?php $__currentLoopData = [
          'single' => ['label' => 'Single', 'icon' => 'bi-person', 'rooms' => 'Single RNs', 'rate' => 'Single Gross Rate'],
          'double' => ['label' => 'Double', 'icon' => 'bi-people', 'rooms' => 'Double RNs', 'rate' => 'Double Gross Rate'],
          'triple' => ['label' => 'Triple', 'icon' => 'bi-people-fill', 'rooms' => 'Triple RNs', 'rate' => 'Triple Gross Rate'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="col-md-4">
            <div class="room-rate-card">
              <h3><i class="bi <?php echo e($meta['icon']); ?>"></i> <?php echo e($meta['label']); ?></h3>
              <div class="mb-3">
                <label for="gb_<?php echo e($type); ?>_rooms" class="form-label"><?php echo e($meta['rooms']); ?></label>
                <input type="text" name="<?php echo e($type); ?>_rooms" id="gb_<?php echo e($type); ?>_rooms" inputmode="numeric" class="form-control js-digits gb-calc <?php $__errorArgs = [$type.'_rooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v($type.'_rooms')); ?>">
                <?php $__errorArgs = [$type.'_rooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              <div>
                <label for="gb_<?php echo e($type); ?>_rate" class="form-label"><?php echo e($meta['rate']); ?></label>
                <input type="text" name="<?php echo e($type); ?>_rate" id="gb_<?php echo e($type); ?>_rate" inputmode="decimal" class="form-control js-decimal gb-calc <?php $__errorArgs = [$type.'_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v($type.'_rate')); ?>">
                <?php $__errorArgs = [$type.'_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-currency-pound"></i> Revenue</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_total_rns_display" class="form-label">Total RNs</label>
            <input type="text" id="gb_total_rns_display" class="form-control" value="" readonly>
            <input type="hidden" name="total_rns" id="gb_total_rns" value="<?php echo e($v('total_rns')); ?>">
            <div class="form-text">(Single + Double + Triple) × nights</div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_total_revenue_display" class="form-label">Total Rev</label>
            <input type="text" id="gb_total_revenue_display" class="form-control" value="" placeholder="£0.00" readonly>
            <input type="hidden" name="total_revenue" id="gb_total_revenue" value="<?php echo e($v('total_revenue')); ?>">
            <div class="form-text">Room totals × nights</div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_bb_revenue_display" class="form-label">BB Revenue (Nett £)</label>
            <input type="text" id="gb_bb_revenue_display" class="form-control <?php $__errorArgs = ['bb_revenue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="" readonly>
            <input type="hidden" name="bb_revenue" id="gb_bb_revenue" value="<?php echo e($v('bb_revenue')); ?>">
            <div class="form-text">(Single × 10 + Double × 20 + Triple × 30) × nights</div>
            <?php $__errorArgs = ['bb_revenue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_dinner_revenue_display" class="form-label">Dinner Revenue (Nett £)</label>
            <input type="text" id="gb_dinner_revenue_display" class="form-control <?php $__errorArgs = ['dinner_revenue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="" readonly>
            <input type="hidden" name="dinner_revenue" id="gb_dinner_revenue" value="<?php echo e($v('dinner_revenue')); ?>">
            <div class="form-text">£0</div>
            <?php $__errorArgs = ['dinner_revenue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_nett_rev_ex_vat_display" class="form-label">Nett Rev EX VAT &amp; BF</label>
            <input type="text" id="gb_nett_rev_ex_vat_display" class="form-control <?php $__errorArgs = ['nett_rev_ex_vat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="" readonly>
            <input type="hidden" name="nett_rev_ex_vat" id="gb_nett_rev_ex_vat" value="<?php echo e($v('nett_rev_ex_vat')); ?>">
            <div class="form-text">(Total Rev × 100 / 120) − BB Revenue</div>
            <?php $__errorArgs = ['nett_rev_ex_vat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_basis" class="form-label">BB/DBB</label>
          <select name="basis" id="gb_basis" class="form-select <?php $__errorArgs = ['basis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Select BB/DBB</option>
            <?php $__currentLoopData = ['BB', 'DBB']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $basis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($basis); ?>" <?php if($selectedBasis === $basis): echo 'selected'; endif; ?>><?php echo e($basis); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['basis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-receipt"></i> Rooming and invoice</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-4">
          <label for="gb_booking_update" class="form-label">Update</label>
          <input type="text" name="booking_update" id="gb_booking_update" class="form-control <?php $__errorArgs = ['booking_update'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('booking_update')); ?>" maxlength="255">
          <?php $__errorArgs = ['booking_update'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_rooming" class="form-label">Rooming</label>
          <input type="text" name="rooming" id="gb_rooming" class="form-control <?php $__errorArgs = ['rooming'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('rooming')); ?>" maxlength="255">
          <?php $__errorArgs = ['rooming'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_invoice_status" class="form-label">Invoice Status</label>
          <input type="text" name="invoice_status" id="gb_invoice_status" class="form-control <?php $__errorArgs = ['invoice_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('invoice_status')); ?>" maxlength="255">
          <?php $__errorArgs = ['invoice_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_invoice_sent_on" class="form-label">Invoice Sent On</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="invoice_sent_on" id="gb_invoice_sent_on" class="form-control <?php $__errorArgs = ['invoice_sent_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('invoice_sent_on')); ?>">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <?php $__errorArgs = ['invoice_sent_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_invoice_amount" class="form-label">Invoice Amount</label>
          <input type="text" name="invoice_amount" id="gb_invoice_amount" inputmode="decimal" class="form-control js-decimal <?php $__errorArgs = ['invoice_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('invoice_amount')); ?>">
          <?php $__errorArgs = ['invoice_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6 col-xl-4" id="gb_commission_payable_wrap" <?php if (! ($selectedHasCommission === '1' || $selectedHasCommission === 1 || $selectedHasCommission === true)): ?> hidden <?php endif; ?>>
          <label for="gb_commission_payable_status" class="form-label">Commission Payable Status</label>
          <select name="commission_payable_status" id="gb_commission_payable_status" class="form-select <?php $__errorArgs = ['commission_payable_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Select status</option>
            <option value="Pending" <?php if($selectedCommissionPayable === 'Pending'): echo 'selected'; endif; ?>>Pending</option>
            <option value="Received" <?php if($selectedCommissionPayable === 'Received'): echo 'selected'; endif; ?>>Received</option>
          </select>
          <?php $__errorArgs = ['commission_payable_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="enquiry-sticky-actions">
    <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Submit group booking</button>
    <?php if($enquiry->is_confirm && ! $enquiry->is_cancel): ?>
      <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelGroupBookingModal">
        <i class="bi bi-x-circle"></i> Cancel group booking
      </button>
    <?php endif; ?>
    <?php if(isset($groupBookingBackUrl)): ?>
      <a href="<?php echo e($groupBookingBackUrl); ?>" class="btn btn-outline-secondary">Back</a>
    <?php endif; ?>
  </div>
</form>

<?php if($enquiry->is_confirm && ! $enquiry->is_cancel): ?>
  <div class="modal fade" id="cancelGroupBookingModal" tabindex="-1" aria-labelledby="cancelGroupBookingModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="<?php echo e(route('enquiries.cancel-booking', $enquiry)); ?>">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="cancel_scope" value="group">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelGroupBookingModalTitle">Cancel group booking</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-secondary">Cancel this group booking? The status will be set to Cancelled and it will move to Cancelled Bookings.</p>
            <label for="booking_cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
            <textarea name="cancellation_reason" id="booking_cancellation_reason" rows="4" class="form-control <?php $__errorArgs = ['cancellation_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required maxlength="2000" placeholder="Enter the cancellation reason"><?php echo e(old('cancel_scope') === 'group' ? old('cancellation_reason') : ''); ?></textarea>
            <?php if(old('cancel_scope') === 'group'): ?>
              <?php $__errorArgs = ['cancellation_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php endif; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
            <button type="submit" class="btn btn-danger">Cancel group booking</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php /**PATH D:\working\h_r_p\resources\views/enquiries/partials/group-booking-form.blade.php ENDPATH**/ ?>