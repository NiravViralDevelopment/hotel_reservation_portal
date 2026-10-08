<?php $__env->startSection('title', 'Companies'); ?>
<?php $__env->startSection('page', 'companies'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item active">Companies</li>
        </ol>
      </nav>
      <h1 class="page-title">Companies</h1>
      <p class="page-subtitle">Parent companies and hotel group entities.</p>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Company::class)): ?>
      <a href="<?php echo e(route('companies.create')); ?>" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add company</a>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="<?php echo e(route('companies.index')); ?>" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="max-width: 260px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="<?php echo e(request('q')); ?>"
            class="form-control"
            placeholder="Search name, reg, VAT, city…"
            aria-label="Search companies"
          >
        </div>

        <select name="status" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All statuses</option>
          <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
          <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
        </select>

        <select name="country" class="form-select form-select-sm select2" style="width:auto">
          <option value="">All countries</option>
          <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($country); ?>" <?php if(request('country') === $country): echo 'selected'; endif; ?>><?php echo e($country); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>

        <?php if(request()->hasAny(['q', 'status', 'country'])): ?>
          <a href="<?php echo e(route('companies.index')); ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        <?php endif; ?>

        <?php if(request('sort')): ?>
          <input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>">
        <?php endif; ?>
        <?php if(request('dir')): ?>
          <input type="hidden" name="dir" value="<?php echo e(request('dir')); ?>">
        <?php endif; ?>
      </form>
    </div>

    <div class="table-wrapper">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'name','label' => 'Company']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'name','label' => 'Company']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'city','label' => 'Location']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'city','label' => 'Location']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'hotels','label' => 'Hotels','class' => 'text-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'hotels','label' => 'Hotels','class' => 'text-center']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'status','label' => 'Status']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'status','label' => 'Status']); ?>
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
            <th>Contract</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td>
                <div class="fw-semibold"><?php echo e($company->name); ?></div>
                <?php if($company->reg_number): ?>
                  <div class="small text-secondary">Reg. <?php echo e($company->reg_number); ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if($company->city || $company->country): ?>
                  <?php echo e(collect([$company->city, $company->country])->filter()->implode(', ')); ?>

                <?php else: ?>
                  <span class="text-secondary">—</span>
                <?php endif; ?>
              </td>
              <td class="text-center"><?php echo e($company->hotels_count); ?></td>
              <td><?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal435aefee4aa6dd7f20df034696ae03b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge-status','data' => ['status' => $company->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($company->status)]); ?>
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
              <td class="text-nowrap">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $company)): ?>
                  <a href="<?php echo e(route('companies.contracts.index', $company)); ?>" class="btn btn-sm btn-outline-primary">
                    Manage contract
                  </a>
                <?php endif; ?>
              </td>
              <td class="text-end text-nowrap">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $company)): ?>
                  <a href="<?php echo e(route('companies.show', $company)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $company)): ?>
                  <a href="<?php echo e(route('companies.edit', $company)); ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $company)): ?>
                  <form method="POST" action="<?php echo e(route('companies.destroy', $company)); ?>" class="d-inline" data-confirm-title="Delete company" data-confirm="<?php echo e("Are you sure you want to delete the company \"{$company->name}\"?\n\nThis will permanently remove it and cannot be undone."); ?>" data-confirm-button="Delete">
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
            <tr>
              <td colspan="6" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-building fs-3"></i></div>
                <div>No companies match your filters.</div>
                <?php if(request()->hasAny(['q', 'status', 'country'])): ?>
                  <a href="<?php echo e(route('companies.index')); ?>" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="bi bi-x-circle"></i> Clear filters
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php echo $__env->make('partials.pagination-footer', ['paginator' => $companies], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/companies/index.blade.php ENDPATH**/ ?>