<?php $__env->startSection('title', 'Add user'); ?>
<?php $__env->startSection('page', 'users'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .user-form-layout .user-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .user-form-layout .user-section-title i { color: var(--brand-accent); }
  .user-form-layout .user-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.5rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="user-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('users.index')); ?>">Users</a></li>
        <li class="breadcrumb-item active">Add user</li>
      </ol>
    </nav>
    <h1 class="page-title">Add user</h1>
    <p class="page-subtitle mb-0">Create a login account, set access, then save. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form
    method="POST"
    action="<?php echo e(route('users.store')); ?>"
    class="user-form"
    novalidate
    data-password-required="1"
    data-existing-emails='<?php echo json_encode($existingEmails, 15, 512) ?>'
  >
    <?php echo csrf_field(); ?>

    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-person-vcard"></i> Profile</h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Full name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>" placeholder="Enter name" maxlength="255" autocomplete="name">
                <?php $__errorArgs = ['name'];
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
                <div class="form-text">Sign-in email. Must be unique.</div>
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
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone')); ?>" placeholder="Enter phone" maxlength="20" autocomplete="tel">
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
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                  <option value="active" <?php if(old('status', 'active') === 'active'): echo 'selected'; endif; ?>>Active</option>
                  <option value="inactive" <?php if(old('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                </select>
                <div class="form-text">Inactive users cannot sign in.</div>
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

        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-shield-lock"></i> Password</h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <div class="password-field">
                  <input type="password" name="password" id="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Enter password" autocomplete="new-password">
                  <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password" title="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="form-text">Minimum 8 characters.</div>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
                <div class="password-field">
                  <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Enter confirm password" autocomplete="new-password">
                  <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password" title="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-person-badge"></i> Roles</h2>
          </div>
          <div class="card-body">
            <p class="small text-secondary mb-3">Select one or more roles.</p>
            <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="form-check mb-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="roles[]"
                  value="<?php echo e($role->name); ?>"
                  id="role_<?php echo e($role->id); ?>"
                  <?php if(in_array($role->name, old('roles', []), true)): echo 'checked'; endif; ?>
                >
                <label class="form-check-label" for="role_<?php echo e($role->id); ?>"><?php echo e($role->name); ?></label>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-secondary mb-0">No roles defined.</p>
            <?php endif; ?>
            <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-2"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-building"></i> Hotel access</h2>
          </div>
          <div class="card-body" style="max-height: 360px; overflow-y: auto;">
            <p class="small text-secondary mb-3">Select hotels this user can access.</p>
            <?php $__empty_1 = true; $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="form-check mb-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="hotels[]"
                  value="<?php echo e($hotel->id); ?>"
                  id="hotel_<?php echo e($hotel->id); ?>"
                  <?php if(in_array($hotel->id, array_map('intval', old('hotels', [])), true)): echo 'checked'; endif; ?>
                >
                <label class="form-check-label" for="hotel_<?php echo e($hotel->id); ?>">
                  <?php echo e($hotel->name); ?> <span class="text-muted">(<?php echo e($hotel->code); ?>)</span>
                </label>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-secondary mb-0">No hotels available.</p>
            <?php endif; ?>
            <?php $__errorArgs = ['hotels'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-2"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="user-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Save user</button>
      <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/user-validation.js')); ?>?v=<?php echo e(@filemtime(public_path('assets/js/user-validation.js'))); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/users/create.blade.php ENDPATH**/ ?>