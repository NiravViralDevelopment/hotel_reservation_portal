<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page', 'dashboard'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
      <h1 class="page-title">Dashboard</h1>
      <p class="page-subtitle">
        Welcome back, <?php echo e(auth()->user()->name); ?>.
        <?php if(! empty($currentHotel)): ?>
          Working in <strong><?php echo e($currentHotel->name); ?></strong>.
        <?php else: ?>
          Viewing <strong>all hotels</strong>.
        <?php endif; ?>
      </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <?php if(! empty($currentHotel)): ?>
        <form method="POST" action="<?php echo e(route('hotel-context.clear')); ?>" class="m-0">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-building"></i> All hotels
          </button>
        </form>
      <?php elseif(! empty($allocatedHotels) && $allocatedHotels->isNotEmpty()): ?>
        <div class="dropdown">
          <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-building"></i> Filter by hotel
          </button>
          <ul class="dropdown-menu">
            <?php $__currentLoopData = $allocatedHotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li>
                <form method="POST" action="<?php echo e(route('hotel-context.switch')); ?>">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="hotel_id" value="<?php echo e($hotel->id); ?>">
                  <button type="submit" class="dropdown-item"><?php echo e($hotel->name); ?></button>
                </form>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>
      <a href="<?php echo e(route('arrivals.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-in-right"></i> Today&apos;s arrivals</a>
      <a href="<?php echo e(route('group-bookings.create')); ?>" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> New booking</a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Active Hotels</div>
        <div class="stat-card-value"><?php echo e(number_format($stats['hotels'])); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('hotels.index')); ?>">View hotels</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Companies</div>
        <div class="stat-card-value"><?php echo e(number_format($stats['companies'])); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('companies.index')); ?>">View companies</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Travel Agencies</div>
        <div class="stat-card-value"><?php echo e(number_format($stats['travelAgencies'])); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('travel-agencies.index')); ?>">View agencies</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Active Groups</div>
        <div class="stat-card-value"><?php echo e(number_format($stats['activeGroups'])); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('group-bookings.index')); ?>">View bookings</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Arrivals Today</div>
        <div class="stat-card-value"><?php echo e(number_format($stats['arrivalsToday'])); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('arrivals.index')); ?>">Arrivals list</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Departures Today</div>
        <div class="stat-card-value"><?php echo e(number_format($stats['departuresToday'])); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('departures.index')); ?>">Departures list</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Open Enquiries</div>
        <div class="stat-card-value"><?php echo e(number_format($stats['openEnquiries'])); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('enquiries.index')); ?>">View enquiries</a></div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-card-label">Revenue YTD</div>
        <div class="stat-card-value">£<?php echo e(number_format($stats['revenueYtd'], 0)); ?></div>
        <div class="stat-card-meta"><a href="<?php echo e(route('revenue.index')); ?>">Revenue report</a></div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3 px-4">
      <h2 class="h5 fw-semibold mb-0">Upcoming arrivals</h2>
      <a href="<?php echo e(route('calendar.index')); ?>" class="btn btn-sm btn-outline-secondary">Calendar</a>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Group</th>
            <th>Hotel</th>
            <th>Travel agency</th>
            <th>Arrival</th>
            <th class="text-center">Rooms</th>
            <th class="text-center">Pax</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $upcomingArrivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><code><?php echo e($booking->ref ?: '—'); ?></code></td>
              <td class="fw-semibold"><?php echo e($booking->group_name); ?></td>
              <td><?php echo e($booking->hotel?->name ?? '—'); ?></td>
              <td><?php echo e($booking->travelAgency?->name ?? '—'); ?></td>
              <td><?php echo e($booking->check_in?->format('d M Y') ?? '—'); ?></td>
              <td class="text-center"><?php echo e($booking->rooms_per_night ?? '—'); ?></td>
              <td class="text-center"><?php echo e($booking->total_pax ?? '—'); ?></td>
              <td class="text-end">
                <a href="<?php echo e(route('enquiries.show', $booking)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="8" class="text-center text-secondary py-4">No upcoming arrivals.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\working\h_r_p\resources\views/dashboard/index.blade.php ENDPATH**/ ?>