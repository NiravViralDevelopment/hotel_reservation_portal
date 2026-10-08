<?php
  $e = $enquiry ?? null;
  $v = function (string $key, $fallback = '') use ($e) {
      if (old($key) !== null) {
          return old($key);
      }
      if ($e === null) {
          return $fallback;
      }
      $value = $e->{$key} ?? null;
      if ($value instanceof \DateTimeInterface) {
          return $value->format('Y-m-d');
      }

      return $value ?? $fallback;
  };
  $selectedStatus = old('status', $e?->status);
  $selectedBasis = old('basis', $e?->basis);
  $selectedDay = old('day', $e?->day);
?>

<div class="card mb-3">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-calendar3"></i> Dates</h2>
    <span class="enquiry-section-hint">Day comes from the arrival date. Nights are calculated from arrival and departure.</span>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6 col-xl-3">
        <label for="enquiry_date" class="form-label">Enquiry Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="enquiry_date" id="enquiry_date" class="form-control <?php $__errorArgs = ['enquiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('enquiry_date', $e ? '' : date('Y-m-d'))); ?>" required>
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        <?php $__errorArgs = ['enquiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="response_date" class="form-label">Response Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="response_date" id="response_date" class="form-control <?php $__errorArgs = ['response_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('response_date')); ?>" required>
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        <?php $__errorArgs = ['response_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="check_in" class="form-label">Arrival Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="check_in" id="check_in" class="form-control <?php $__errorArgs = ['check_in'];
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
        <label for="check_out" class="form-label">Departure Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="check_out" id="check_out" class="form-control <?php $__errorArgs = ['check_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('check_out')); ?>" <?php if($v('check_in')): ?> min="<?php echo e(\Illuminate\Support\Carbon::parse($v('check_in'))->addDay()->toDateString()); ?>" <?php endif; ?> required>
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
        <label for="day" class="form-label">Day <span class="field-auto-badge">Auto</span></label>
        <select name="day" id="day" class="form-select day-auto <?php $__errorArgs = ['day'];
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
        <label for="nights" class="form-label">Nights <span class="field-auto-badge">Auto</span></label>
        <input type="text" name="nights" id="nights" inputmode="numeric" class="form-control nights-auto enquiry-calc <?php $__errorArgs = ['nights'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('nights')); ?>" placeholder="From dates" readonly tabindex="-1" aria-readonly="true" required>
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
        <label for="rooms_per_night" class="form-label">Total Room per Night <span class="text-danger">*</span></label>
        <input type="text" name="rooms_per_night" id="rooms_per_night" inputmode="numeric" class="form-control js-digits <?php $__errorArgs = ['rooms_per_night'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('rooms_per_night')); ?>" placeholder="Enter total rooms" required>
        <?php $__errorArgs = ['rooms_per_night'];
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
    <h2 class="enquiry-section-title"><i class="bi bi-people"></i> Group</h2>
    <span class="enquiry-section-hint">Who the enquiry is for, and how to reach them.</span>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label for="group_name" class="form-label">Group Name <span class="text-danger">*</span></label>
        <input type="text" name="group_name" id="group_name" class="form-control <?php $__errorArgs = ['group_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('group_name')); ?>" placeholder="Enter group name" maxlength="255" required>
        <?php $__errorArgs = ['group_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="col-md-6">
        <label for="ref" class="form-label">Ref No <span class="text-danger">*</span></label>
        <input type="text" name="ref" id="ref" class="form-control <?php $__errorArgs = ['ref'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('ref')); ?>" placeholder="Enter ref no" maxlength="255" required>
        <?php $__errorArgs = ['ref'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="col-md-6">
        <label for="email" class="form-label">Email ID <span class="text-danger">*</span></label>
        <input type="text" name="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('email')); ?>" placeholder="Enter email" maxlength="255" inputmode="email" required>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select select2 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-placeholder="Select status">
          <option value="">Select status</option>
          <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php if($selectedStatus === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <div class="form-text"><?php echo e($e ? 'Leave blank to keep the current status.' : 'Blank uses the default from Status Master.'); ?></div>
        <?php $__errorArgs = ['status'];
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
    <span class="enquiry-section-hint">Room dates run from arrival through the night before departure (checkout day is not charged).</span>
  </div>
  <div class="card-body">
    <?php
      $dailySeed = old('daily_rooms');
      if (! is_array($dailySeed)) {
          $dailySeed = [];
          foreach (($e?->daily_room_rates ?? []) as $row) {
              if (is_array($row) && ! empty($row['date'])) {
                  $dailySeed[$row['date']] = $row;
              }
          }
      }
    ?>
    <script type="application/json" id="daily-room-seed"><?php echo json_encode($dailySeed, 15, 512) ?></script>
    <div class="stay-date-breakdown" id="stay-date-breakdown">
      <div id="stay-date-list"></div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-currency-pound"></i> Revenue and terms</h2>
    <span class="enquiry-section-hint">Total revenue updates as you enter rooms, rates, and nights.</span>
  </div>
  <div class="card-body">
    <div class="row g-3 align-items-start">
      <div class="col-md-6 col-xl-3">
        <label for="basis" class="form-label">Basis</label>
        <select name="basis" id="basis" class="form-select select2 <?php $__errorArgs = ['basis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-placeholder="Select basis">
          <option value="">Select basis</option>
          <?php $__currentLoopData = ['BB', 'DBB', 'HB', 'FB', 'RO']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $basis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
      <div class="col-md-6 col-xl-3">
        <label for="option_date" class="form-label">Option Date</label>
        <div class="date-placeholder-wrap">
          <input type="date" name="option_date" id="option_date" class="form-control <?php $__errorArgs = ['option_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('option_date')); ?>">
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        <?php $__errorArgs = ['option_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="col-xl-6">
        <label for="cxl_policy" class="form-label">CXL Policy</label>
        <input type="text" name="cxl_policy" id="cxl_policy" class="form-control <?php $__errorArgs = ['cxl_policy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('cxl_policy')); ?>" placeholder="e.g. 30 days" maxlength="255">
        <div class="form-text">Enter the number of days before arrival.</div>
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
        <label for="cxl_due_date" class="form-label">CXL Due Date <span class="field-auto-badge">Auto</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="cxl_due_date" id="cxl_due_date" class="form-control cxl-due-auto <?php $__errorArgs = ['cxl_due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($v('cxl_due_date')); ?>" readonly tabindex="-1" aria-readonly="true">
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        <div class="form-text">Arrival date minus the CXL Policy days.</div>
        <?php $__errorArgs = ['cxl_due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="revenue-panel">
          <label for="total_revenue_display" class="form-label">Total Revenue</label>
          <input type="text" id="total_revenue_display" class="form-control" value="" placeholder="£0.00" readonly>
          <input type="hidden" name="total_revenue" id="total_revenue" value="<?php echo e($v('total_revenue')); ?>">
          <div class="form-text">((Single × rate) + (Double × rate) + (Triple × rate)) × nights</div>
          <?php $__errorArgs = ['total_revenue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
      <div class="col-md-6 col-xl-9">
        <label for="remarks" class="form-label">Remarks</label>
        <textarea name="remarks" id="remarks" rows="3" class="form-control <?php $__errorArgs = ['remarks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Enter remarks"><?php echo e($v('remarks')); ?></textarea>
        <?php $__errorArgs = ['remarks'];
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
<?php /**PATH E:\Working\hotel_reservation_portal\resources\views/enquiries/partials/entry-form.blade.php ENDPATH**/ ?>