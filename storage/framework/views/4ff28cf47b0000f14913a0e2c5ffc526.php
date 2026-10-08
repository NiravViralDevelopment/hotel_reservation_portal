<?php
  $rows = $rows ?? collect();
  $defaultSort = $defaultSort ?? 'check_in';
  $defaultDir = $defaultDir ?? 'asc';
  $emptyMessage = $emptyMessage ?? 'No confirmed bookings found.';
  $showCancellationReason = $showCancellationReason ?? false;
  $highlightDates = $highlightDates ?? false;
  $recordRoute = $showCancellationReason ? 'cancelled-bookings.show' : 'group-bookings.show';
  $text = function ($value) {
      return filled($value) ? $value : '—';
  };
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
  $date = function ($value) {
      return $value?->format('d M Y') ?? '—';
  };
?>

<div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
<div class="table-wrapper table-scroll-wide table-scroll-enquiries">
  <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
    <thead>
      <tr>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'check_in','label' => 'Date of Arrival','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'check_in','label' => 'Date of Arrival','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'check_out','label' => 'Date of Departure','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'check_out','label' => 'Date of Departure','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'day','label' => 'Day','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'day','label' => 'Day','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'nights','label' => 'No. of Nights','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'nights','label' => 'No. of Nights','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'block_id','label' => 'Block ID','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'block_id','label' => 'Block ID','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'client','label' => 'Client','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'client','label' => 'Client','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <th>Agency - Ref</th>
        <th>Contact</th>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'email','label' => 'Email ID','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'email','label' => 'Email ID','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'status','label' => 'Status','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'status','label' => 'Status','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <th>Contract Sent On</th>
        <th>Contract Recd On</th>
        <th>Saved to Doc</th>
        <th>Payment Term</th>
        <th>Due Date</th>
        <th>Payment Status</th>
        <th>CXL Policy</th>
        <th>CXL Due Date</th>
        <?php if($showCancellationReason): ?>
          <th>CXL Date</th>
        <?php endif; ?>
        <th>Commission</th>
        <th>Single RNs</th>
        <th>Single Gross Rate</th>
        <th>Double RNs</th>
        <th>Double Gross Rate</th>
        <th>Triple RNs</th>
        <th>Triple Gross Rate</th>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'total_rns','label' => 'Total RNs','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'total_rns','label' => 'Total RNs','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'total_revenue','label' => 'Total Rev','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'total_revenue','label' => 'Total Rev','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $attributes = $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07)): ?>
<?php $component = $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07; ?>
<?php unset($__componentOriginal3c1df23c66879bbdd25946c6c08cdc07); ?>
<?php endif; ?>
        <th>BB Revenue (Nett £10)</th>
        <th>Dinner Revenue (Nett £)</th>
        <th>Nett Rev EX VAT &amp; BF</th>
        <th>BB/DBB</th>
        <th>Update</th>
        <th>Rooming</th>
        <th>Invoice Status</th>
        <th>Invoice Number</th>
        <th>Invoice Sent On</th>
        <th>Invoice Amount</th>
        <th>Commission Payable Status</th>
        <?php if($showCancellationReason): ?>
          <th>Cancellation Reason</th>
        <?php endif; ?>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="text-nowrap"><?php if($highlightDates): ?><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-alert','data' => ['date' => $enquiry->check_in]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->check_in)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $attributes = $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $component = $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?><?php else: ?><?php echo e($date($enquiry->check_in)); ?><?php endif; ?></td>
          <td class="text-nowrap"><?php echo e($date($enquiry->check_out)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->day)); ?></td>
          <td><?php echo e($enquiry->nights ?? '—'); ?></td>
          <td class="fw-semibold text-nowrap"><a href="<?php echo e(route($recordRoute, $enquiry)); ?>"><?php echo e($text($enquiry->block_id)); ?></a></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->client)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->agency_ref)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->contact_name)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->email)); ?></td>
          <td><?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge-status','data' => ['status' => $enquiry->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->status)]); ?>
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
          <td class="text-nowrap"><?php echo e($date($enquiry->contract_sent_on)); ?></td>
          <td class="text-nowrap"><?php echo e($date($enquiry->contract_received_on)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->saved_to_doc)); ?></td>
          <td class="text-nowrap"><?php if($enquiry->payment_term): ?><?php echo e($enquiry->payment_term); ?><?php if($enquiry->payment_term_days !== null): ?> (<?php echo e($enquiry->payment_term_days); ?> <?php echo e((int) $enquiry->payment_term_days === 1 ? 'day' : 'days'); ?>)<?php endif; ?>@else—<?php endif; ?></td>
          <td class="text-nowrap"><?php if($highlightDates): ?><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-alert','data' => ['date' => $enquiry->payment_due_date]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->payment_due_date)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $attributes = $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $component = $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?><?php else: ?><?php echo e($date($enquiry->payment_due_date)); ?><?php endif; ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->payment_status)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->cxl_policy)); ?></td>
          <td class="text-nowrap"><?php if($highlightDates): ?><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-alert','data' => ['date' => $enquiry->cxl_due_date]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->cxl_due_date)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $attributes = $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $component = $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?><?php else: ?><?php echo e($date($enquiry->cxl_due_date)); ?><?php endif; ?></td>
          <?php if($showCancellationReason): ?>
            <td class="text-nowrap"><?php echo e($date($enquiry->cxl_date)); ?></td>
          <?php endif; ?>
          <td class="text-nowrap"><?php echo e($enquiry->has_commission === null ? '—' : ($enquiry->has_commission ? 'Yes' : 'No')); ?></td>
          <td><?php echo e($enquiry->single_rooms ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->single_rate)); ?></td>
          <td><?php echo e($enquiry->double_rooms ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->double_rate)); ?></td>
          <td><?php echo e($enquiry->triple_rooms ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->triple_rate)); ?></td>
          <td><?php echo e($enquiry->total_rns ?? '—'); ?></td>
          <td class="text-nowrap fw-semibold"><?php echo e($money($enquiry->total_revenue)); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->bb_revenue)); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->dinner_revenue)); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->nett_rev_ex_vat)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->basis)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->booking_update)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->rooming)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->invoice_status)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->invoice_number)); ?></td>
          <td class="text-nowrap"><?php echo e($date($enquiry->invoice_sent_on)); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->invoice_amount)); ?></td>
          <td class="text-nowrap"><?php echo e($text($enquiry->commission_payable_status)); ?></td>
          <?php if($showCancellationReason): ?>
            <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($enquiry->cancellation_reason); ?>"><?php echo e($text($enquiry->cancellation_reason)); ?></td>
          <?php endif; ?>
          <td class="text-end text-nowrap">
            <a href="<?php echo e(route($recordRoute, $enquiry)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
              <i class="bi bi-eye"></i>
            </a>
            <?php if (! ($showCancellationReason)): ?>
              <a href="<?php echo e(route('group-bookings.contract', $enquiry)); ?>" class="btn btn-sm btn-outline-primary" title="Hotel contract">
                <i class="bi bi-file-earmark-pdf"></i>
              </a>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
                <a href="<?php echo e(route('group-bookings.edit', $enquiry)); ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="<?php echo e($showCancellationReason ? 40 : 38); ?>" class="text-center text-secondary py-4"><?php echo e($emptyMessage); ?></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/group-bookings/partials/list-table.blade.php ENDPATH**/ ?>