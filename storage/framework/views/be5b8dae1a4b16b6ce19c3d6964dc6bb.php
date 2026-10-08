<?php
  $rows = $rows ?? collect();
  $defaultSort = $defaultSort ?? 'enquiry_date';
  $defaultDir = $defaultDir ?? 'desc';
  $emptyMessage = $emptyMessage ?? 'No records found.';
  $variant = $variant ?? 'full';
  $showCancellationReason = $showCancellationReason ?? false;
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
?>

<div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
<div class="table-wrapper table-scroll-wide table-scroll-enquiries">
<?php if($variant === 'enquiry'): ?>
  <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
    <thead>
      <tr>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'enquiry_date','label' => 'Enquiry Date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'enquiry_date','label' => 'Enquiry Date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'response_date','label' => 'Response Date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'response_date','label' => 'Response Date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'check_in','label' => 'Arrival Date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'check_in','label' => 'Arrival Date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'check_out','label' => 'Departure Date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'check_out','label' => 'Departure Date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'nights','label' => 'Nights','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'nights','label' => 'Nights','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'rooms_per_night','label' => 'Total Room per Night','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'rooms_per_night','label' => 'Total Room per Night','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'group_name','label' => 'Group Name','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'group_name','label' => 'Group Name','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'ref','label' => 'Ref No','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'ref','label' => 'Ref No','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
        <th>Single</th>
        <th>Single Rate</th>
        <th>Double</th>
        <th>Double Rate</th>
        <th>Triple</th>
        <th>Triple Rate</th>
        <th>Basis</th>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'option_date','label' => 'Option Date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'option_date','label' => 'Option Date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
        <th>CXL Policy</th>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'cxl_due_date','label' => 'CXL Due Date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'cxl_due_date','label' => 'CXL Due Date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'total_revenue','label' => 'Total Revenue','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'total_revenue','label' => 'Total Revenue','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
        <th>Remarks</th>
        <?php if($showCancellationReason): ?>
          <th>CXL Date</th>
          <th>Cancellation Reason</th>
        <?php endif; ?>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="text-nowrap"><?php echo e($enquiry->enquiry_date?->format('d M Y') ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->response_date?->format('d M Y') ?? '—'); ?></td>
          <td><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-alert','data' => ['date' => $enquiry->check_in,'window' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->check_in),'window' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $attributes = $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $component = $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->check_out?->format('d M Y') ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->day ?: '—'); ?></td>
          <td><?php echo e($enquiry->nights ?? '—'); ?></td>
          <td><?php echo e($enquiry->rooms_per_night ?? '—'); ?></td>
          <td class="fw-semibold text-nowrap"><a href="<?php echo e(route('enquiries.show', $enquiry)); ?>"><?php echo e($enquiry->group_name ?: '—'); ?></a></td>
          <td class="text-nowrap"><?php echo e($enquiry->ref ?: '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->email ?: '—'); ?></td>
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
          <td><?php echo e($enquiry->single_rooms ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->single_rate)); ?></td>
          <td><?php echo e($enquiry->double_rooms ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->double_rate)); ?></td>
          <td><?php echo e($enquiry->triple_rooms ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($money($enquiry->triple_rate)); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->basis ?: '—'); ?></td>
          <td><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-alert','data' => ['date' => $enquiry->option_date,'scale' => 'option']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->option_date),'scale' => 'option']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $attributes = $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $component = $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?></td>
          <td style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($enquiry->cxl_policy); ?>"><?php echo e($enquiry->cxl_policy ?: '—'); ?></td>
          <td><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-alert','data' => ['date' => $enquiry->cxl_due_date,'window' => 30]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->cxl_due_date),'window' => 30]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $attributes = $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $component = $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?></td>
          <td class="text-nowrap fw-semibold"><?php echo e($money($enquiry->total_revenue)); ?></td>
          <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($enquiry->remarks); ?>"><?php echo e($enquiry->remarks ?: '—'); ?></td>
          <?php if($showCancellationReason): ?>
            <td class="text-nowrap"><?php echo e($enquiry->cxl_date?->format('d M Y') ?? '—'); ?></td>
            <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($enquiry->cancellation_reason); ?>"><?php echo e($enquiry->cancellation_reason ?: '—'); ?></td>
          <?php endif; ?>
          <td class="text-end text-nowrap">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $enquiry)): ?>
              <a href="<?php echo e(route('enquiries.show', $enquiry)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                <i class="bi bi-eye"></i>
              </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
              <a href="<?php echo e(route('enquiries.show', $enquiry)); ?>#client-response" class="btn btn-sm btn-outline-secondary" title="Client response for <?php echo e($enquiry->group_name); ?>">
                <i class="bi bi-chat-left-text"></i>
              </a>
              <a href="<?php echo e(route('enquiries.edit', $enquiry)); ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $enquiry)): ?>
              <form method="POST" action="<?php echo e(route('enquiries.destroy', $enquiry)); ?>" class="d-inline" data-confirm-title="Delete enquiry" data-confirm="<?php echo e(sprintf("Are you sure you want to delete enquiry \"%s\"?\n\nThis will permanently remove it and cannot be undone.", $enquiry->group_name ?? 'this enquiry')); ?>" data-confirm-button="Delete">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="<?php echo e($showCancellationReason ? 25 : 23); ?>" class="text-center text-secondary py-4"><?php echo e($emptyMessage); ?></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
