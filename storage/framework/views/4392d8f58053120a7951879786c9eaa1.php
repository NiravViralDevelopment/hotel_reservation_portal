<?php $__env->startSection('title', $enquiry->block_id ?: 'Group booking'); ?>
<?php $__env->startSection('page', ! empty($cancelledContext) ? 'cancelled-bookings' : 'group-bookings'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .gb-show .gb-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .gb-show .gb-section-title i {
    width: 1.85rem;
    height: 1.85rem;
    border-radius: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(26, 77, 156, 0.1);
    color: var(--brand-accent);
    font-size: 0.95rem;
    flex-shrink: 0;
  }
  .gb-show .gb-dl {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
  }
  .gb-show .gb-dl.gb-dl-wide {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  @media (max-width: 1199.98px) {
    .gb-show .gb-dl.gb-dl-wide { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 575.98px) {
    .gb-show .gb-dl,
    .gb-show .gb-dl.gb-dl-wide { grid-template-columns: 1fr; }
  }
  .gb-show .gb-dl > div {
    min-width: 0;
    background: var(--bg-body);
    border-radius: 0.65rem;
    padding: 0.8rem 0.9rem;
  }
  .gb-show .gb-dl > div.span-all { grid-column: 1 / -1; }
  .gb-show .gb-dl > div.is-highlight {
    background: linear-gradient(135deg, rgba(2, 34, 90, 0.06), rgba(26, 77, 156, 0.12));
    border: 1px solid rgba(26, 77, 156, 0.16);
  }
  .gb-show .gb-label {
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
  }
  .gb-show .gb-value { font-weight: 600; word-break: break-word; }
  .gb-show .gb-value.revenue-value { font-size: 1.35rem; font-weight: 650; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
  $text = fn ($value) => filled($value) ? $value : '—';
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
  $date = fn ($value) => $value?->format('d M Y') ?? '—';
?>

<div class="gb-show">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo e(! empty($cancelledContext) ? route('cancelled-bookings.index') : route('group-bookings.index')); ?>"><?php echo e(! empty($cancelledContext) ? 'Cancelled Bookings' : 'Confirm Group Bookings'); ?></a></li>
          <li class="breadcrumb-item active"><?php echo e($enquiry->block_id ?: ($enquiry->group_name ?: 'Booking')); ?></li>
        </ol>
      </nav>
      <h1 class="page-title mb-2"><?php echo e($enquiry->group_name ?: 'Group booking'); ?></h1>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="text-secondary"><?php echo e($enquiry->block_id ?: 'No block ID'); ?></span>
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
    <div class="show-actions">
      <a href="<?php echo e(! empty($cancelledContext) ? route('cancelled-bookings.index') : route('group-bookings.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <?php if(empty($cancelledContext)): ?>
        <a href="<?php echo e(route('group-bookings.contract', $enquiry)); ?>" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-file-earmark-pdf"></i> Contract
        </a>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
        <?php if(! $enquiry->is_cancel): ?>
          <a href="<?php echo e(route('group-bookings.edit', $enquiry)); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
          <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelGroupBookingModal">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <?php if($enquiry->is_cancel): ?>
    <div class="cancel-banner">
      <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
      <div>
        <div class="fw-semibold mb-1">Cancellation reason</div>
        <div class="mb-0" style="white-space: pre-wrap;"><?php echo e($enquiry->cancellation_reason ?: '—'); ?></div>
      </div>
    </div>
  <?php endif; ?>

  <div class="detail-kpis kpis-6">
    <div class="detail-kpi">
      <div class="detail-kpi-label">Arrival</div>
      <div class="detail-kpi-value"><?php echo e($date($enquiry->check_in)); ?></div>
    </div>
    <div class="detail-kpi">
      <div class="detail-kpi-label">Departure</div>
      <div class="detail-kpi-value"><?php echo e($date($enquiry->check_out)); ?></div>
    </div>
    <div class="detail-kpi">
      <div class="detail-kpi-label">Nights</div>
      <div class="detail-kpi-value"><?php echo e($enquiry->nights ?? '—'); ?></div>
    </div>
    <div class="detail-kpi">
      <div class="detail-kpi-label">Total RNs</div>
      <div class="detail-kpi-value"><?php echo e($enquiry->total_rns ?? '—'); ?></div>
    </div>
    <div class="detail-kpi">
      <div class="detail-kpi-label">Payment</div>
      <div class="detail-kpi-value"><?php echo e($text($enquiry->payment_status)); ?></div>
    </div>
    <div class="detail-kpi is-primary">
      <div class="detail-kpi-label">Total revenue</div>
      <div class="detail-kpi-value"><?php echo e($money($enquiry->total_revenue)); ?></div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-lg-6 d-flex">
  <div class="card mb-0 h-100 w-100">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-calendar3"></i> Stay</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Date of Arrival</div><div class="gb-value"><?php echo e($date($enquiry->check_in)); ?></div></div>
        <div><div class="gb-label">Date of Departure</div><div class="gb-value"><?php echo e($date($enquiry->check_out)); ?></div></div>
        <div><div class="gb-label">Day</div><div class="gb-value"><?php echo e($text($enquiry->day)); ?></div></div>
        <div><div class="gb-label">No. of Nights</div><div class="gb-value"><?php echo e($enquiry->nights ?? '—'); ?></div></div>
        <div class="span-all"><div class="gb-label">Block ID</div><div class="gb-value"><?php echo e($text($enquiry->block_id)); ?></div></div>
      </div>
    </div>
  </div>
    </div>
    <div class="col-lg-6 d-flex">
  <div class="card mb-0 h-100 w-100">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-person-vcard"></i> Client</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Client</div><div class="gb-value"><?php echo e($text($enquiry->client)); ?></div></div>
        <div><div class="gb-label">Contact number</div><div class="gb-value"><?php echo e($text($enquiry->contact_number)); ?></div></div>
        <div><div class="gb-label">Email ID</div><div class="gb-value"><?php echo e($text($enquiry->email)); ?></div></div>
        <div><div class="gb-label">Status</div><div class="gb-value"><?php if (isset($component)) { $__componentOriginal435aefee4aa6dd7f20df034696ae03b9 = $component; } ?>
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
<?php endif; ?></div></div>
      </div>
    </div>
  </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-lg-7 d-flex">
  <div class="card mb-0 h-100 w-100">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-file-earmark-text"></i> Contract and payment</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Contract Sent On</div><div class="gb-value"><?php echo e($date($enquiry->contract_sent_on)); ?></div></div>
        <div><div class="gb-label">Contract Recd On</div><div class="gb-value"><?php echo e($date($enquiry->contract_received_on)); ?></div></div>
        <div><div class="gb-label">Saved to Doc</div><div class="gb-value"><?php echo e($text($enquiry->saved_to_doc)); ?></div></div>
        <div class="span-all">
          <div class="gb-label">Hotel / booking contract</div>
          <div class="gb-value">
            <a href="<?php echo e(route('group-bookings.contract', $enquiry)); ?>">Open contract workspace</a>
            <?php if($enquiry->hasBookingContract()): ?>
              <div class="small text-secondary mt-1">
                Booking copy:
                <a href="<?php echo e(route('group-bookings.contract.download', $enquiry)); ?>"><?php echo e($enquiry->booking_contract_original_name); ?></a>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div><div class="gb-label">Payment Term</div><div class="gb-value"><?php if($enquiry->payment_term): ?><?php echo e($enquiry->payment_term); ?><?php if($enquiry->payment_term_days !== null): ?> (<?php echo e($enquiry->payment_term_days); ?> <?php echo e((int) $enquiry->payment_term_days === 1 ? 'day' : 'days'); ?>)<?php endif; ?>@else—<?php endif; ?></div></div>
        <div><div class="gb-label">Due Date</div><div class="gb-value"><?php echo e($date($enquiry->payment_due_date)); ?></div></div>
        <div><div class="gb-label">Payment Status</div><div class="gb-value"><?php echo e($text($enquiry->payment_status)); ?></div></div>
      </div>
    </div>
  </div>
    </div>
    <div class="col-lg-5 d-flex">
  <div class="card mb-0 h-100 w-100">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-shield-exclamation"></i> Cancellation and commission</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">CXL Policy</div><div class="gb-value"><?php echo e($text($enquiry->cxl_policy)); ?></div></div>
        <div><div class="gb-label">CXL Due Date</div><div class="gb-value"><?php echo e($date($enquiry->cxl_due_date)); ?></div></div>
        <?php if($enquiry->is_cancel): ?>
          <div><div class="gb-label">CXL Date</div><div class="gb-value"><?php echo e($date($enquiry->cxl_date)); ?></div></div>
        <?php endif; ?>
        <div><div class="gb-label">Commission</div><div class="gb-value"><?php echo e($enquiry->has_commission === null ? '—' : ($enquiry->has_commission ? 'Yes' : 'No')); ?></div></div>
        <?php if($enquiry->has_commission): ?>
          <div><div class="gb-label">Commission %</div><div class="gb-value"><?php echo e($enquiry->commission_percentage === null ? '—' : rtrim(rtrim(number_format((float) $enquiry->commission_percentage, 2), '0'), '.').'%'); ?></div></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-door-open"></i> Rooms and revenue</h2></div>
    <div class="card-body">
      <div class="gb-dl gb-dl-wide">
        <div><div class="gb-label">Single RNs</div><div class="gb-value"><?php echo e($enquiry->single_rooms ?? '—'); ?></div></div>
        <div><div class="gb-label">Single Gross Rate</div><div class="gb-value"><?php echo e($money($enquiry->single_rate)); ?></div></div>
        <div><div class="gb-label">Double RNs</div><div class="gb-value"><?php echo e($enquiry->double_rooms ?? '—'); ?></div></div>
        <div><div class="gb-label">Double Gross Rate</div><div class="gb-value"><?php echo e($money($enquiry->double_rate)); ?></div></div>
        <div><div class="gb-label">Triple RNs</div><div class="gb-value"><?php echo e($enquiry->triple_rooms ?? '—'); ?></div></div>
        <div><div class="gb-label">Triple Gross Rate</div><div class="gb-value"><?php echo e($money($enquiry->triple_rate)); ?></div></div>
        <div><div class="gb-label">Total RNs</div><div class="gb-value"><?php echo e($enquiry->total_rns ?? '—'); ?></div></div>
        <div class="is-highlight"><div class="gb-label">Total Rev</div><div class="gb-value revenue-value"><?php echo e($money($enquiry->total_revenue)); ?></div></div>
        <div><div class="gb-label">BB Revenue (Nett £10)</div><div class="gb-value"><?php echo e($money($enquiry->bb_revenue)); ?></div></div>
        <div><div class="gb-label">Dinner Revenue (Nett £)</div><div class="gb-value"><?php echo e($money($enquiry->dinner_revenue)); ?></div></div>
        <div><div class="gb-label">Nett Rev EX VAT &amp; BF</div><div class="gb-value"><?php echo e($money($enquiry->nett_rev_ex_vat)); ?></div></div>
        <div><div class="gb-label">City Tax</div><div class="gb-value"><?php echo e($money($enquiry->hotel?->calculatedCityTax($enquiry->total_rns, $enquiry->nett_rev_ex_vat))); ?></div></div>
        <div><div class="gb-label">BB/DBB</div><div class="gb-value"><?php echo e($text($enquiry->basis)); ?></div></div>
      </div>
      <?php
        $staySegments = is_array($enquiry->stay_segments) ? array_values($enquiry->stay_segments) : [];
        $showSegmentBreakfast = in_array($enquiry->basis, ['BB', 'DBB'], true);
        $showSegmentDinner = $enquiry->basis === 'DBB';
        $segmentBreakfast = (float) ($enquiry->breakfast_rate ?? 0);
        $segmentDinner = (float) ($enquiry->dinner_rate ?? 0);
      ?>
      <?php if($staySegments !== []): ?>
        <div class="table-responsive mt-3">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Day</th>
                <th class="text-end">Nights</th>
                <th class="text-end">Single</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Double</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Triple</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Revenue</th>
                <?php if($showSegmentBreakfast): ?>
                  <th class="text-end">BB</th>
                <?php endif; ?>
                <th class="text-end">Nett Rev EX VAT &amp; BF</th>
                <?php if($showSegmentDinner): ?>
                  <th class="text-end">Dinner</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php
                $segmentNightsTotal = 0;
                $segmentRevenueTotal = 0.0;
                $segmentBbTotal = 0.0;
                $segmentDinnerTotal = 0.0;
                $segmentNettTotal = 0.0;
              ?>
              <?php $__currentLoopData = $staySegments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $segmentNights = (int) ($segment['nights'] ?? 0);
                  $segmentSingle = (int) ($segment['single_rooms'] ?? 0);
                  $segmentSingleRate = (float) ($segment['single_rate'] ?? 0);
                  $segmentDouble = (int) ($segment['double_rooms'] ?? 0);
                  $segmentDoubleRate = (float) ($segment['double_rate'] ?? 0);
                  $segmentTriple = (int) ($segment['triple_rooms'] ?? 0);
                  $segmentTripleRate = (float) ($segment['triple_rate'] ?? 0);
                  $segmentRevenue = $segmentNights * (($segmentSingle * $segmentSingleRate) + ($segmentDouble * $segmentDoubleRate) + ($segmentTriple * $segmentTripleRate));
                  $segmentCovers = $segmentNights * (($segmentSingle * 1) + ($segmentDouble * 2) + ($segmentTriple * 3));
                  $segmentBb = $segmentCovers * $segmentBreakfast;
                  $segmentDinnerAmount = $segmentCovers * $segmentDinner;
                  $segmentNett = (($segmentRevenue * 100) / 120) - $segmentBb - ($enquiry->dinner_rate !== null ? $segmentDinnerAmount : 0);
                  $segmentNightsTotal += $segmentNights;
                  $segmentRevenueTotal += $segmentRevenue;
                  $segmentBbTotal += $segmentBb;
                  $segmentDinnerTotal += $segmentDinnerAmount;
                  $segmentNettTotal += $segmentNett;
                ?>
                <tr>
                  <td class="text-nowrap"><?php echo e(\Illuminate\Support\Carbon::parse($segment['check_in'])->format('d-m-Y')); ?></td>
                  <td class="text-nowrap"><?php echo e(\Illuminate\Support\Carbon::parse($segment['check_out'])->format('d-m-Y')); ?></td>
                  <td><?php echo e($segment['day'] ?? '—'); ?></td>
                  <td class="text-end"><?php echo e($segmentNights); ?></td>
                  <td class="text-end"><?php echo e($segmentSingle); ?></td>
                  <td class="text-end"><?php echo e($money($segmentSingleRate)); ?></td>
                  <td class="text-end"><?php echo e($segmentDouble); ?></td>
                  <td class="text-end"><?php echo e($money($segmentDoubleRate)); ?></td>
                  <td class="text-end"><?php echo e($segmentTriple); ?></td>
                  <td class="text-end"><?php echo e($money($segmentTripleRate)); ?></td>
                  <td class="text-end fw-semibold"><?php echo e($money($segmentRevenue)); ?></td>
                  <?php if($showSegmentBreakfast): ?>
                    <td class="text-end"><?php echo e($money($segmentBb)); ?></td>
                  <?php endif; ?>
                  <td class="text-end fw-semibold"><?php echo e($money($segmentNett)); ?></td>
                  <?php if($showSegmentDinner): ?>
                    <td class="text-end"><?php echo e($money($segmentDinnerAmount)); ?></td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
              <tr>
                <th scope="row" colspan="3">Total</th>
                <td class="text-end fw-semibold"><?php echo e($segmentNightsTotal); ?></td>
                <td colspan="6"></td>
                <td class="text-end fw-semibold"><?php echo e($money($segmentRevenueTotal)); ?></td>
                <?php if($showSegmentBreakfast): ?>
                  <td class="text-end fw-semibold"><?php echo e($money($segmentBbTotal)); ?></td>
                <?php endif; ?>
                <td class="text-end fw-semibold"><?php echo e($money($segmentNettTotal)); ?></td>
                <?php if($showSegmentDinner): ?>
                  <td class="text-end fw-semibold"><?php echo e($money($segmentDinnerTotal)); ?></td>
                <?php endif; ?>
              </tr>
            </tfoot>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-receipt"></i> Rooming and invoice</h2></div>
    <div class="card-body">
      <div class="gb-dl gb-dl-wide">
        <div><div class="gb-label">Update</div><div class="gb-value"><?php echo e($text($enquiry->booking_update)); ?></div></div>
        <div><div class="gb-label">Rooming</div><div class="gb-value"><?php echo e($text($enquiry->rooming)); ?></div></div>
        <div><div class="gb-label">Invoice Status</div><div class="gb-value"><?php echo e($text($enquiry->invoice_status)); ?></div></div>
        <div><div class="gb-label">Invoice Number</div><div class="gb-value"><?php echo e($text($enquiry->invoice_number)); ?></div></div>
        <div><div class="gb-label">Invoice Sent On</div><div class="gb-value"><?php echo e($date($enquiry->invoice_sent_on)); ?></div></div>
        <div><div class="gb-label">Invoice Amount</div><div class="gb-value"><?php echo e($money($enquiry->invoice_amount)); ?></div></div>
        <?php if($enquiry->has_commission): ?>
          <div><div class="gb-label">Commission Payable Status</div><div class="gb-value"><?php echo e($text($enquiry->commission_payable_status)); ?></div></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if(! $enquiry->is_cancel): ?>
  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
    <div class="modal fade" id="cancelGroupBookingModal" tabindex="-1" aria-labelledby="cancelGroupBookingModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="<?php echo e(route('enquiries.cancel-booking', $enquiry)); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="cancel_scope" value="group">
            <div class="modal-header">
              <h5 class="modal-title" id="cancelGroupBookingModalTitle">Cancel group booking</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p class="text-secondary">Cancel this group booking? The status will be set to Cancelled and it will move to Cancelled Bookings.</p>
              <label for="booking_cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
              <textarea name="cancellation_reason" id="booking_cancellation_reason" rows="4" class="form-control <?php $__errorArgs = ['cancellation_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required maxlength="2000" placeholder="Enter the cancellation reason"><?php echo e(old('cancellation_reason')); ?></textarea>
              <?php $__errorArgs = ['cancellation_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
              <button type="submit" class="btn btn-danger">Cancel group booking</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php if($errors->has('cancellation_reason')): ?>
  <?php $__env->startPush('scripts'); ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('cancelGroupBookingModal');
        if (modalEl && window.bootstrap) {
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      });
    </script>
  <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Working\hotel_reservation_portal\resources\views/group-bookings/show.blade.php ENDPATH**/ ?>