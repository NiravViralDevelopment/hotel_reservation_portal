<?php $__env->startSection('title', 'Add hotel'); ?>
<?php $__env->startSection('page', 'hotels'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .hotel-form-layout .hotel-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .hotel-form-layout .hotel-section-title i { color: var(--brand-accent); }
  .hotel-form-layout .hotel-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.5rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
  .hotel-form .select2-container--bootstrap-5 .select2-selection.is-invalid {
    border-color: var(--bs-form-invalid-border-color, #dc3545);
  }
  .hotel-logo-preview {
    display: none;
    max-height: 72px;
    max-width: 220px;
    object-fit: contain;
    margin-bottom: 0.75rem;
    background: #fff;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    padding: 0.35rem;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="hotel-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('hotels.index')); ?>">Hotels</a></li>
        <li class="breadcrumb-item active">Add hotel</li>
      </ol>
    </nav>
    <h1 class="page-title">Add hotel</h1>
    <p class="page-subtitle mb-0">Create a hotel profile and contact details. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="<?php echo e(route('hotels.store')); ?>" class="hotel-form" enctype="multipart/form-data" novalidate data-existing-pairs='<?php echo json_encode($existingPairs, 15, 512) ?>'>
    <?php echo csrf_field(); ?>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-building"></i> Hotel details</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('code')); ?>" placeholder="Enter code" maxlength="20" autocomplete="off">
            <div class="form-text">Can be reused with a different hotel name.</div>
            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-5">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>" placeholder="Enter hotel name" maxlength="255">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-4">
            <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
            <select name="company_id" id="company_id" class="form-select select2 <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-placeholder="Select company">
              <option value="">Select company</option>
              <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($company->id); ?>" <?php if(old('company_id') == $company->id): echo 'selected'; endif; ?>><?php echo e($company->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select select2 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-placeholder="Select status">
              <option value="active" <?php if(old('status', 'active') === 'active'): echo 'selected'; endif; ?>>Active</option>
              <option value="inactive" <?php if(old('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
            </select>
            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-4">
            <label for="rooms" class="form-label">Rooms <span class="text-danger">*</span></label>
            <input type="text" name="rooms" id="rooms" inputmode="numeric" class="form-control <?php $__errorArgs = ['rooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rooms')); ?>" placeholder="Enter number of rooms" maxlength="5" autocomplete="off">
            <div class="form-text">Numbers only.</div>
            <?php $__errorArgs = ['rooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-4">
            <label for="manager_name" class="form-label">Manager</label>
            <input type="text" name="manager_name" id="manager_name" class="form-control <?php $__errorArgs = ['manager_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('manager_name')); ?>" placeholder="Enter manager name" maxlength="255">
            <?php $__errorArgs = ['manager_name'];
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

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-geo-alt"></i> Location</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
            <input type="text" name="city" id="city" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('city')); ?>" placeholder="Enter city" maxlength="255">
            <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-6">
            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
            <input type="text" name="country" id="country" class="form-control <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="United Kingdom" readonly>
            <div class="form-text">Fixed to United Kingdom.</div>
            <?php $__errorArgs = ['country'];
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

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-telephone"></i> Contact</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone')); ?>" placeholder="Enter phone" maxlength="15" autocomplete="tel">
            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="text" name="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email')); ?>" placeholder="Enter email" maxlength="255" inputmode="email" autocomplete="email">
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

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-image"></i> Logo</h2>
      </div>
      <div class="card-body">
        <img id="logoPreview" class="hotel-logo-preview" alt="Hotel logo preview">
        <label for="logo" class="form-label">Upload logo</label>
        <input
          type="file"
          name="logo"
          id="logo"
          accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"
          class="form-control <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
        >
        <div class="form-text">JPG, PNG, WEBP, or GIF, up to 2 MB.</div>
        <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-file-earmark-arrow-up"></i> Document</h2>
      </div>
      <div class="card-body">
        <label for="document" class="form-label">Upload document</label>
        <input
          type="file"
          name="document"
          id="document"
          accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
          class="form-control <?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
        >
        <div class="form-text">PDF, DOC, or DOCX only, up to 10 MB. One file per hotel.</div>
        <?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-sticky"></i> Notes</h2>
      </div>
      <div class="card-body">
        <label for="notes" class="form-label">Notes</label>
        <textarea name="notes" id="notes" rows="3" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Enter notes"><?php echo e(old('notes')); ?></textarea>
        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div class="hotel-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Save hotel</button>
      <a href="<?php echo e(route('hotels.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/hotel-validation.js')); ?>?v=<?php echo e(@filemtime(public_path('assets/js/hotel-validation.js'))); ?>"></script>
<script>
  (function () {
    var input = document.getElementById('logo');
    var preview = document.getElementById('logoPreview');
    if (!input || !preview) return;
    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) {
        preview.removeAttribute('src');
        preview.style.display = 'none';
        return;
      }
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    });
  })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/hotels/create.blade.php ENDPATH**/ ?>