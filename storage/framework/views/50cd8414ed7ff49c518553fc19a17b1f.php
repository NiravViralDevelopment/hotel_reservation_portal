<?php $__env->startSection('title', $enquiry->group_name ?: ($enquiry->ref ?: 'Enquiry')); ?>
<?php $__env->startSection('page', 'enquiries'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .enq-show .enq-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1rem;
    align-items: center;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }
  .enq-show .enq-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .enq-show .enq-section-title i { color: var(--brand-accent); }
  .enq-show .enq-dl {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.85rem 1.25rem;
  }
  @media (max-width: 575.98px) {
    .enq-show .enq-dl { grid-template-columns: 1fr; }
  }
  .enq-show .enq-dl-item { min-width: 0; }
  .enq-show .enq-dl-label {
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 0.2rem;
  }
  .enq-show .enq-dl-value {
    font-weight: 600;
    color: var(--text-primary);
    word-break: break-word;
  }
  .enq-show .enq-dl-value.muted { font-weight: 500; color: var(--text-secondary); }
  .enq-show .room-tile {
    background: var(--bg-body);
    border-radius: 0.75rem;
    padding: 1rem;
    height: 100%;
    border: 1px solid var(--border-color);
  }
  .enq-show .room-tile-label {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  .enq-show .room-tile-label i { color: var(--brand-accent); }
  .enq-show .revenue-value {
    font-size: 1.35rem;
    font-weight: 650;
  }
  .enq-show .response-timeline { list-style: none; margin: 0; padding: 0; }
  .enq-show .response-timeline-item {
    position: relative;
    padding: 0 0 1.25rem 1.5rem;
    border-left: 2px solid var(--border-color);
  }
  .enq-show .response-timeline-item:last-child { padding-bottom: 0; border-left-color: transparent; }
  .enq-show .response-timeline-item::before {
    content: '';
    position: absolute;
    left: -0.4rem;
    top: 0.25rem;
    width: 0.7rem;
    height: 0.7rem;
    border-radius: 50%;
    background: var(--brand-accent);
    border: 2px solid var(--bg-surface);
    box-shadow: 0 0 0 1px var(--brand-accent);
  }
  .enq-show .response-date { font-weight: 700; color: var(--text-primary); }
  .enq-show .response-meta { font-size: 0.8125rem; color: var(--text-secondary); }
  .enq-show .response-body {
    margin-top: 0.5rem;
    padding: 0.85rem 1rem;
    background: var(--bg-body);
    border-radius: 0.5rem;
    color: var(--text-primary);
    white-space: pre-wrap;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
  $text = function ($value) {
      return filled($value) ? $value : '—';
  };
?>

<div class="enq-show">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo e(route('enquiries.index')); ?>">Enquiries</a></li>
          <li class="breadcrumb-item active"><?php echo e($enquiry->group_name ?: ($enquiry->ref ?: 'Enquiry')); ?></li>
        </ol>
      </nav>
      <h1 class="page-title mb-2"><?php echo e($enquiry->group_name ?: 'Enquiry'); ?></h1>
      <div class="enq-meta">
        <span><?php echo e($enquiry->ref ?: 'No ref no'); ?></span>
        <?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
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
<?php endif; ?>
      </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="<?php echo e(route('enquiries.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('convert', $enquiry)): ?>
        <?php if(! $enquiry->is_confirm && ! $enquiry->is_cancel): ?>
          <form method="POST" action="<?php echo e(route('enquiries.convert', $enquiry)); ?>" data-confirm-title="Confirm enquiry" data-confirm="<?php echo e(sprintf('Confirm enquiry "%s"? It will move to Group Bookings.', $enquiry->group_name ?? 'this enquiry')); ?>" data-confirm-button="Confirm" data-confirm-variant="primary" data-confirm-icon="bi-check-circle">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-accent btn-sm">
              <i class="bi bi-check2-circle"></i> Confirm
            </button>
          </form>
        <?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
        <a href="<?php echo e(route('enquiries.edit', $enquiry)); ?>" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="#client-response" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-chat-left-text"></i> Add remark
        </a>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $enquiry)): ?>
        <form method="POST" action="<?php echo e(route('enquiries.destroy', $enquiry)); ?>" data-confirm-title="Delete enquiry" data-confirm="<?php echo e(sprintf("Are you sure you want to delete enquiry \"%s\"?\n\nThis will permanently remove it and cannot be undone.", $enquiry->group_name ?? 'this enquiry')); ?>" data-confirm-button="Delete">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
            <i class="bi bi-trash"></i>
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-calendar3"></i> Dates</h2>
    </div>
    <div class="card-body">
      <div class="enq-dl">
        <div class="enq-dl-item">
          <div class="enq-dl-label">Enquiry Date</div>
          <div class="enq-dl-value"><?php echo e($enquiry->enquiry_date?->format('d M Y') ?? '—'); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Response Date</div>
          <div class="enq-dl-value"><?php echo e($enquiry->response_date?->format('d M Y') ?? '—'); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Arrival Date</div>
          <div class="enq-dl-value"><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
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
<?php endif; ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Departure Date</div>
          <div class="enq-dl-value"><?php echo e($enquiry->check_out?->format('d M Y') ?? '—'); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Day</div>
          <div class="enq-dl-value"><?php echo e($text($enquiry->day ?: $enquiry->check_in_day)); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Nights</div>
          <div class="enq-dl-value"><?php echo e($enquiry->nights ?? '—'); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Total Room per Night</div>
          <div class="enq-dl-value"><?php echo e($enquiry->rooms_per_night ?? '—'); ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-people"></i> Group</h2>
    </div>
    <div class="card-body">
      <div class="enq-dl">
        <div class="enq-dl-item">
          <div class="enq-dl-label">Group Name</div>
          <div class="enq-dl-value"><?php echo e($text($enquiry->group_name)); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Ref No</div>
          <div class="enq-dl-value"><?php echo e($text($enquiry->ref)); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Email ID</div>
          <div class="enq-dl-value">
            <?php if($enquiry->email): ?>
              <a href="mailto:<?php echo e($enquiry->email); ?>"><?php echo e($enquiry->email); ?></a>
            <?php else: ?>
              <span class="muted">—</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Status</div>
          <div class="enq-dl-value"><?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
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
<?php endif; ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
    </div>
    <div class="card-body">
      <?php if($enquiry->check_in && $enquiry->check_out && $enquiry->check_out->gte($enquiry->check_in)): ?>
        <?php
          $dailyByDate = [];
          foreach (($enquiry->daily_room_rates ?? []) as $row) {
              if (is_array($row) && ! empty($row['date'])) {
                  $dailyByDate[\Illuminate\Support\Carbon::parse($row['date'])->toDateString()] = $row;
              }
          }
          $stayCursor = $enquiry->check_in->copy()->startOfDay();
          $stayEnd = $enquiry->check_out->copy()->startOfDay();
          $stayCount = 0;
        ?>
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th rowspan="2">Date</th>
                <th colspan="2">Single Room &amp; Rate</th>
                <th colspan="2">Double Room &amp; Rate</th>
                <th colspan="2">Triple Room &amp; Rate</th>
              </tr>
              <tr>
                <th>Rooms</th>
                <th>Rate</th>
                <th>Rooms</th>
                <th>Rate</th>
                <th>Rooms</th>
                <th>Rate</th>
              </tr>
            </thead>
            <tbody>
              <?php while($stayCursor->lt($stayEnd) && $stayCount < 400): ?>
                <?php
                  $dayKey = $stayCursor->toDateString();
                  $dayRow = $dailyByDate[$dayKey] ?? null;
                  $singleRooms = $dayRow['single_rooms'] ?? $enquiry->single_rooms;
                  $singleRate = $dayRow['single_rate'] ?? $enquiry->single_rate;
                  $doubleRooms = $dayRow['double_rooms'] ?? $enquiry->double_rooms;
                  $doubleRate = $dayRow['double_rate'] ?? $enquiry->double_rate;
                  $tripleRooms = $dayRow['triple_rooms'] ?? $enquiry->triple_rooms;
                  $tripleRate = $dayRow['triple_rate'] ?? $enquiry->triple_rate;
                ?>
                <tr>
                  <td class="fw-semibold text-nowrap"><?php echo e($stayCursor->format('d-m-Y')); ?></td>
                  <td><?php echo e($singleRooms ?? '—'); ?></td>
                  <td><?php echo e($money($singleRate)); ?></td>
                  <td><?php echo e($doubleRooms ?? '—'); ?></td>
                  <td><?php echo e($money($doubleRate)); ?></td>
                  <td><?php echo e($tripleRooms ?? '—'); ?></td>
                  <td><?php echo e($money($tripleRate)); ?></td>
                </tr>
                <?php
                  $stayCursor->addDay();
                  $stayCount++;
                ?>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-secondary mb-0">Arrival and departure dates are not set.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h2 class="enq-section-title"><i class="bi bi-currency-pound"></i> Revenue and terms</h2>
    </div>
    <div class="card-body">
      <div class="enq-dl">
        <div class="enq-dl-item">
          <div class="enq-dl-label">Basis</div>
          <div class="enq-dl-value"><?php echo e($text($enquiry->basis)); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Option Date</div>
          <div class="enq-dl-value"><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-alert','data' => ['date' => $enquiry->option_date]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enquiry->option_date)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $attributes = $__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__attributesOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8)): ?>
