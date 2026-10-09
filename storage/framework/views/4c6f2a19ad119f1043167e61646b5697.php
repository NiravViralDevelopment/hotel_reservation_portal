<?php $__env->startSection('title', 'Sign In'); ?>
<?php $__env->startSection('meta_description', 'Hotel Group Booking Management System — Secure Login'); ?>

<?php $__env->startSection('content'); ?>
  <div class="login-page">
    <div class="login-card">
      <div class="login-brand">
        <div class="login-brand-icon">
          <i class="bi bi-building"></i>
        </div>
        <h1>Hotel Group Booking</h1>
        <p>Management System &mdash; United Kingdom</p>
      </div>

      <form method="POST" action="<?php echo e(route('login')); ?>" id="loginForm" class="login-form needs-validation" novalidate data-laravel-auth>
        <?php echo csrf_field(); ?>

        <div class="form-floating mb-3">
          <input
            type="email"
            class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            id="loginEmail"
            name="email"
            placeholder="name@company.co.uk"
            value="<?php echo e(old('email')); ?>"
            required
            autofocus
            autocomplete="username"
          >
          <label for="loginEmail"><i class="bi bi-envelope me-1"></i> Email Address</label>
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
          <?php else: ?>
            <div class="invalid-feedback">Please enter a valid email address.</div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-floating mb-3 password-field">
          <input
            type="password"
            class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            id="loginPassword"
            name="password"
            placeholder="Password"
            required
            minlength="6"
            autocomplete="current-password"
          >
          <label for="loginPassword"><i class="bi bi-lock me-1"></i> Password</label>
          <button
            type="button"
            class="password-toggle"
            data-password-toggle="loginPassword"
            aria-label="Show password"
            title="Show password"
          >
            <i class="bi bi-eye"></i>
          </button>
          <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
          <?php else: ?>
            <div class="invalid-feedback">Password is required (minimum 6 characters).</div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" value="1" <?php if(old('remember')): echo 'checked'; endif; ?>>
            <label class="form-check-label small text-secondary" for="rememberMe">Remember me</label>
          </div>
        </div>

        <button type="submit" class="btn btn-brand w-100">
          <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
      </form>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\working\h_r_p\resources\views/auth/login.blade.php ENDPATH**/ ?>