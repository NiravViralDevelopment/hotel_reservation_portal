<?php $__env->startSection('title', $hotel->name); ?>
<?php $__env->startSection('page', 'hotels'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .hotel-show-layout .hotel-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .hotel-show-layout .hotel-section-title i { color: var(--brand-accent); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="hotel-show-layout">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo e(route('hotels.index')); ?>">Hotels</a></li>
          <li class="breadcrumb-item active"><?php echo e($hotel->code); ?></li>
        </ol>
      </nav>
      <h1 class="page-title"><?php echo e($hotel->name); ?></h1>
      <p class="page-subtitle mb-0">
        <span class="me-2"><?php echo e($hotel->code); ?></span>
        <?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge-status','data' => ['status' => $hotel->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hotel->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal435aefee4aa6dd7f20df034696ae03b9)): ?>
<?php $attributes = $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9; ?>
<?php unset($__attributesOriginal435aefee4aa6dd7f20df034696ae03b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal435aefee4aa6dd7f20df034696ae03b9)): ?>
<?php $component = $__componentOriginal435aefee4aa6dd7f20df034696ae03b9; ?>
<?php unset($__componentOriginal435aefee4aa6dd7f20df034696ae03b9); ?>
<?php endif; ?>
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="<?php echo e(route('hotels.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $hotel)): ?>
        <a href="<?php echo e(route('hotels.edit', $hotel)); ?>" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-building"></i> Hotel details</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Code</div>
          <div class="info-card-value"><?php echo e($hotel->code); ?></div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Company</div>
          <div class="info-card-value">
            <?php if($hotel->company): ?>
              <a href="<?php echo e(route('companies.show', $hotel->company)); ?>"><?php echo e($hotel->company->name); ?></a>
            <?php else: ?>
              —
            <?php endif; ?>
          </div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Rooms</div>
          <div class="info-card-value"><?php echo e($hotel->rooms ?? '—'); ?></div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Status</div>
          <div class="info-card-value"><?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge-status','data' => ['status' => $hotel->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hotel->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal435aefee4aa6dd7f20df034696ae03b9)): ?>
<?php $attributes = $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9; ?>
<?php unset($__attributesOriginal435aefee4aa6dd7f20df034696ae03b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal435aefee4aa6dd7f20df034696ae03b9)): ?>
<?php $component = $__componentOriginal435aefee4aa6dd7f20df034696ae03b9; ?>
<?php unset($__componentOriginal435aefee4aa6dd7f20df034696ae03b9); ?>
<?php endif; ?></div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Document</div>
          <div class="info-card-value">
            <?php if($hotel->hasDocument()): ?>
              <a href="<?php echo e(route('hotels.document.download', $hotel)); ?>">
                <i class="bi bi-download"></i> <?php echo e($hotel->document_original_name); ?>

              </a>
            <?php else: ?>
              —
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-geo-alt"></i> Location</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">City</div>
          <div class="info-card-value"><?php echo e($hotel->city ?: '—'); ?></div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Country</div>
          <div class="info-card-value"><?php echo e($hotel->country ?: '—'); ?></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-telephone"></i> Contact</h2>
      </div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Manager</div>
          <div class="info-card-value"><?php echo e($hotel->manager_name ?: '—'); ?></div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Phone</div>
          <div class="info-card-value"><?php echo e($hotel->phone ?: '—'); ?></div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Email</div>
          <div class="info-card-value"><?php echo e($hotel->email ?: '—'); ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="hotel-section-title"><i class="bi bi-sticky"></i> Notes</h2>
        </div>
        <div class="card-body">
          <p class="mb-0 text-secondary"><?php echo e($hotel->notes ?: 'No notes.'); ?></p>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="hotel-section-title"><i class="bi bi-people"></i> Assigned users</h2>
        </div>
        <div class="card-body">
          <?php $__empty_1 = true; $__currentLoopData = $hotel->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <span class="badge bg-light text-dark border me-1 mb-1"><?php echo e($user->name); ?></span>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="mb-0 text-secondary">No users assigned to this hotel.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h2 class="hotel-section-title"><i class="bi bi-calendar2-week"></i> Confirmed bookings</h2>
      <span class="badge bg-light text-dark border"><?php echo e($hotel->confirmedBookings->count()); ?></span>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Group</th>
            <th>Arrival</th>
            <th class="text-end">Grand total</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $hotel->confirmedBookings->take(15); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="fw-semibold text-nowrap">
                <a href="<?php echo e(route('enquiries.show', $booking)); ?>"><?php echo e($booking->ref ?: '—'); ?></a>
              </td>
              <td><?php echo e($booking->group_name); ?></td>
              <td><?php echo e($booking->check_in?->format('d M Y') ?? '—'); ?></td>
              <td class="text-end">£<?php echo e(number_format((float) ($booking->grand_total ?? 0), 2)); ?></td>
              <td><?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge-status','data' => ['status' => $booking->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal435aefee4aa6dd7f20df034696ae03b9)): ?>
<?php $attributes = $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9; ?>
<?php unset($__attributesOriginal435aefee4aa6dd7f20df034696ae03b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal435aefee4aa6dd7f20df034696ae03b9)): ?>
<?php $component = $__componentOriginal435aefee4aa6dd7f20df034696ae03b9; ?>
<?php unset($__componentOriginal435aefee4aa6dd7f20df034696ae03b9); ?>
<?php endif; ?></td>
              <td class="text-end">
                <a href="<?php echo e(route('enquiries.show', $booking)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center text-secondary py-4">No confirmed bookings for this hotel yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/hotels/show.blade.php ENDPATH**/ ?>