<?php $component = $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8; ?>
<?php unset($__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8); ?>
<?php endif; ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">CXL Due Date</div>
          <div class="enq-dl-value"><?php if (isset($component)) { $__componentOriginal899a5b6ea6f91be084ad9a28a4fd56e8 = $component; } ?>
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
<?php endif; ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">CXL Policy</div>
          <div class="enq-dl-value"><?php echo e($text($enquiry->cxl_policy)); ?></div>
        </div>
        <div class="enq-dl-item">
          <div class="enq-dl-label">Total Revenue</div>
          <div class="enq-dl-value revenue-value"><?php echo e($money($enquiry->total_revenue)); ?></div>
        </div>
        <div class="enq-dl-item" style="grid-column: 1 / -1;">
          <div class="enq-dl-label">Remarks</div>
          <div class="enq-dl-value <?php echo e($enquiry->remarks ? '' : 'muted'); ?>" style="white-space: pre-wrap;"><?php echo e($text($enquiry->remarks)); ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
      <div class="col-lg-5">
        <div class="card h-100" id="client-response">
          <div class="card-header">
            <h2 class="enq-section-title"><i class="bi bi-reply"></i> Add remark</h2>
          </div>
          <div class="card-body">
            <p class="small text-secondary mb-3">
              Record a remark for this enquiry. Each save is kept in the history for <strong><?php echo e($enquiry->group_name); ?></strong><?php if($enquiry->ref): ?> (<?php echo e($enquiry->ref); ?>)<?php endif; ?>.
            </p>
            <form method="POST" action="<?php echo e(route('enquiries.response', $enquiry)); ?>" class="enquiry-form" novalidate>
              <?php echo csrf_field(); ?>
              <div class="mb-3">
                <label for="response_date" class="form-label">Response date <span class="text-danger">*</span></label>
                <input
                  type="date"
                  name="response_date"
                  id="response_date"
                  class="form-control <?php $__errorArgs = ['response_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                  value="<?php echo e(old('response_date')); ?>"
                  <?php if($enquiry->enquiry_date): ?>
                    min="<?php echo e($enquiry->enquiry_date->format('Y-m-d')); ?>"
                    data-min-date="<?php echo e($enquiry->enquiry_date->format('Y-m-d')); ?>"
                    data-min-date-fixed="1"
                  <?php endif; ?>
                >
                <?php $__errorArgs = ['response_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              <div class="mb-3">
                <label for="client_response" class="form-label">Remark <span class="text-danger">*</span></label>
                <textarea
                  name="client_response"
                  id="client_response"
                  rows="4"
                  class="form-control <?php $__errorArgs = ['client_response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                  placeholder="Enter remark"
                  maxlength="2000"
                ><?php echo e(old('client_response')); ?></textarea>
                <?php $__errorArgs = ['client_response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              <button type="submit" class="btn btn-accent">
                <i class="bi bi-check-lg"></i> Save remark
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="<?php echo e(auth()->user()->can('update', $enquiry) ? 'col-lg-7' : 'col-12'); ?>">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 class="enq-section-title"><i class="bi bi-clock-history"></i> Remark history</h2>
          <span class="badge bg-light text-dark border"><?php echo e($enquiry->responses->count()); ?></span>
        </div>
        <div class="card-body">
          <?php if($enquiry->responses->isNotEmpty()): ?>
            <ul class="response-timeline">
              <?php $__currentLoopData = $enquiry->responses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $response): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="response-timeline-item">
                  <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div class="response-date"><?php echo e($response->response_date?->format('d M Y') ?? 'No date'); ?></div>
                    <div class="response-meta">
                      <?php echo e($response->user?->name ?? 'System'); ?>

                      · <?php echo e($response->created_at?->format('d M Y H:i')); ?>

                    </div>
                  </div>
                  <div class="response-body"><?php echo e($response->client_response); ?></div>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          <?php else: ?>
            <div class="text-center text-secondary py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No remarks yet for this enquiry.
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
                <div class="mt-2">
                  <a href="#client-response" class="btn btn-sm btn-outline-secondary">Add the first remark</a>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/enquiry-validation.js')); ?>?v=<?php echo e(@filemtime(public_path('assets/js/enquiry-validation.js'))); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/enquiries/show.blade.php ENDPATH**/ ?>