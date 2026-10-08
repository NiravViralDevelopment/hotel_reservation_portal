<?php $__env->startSection('title', 'Select hotel'); ?>
<?php $__env->startSection('page', 'dashboard'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item active">Select hotel</li>
      </ol>
    </nav>
    <h1 class="page-title">Select a hotel</h1>
    <p class="page-subtitle">Welcome, <?php echo e(auth()->user()->name); ?>. Choose one of your allocated hotels to continue.</p>
  </div>

  <?php if($allocatedHotels->isEmpty()): ?>
    <div class="alert alert-warning">
      No hotels are allocated to your account. Please contact an administrator.
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php $__currentLoopData = $allocatedHotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6 col-xl-4">
          <div class="card h-100 hotel-select-card">
            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-start gap-3 mb-3">
                <div class="stat-card-icon primary"><i class="bi bi-building"></i></div>
                <div>
                  <h2 class="h5 mb-1"><?php echo e($hotel->name); ?></h2>
                  <div class="text-secondary small">
                    <?php echo e($hotel->code); ?>

                    <?php if($hotel->city || $hotel->country): ?>
                      · <?php echo e(collect([$hotel->city, $hotel->country])->filter()->implode(', ')); ?>

                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="mt-auto">
                <form method="POST" action="<?php echo e(route('hotel-context.switch')); ?>">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="hotel_id" value="<?php echo e($hotel->id); ?>">
                  <button type="submit" class="btn btn-accent w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Enter hotel
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/dashboard/select-hotel.blade.php ENDPATH**/ ?>