<?php else: ?>
  <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
    <thead>
      <tr>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'ref','label' => 'Ref','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'ref','label' => 'Ref','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'group_name','label' => 'Group','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'group_name','label' => 'Group','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'check_in','label' => 'Arrival date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'check_in','label' => 'Arrival date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'check_out','label' => 'End date','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'check_out','label' => 'End date','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'days','label' => 'Days','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'days','label' => 'Days','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'nights','label' => 'Nights','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'nights','label' => 'Nights','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
        <th>Breakdown</th>
        <th>Client</th>
        <th>Mobile no</th>
        <th>Email ID</th>
        <th>Source</th>
        <th>Service person</th>
        <th>Subject</th>
        <th>Booking msg</th>
        <th>Adults price</th>
        <th>Child price</th>
        <th>Adults extra</th>
        <th>Child extra</th>
        <th>Total no pax</th>
        <th>Agent price</th>
        <th>Our cost</th>
        <th>P. price</th>
        <th>GST policy</th>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'total_price','label' => 'Total price','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'total_price','label' => 'Total price','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
        <th>Net price</th>
        <th>Advance</th>
        <th>Remaining</th>
        <th>Agent comm %</th>
        <th>Agent comm amt</th>
        <th>Payable to agent</th>
        <th>Service total</th>
        <th>Total tax</th>
        <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'grand_total','label' => 'Grand total','default' => $defaultSort,'defaultDir' => $defaultDir]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'grand_total','label' => 'Grand total','default' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSort),'default-dir' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultDir)]); ?>
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
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="fw-semibold text-nowrap"><a href="<?php echo e(route('enquiries.show', $enquiry)); ?>"><?php echo e($enquiry->ref ?: '—'); ?></a></td>
          <td class="text-nowrap"><?php echo e($enquiry->group_name); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->check_in?->format('d M Y') ?? '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->check_out?->format('d M Y') ?? '—'); ?></td>
          <td><?php echo e($enquiry->days ?? $enquiry->nights ?? '—'); ?></td>
          <td><?php echo e($enquiry->nights ?? '—'); ?></td>
          <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($enquiry->breakdown); ?>"><?php echo e($enquiry->breakdown ?: '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->client ?: '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->mobile ?: '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->email ?: '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->source ?: '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->service_person ?: '—'); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->subject ?: '—'); ?></td>
          <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($enquiry->booking_msg); ?>"><?php echo e($enquiry->booking_msg ?: '—'); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->adults_price ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->child_price ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->adults_extra ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->child_extra ?? 0), 2)); ?></td>
          <td><?php echo e($enquiry->total_pax ?? 0); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->agent_price ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->our_cost ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->package_price ?? 0), 2)); ?></td>
          <td class="text-nowrap"><?php echo e($enquiry->gst_policy ?: '—'); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->total_price ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->net_price ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->advance ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->remaining ?? 0), 2)); ?></td>
          <td><?php echo e(number_format((float) ($enquiry->agent_comm_percent ?? 0), 2)); ?>%</td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->agent_comm_amount ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->payable_to_agent ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->service_total ?? 0), 2)); ?></td>
          <td class="text-nowrap">£<?php echo e(number_format((float) ($enquiry->total_tax ?? 0), 2)); ?></td>
          <td class="text-nowrap fw-semibold">£<?php echo e(number_format((float) ($enquiry->grand_total ?? 0), 2)); ?></td>
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
          <td class="text-end text-nowrap">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $enquiry)): ?>
              <a href="<?php echo e(route('enquiries.show', $enquiry)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                <i class="bi bi-eye"></i>
              </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
              <a href="<?php echo e(route('enquiries.show', $enquiry)); ?>#client-response" class="btn btn-sm btn-outline-secondary" title="Client response for <?php echo e($enquiry->group_name); ?>">
                <i class="bi bi-chat-left-text"></i>
              </a>
              <a href="<?php echo e(route('enquiries.edit', $enquiry)); ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $enquiry)): ?>
              <form method="POST" action="<?php echo e(route('enquiries.destroy', $enquiry)); ?>" class="d-inline" data-confirm-title="Delete enquiry" data-confirm="<?php echo e(sprintf("Are you sure you want to delete enquiry \"%s\"?\n\nThis will permanently remove it and cannot be undone.", $enquiry->group_name ?? 'this enquiry')); ?>" data-confirm-button="Delete">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="36" class="text-center text-secondary py-4"><?php echo e($emptyMessage); ?></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
<?php endif; ?>
</div>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/enquiries/partials/list-table.blade.php ENDPATH**/ ?>