<!DOCTYPE html>
<html lang="en-GB" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Hotel Group Booking Management System'); ?>">
  <title><?php echo $__env->yieldContent('title', 'HGBMS'); ?> | Hotel Group Booking Management</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?php echo e(asset('assets/css/app.css')); ?>" rel="stylesheet">
  <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body <?php echo $__env->yieldContent('body_attrs'); ?>>
  <?php echo $__env->yieldContent('content'); ?>

  <?php echo $__env->make('partials.toaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/layouts/guest.blade.php ENDPATH**/ ?>