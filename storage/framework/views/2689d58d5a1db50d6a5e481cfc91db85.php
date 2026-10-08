<?php $__env->startSection('title', 'Documents'); ?>
<?php $__env->startSection('page', 'hotels'); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo e(route('hotels.index')); ?>">Hotels</a></li>
          <li class="breadcrumb-item active">Documents</li>
        </ol>
      </nav>
      <h1 class="page-title">Documents</h1>
      <p class="page-subtitle">PDF / DOC files for <?php echo e($hotel->name); ?>.</p>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $hotel)): ?>
      <a href="<?php echo e(route('hotels.documents.create', $hotel)); ?>" class="btn btn-accent btn-sm"><i class="bi bi-upload"></i> Upload document</a>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="<?php echo e(route('hotels.documents.index', $hotel)); ?>" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group input-group-sm" style="max-width: 260px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input
            type="search"
            name="q"
            value="<?php echo e(request('q')); ?>"
            class="form-control"
            placeholder="Search title or file…"
            aria-label="Search documents"
          >
        </div>
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        <?php if(request()->filled('q')): ?>
          <a href="<?php echo e(route('hotels.documents.index', $hotel)); ?>" class="btn btn-outline-danger btn-sm">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'title','label' => 'Title','default' => 'created_at','defaultDir' => 'desc']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'title','label' => 'Title','default' => 'created_at','default-dir' => 'desc']); ?>
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
            <th>File</th>
            <?php if (isset($component)) { $__componentOriginal3c1df23c66879bbdd25946c6c08cdc07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c1df23c66879bbdd25946c6c08cdc07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sortable-th','data' => ['column' => 'created_at','label' => 'Uploaded','default' => 'created_at','defaultDir' => 'desc']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sortable-th'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['column' => 'created_at','label' => 'Uploaded','default' => 'created_at','default-dir' => 'desc']); ?>
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
          <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="fw-semibold"><?php echo e($document->title); ?></td>
              <td>
                <span class="<?php echo e($document->isPdf() ? 'text-danger' : 'text-primary'); ?> fs-4" title="<?php echo e($document->original_name); ?>">
                  <i class="bi <?php echo e($document->isPdf() ? 'bi-file-earmark-pdf' : 'bi-file-earmark-word'); ?>"></i>
                </span>
                <span class="small text-secondary ms-1"><?php echo e($document->original_name); ?></span>
              </td>
              <td>
                <div><?php echo e($document->created_at?->format('d M Y')); ?></div>
                <?php if($document->uploadedBy): ?>
                  <div class="small text-secondary"><?php echo e($document->uploadedBy->name); ?></div>
                <?php endif; ?>
              </td>
              <td class="text-end text-nowrap">
                <a href="<?php echo e(route('hotels.documents.download', [$hotel, $document])); ?>" class="btn btn-sm btn-outline-secondary" title="Download">
                  <i class="bi bi-download"></i>
                </a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $hotel)): ?>
                  <form method="POST" action="<?php echo e(route('hotels.documents.destroy', [$hotel, $document])); ?>" class="d-inline" data-confirm-title="Delete document" data-confirm="<?php echo e("Are you sure you want to delete the document \"{$document->title}\"?\n\nThis will permanently remove it and cannot be undone."); ?>" data-confirm-button="Delete">
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
              <td colspan="4" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-file-earmark-text fs-3"></i></div>
                <div>No documents yet.</div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $hotel)): ?>
                  <a href="<?php echo e(route('hotels.documents.create', $hotel)); ?>" class="btn btn-accent btn-sm mt-2">
                    <i class="bi bi-upload"></i> Upload document
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php echo $__env->make('partials.pagination-footer', ['paginator' => $documents], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/hotel-documents/index.blade.php ENDPATH**/ ?>