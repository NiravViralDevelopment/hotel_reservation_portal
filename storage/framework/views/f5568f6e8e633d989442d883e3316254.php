

<?php $__env->startSection('title', 'Booking contract'); ?>
<?php $__env->startSection('page', 'group-bookings'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .gb-contract .gb-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .gb-contract .gb-section-title i { color: var(--brand-accent); }
  .gb-contract .contract-frame {
    width: 100%;
    min-height: 720px;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    background: #f8f9fa;
  }
  .gb-contract .gb-meta { font-size: 0.8125rem; color: var(--text-muted); }
  .gb-contract .contract-paper-wrap {
    background: #eef1f5;
    border-radius: 0.5rem;
    padding: 1rem;
    overflow: auto;
  }
  .gb-contract .contract-paper {
    background: transparent;
    color: #20262d;
    max-width: none;
    margin: 0 auto;
    min-height: 0;
    padding: 0;
    border-radius: 0;
    box-shadow: none;
    outline: none;
  }
  .gb-contract .contract-paper:focus {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2), 0 8px 24px rgba(15, 23, 42, 0.12);
  }
  .gb-contract .contract-paper .document {
    --primary: #17365d;
    --accent: #2d5f8b;
    --border: #b8c2cc;
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    font-family: Arial, Helvetica, sans-serif;
    line-height: 1.45;
  }
  .gb-contract .contract-paper .page {
    position: relative;
    min-height: 0;
    padding: 22px 20px 16px;
    margin-bottom: 16px;
    background: #fff;
    box-shadow: 0 3px 18px rgba(0, 0, 0, .10);
    overflow: hidden;
  }
  .gb-contract .contract-paper .page:last-child { margin-bottom: 0; }
  .gb-contract .contract-paper .header { text-align: center; margin-bottom: 18px; }
  .gb-contract .contract-paper .logo {
    color: #17365d;
    font-size: 28px;
    font-weight: 900;
    letter-spacing: -1px;
  }
  .gb-contract .contract-paper .logo .hotel-logo-img {
    display: block;
    max-height: 64px;
    max-width: 220px;
    margin: 0 auto 4px;
    object-fit: contain;
  }
  .gb-contract .contract-paper .logo small {
    display: block;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0;
    margin-top: 2px;
  }
  .gb-contract .contract-paper .hotel-address { font-size: 11px; color: #444; }
  .gb-contract .contract-paper .legal-intro {
    margin: 14px 0 18px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
  }
  .gb-contract .contract-paper .document table {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0 20px;
  }
  .gb-contract .contract-paper .document th,
  .gb-contract .contract-paper .document td {
    border: 1px solid #b8c2cc;
    padding: 7px 8px;
    vertical-align: top;
    font-size: 10.5px;
    width: auto;
    background: transparent;
    color: #20262d;
    font-weight: 400;
    text-align: left;
  }
  .gb-contract .contract-paper .document th {
    background: #17365d;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
  }
  .gb-contract .contract-paper .two-col th {
    text-align: center;
    background: #eaf0f6;
    color: #17365d;
    font-size: 12px;
  }
  .gb-contract .contract-paper .requirements td:first-child {
    width: 31%;
    font-weight: 700;
    background: #f8fafc;
  }
  .gb-contract .contract-remove-row {
    float: right;
    margin-left: 8px;
    border: 0;
    background: #f1d6d6;
    color: #8b2e2e;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    padding: 3px 6px;
    cursor: pointer;
  }
  .gb-contract .contract-paper .section-title {
    margin: 18px 0 8px;
    padding: 8px 10px;
    background: #17365d;
    color: #fff;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
  }
  .gb-contract .contract-paper .notice {
    padding: 11px 13px;
    background: #f7f9fb;
    border-left: 4px solid #2d5f8b;
    margin: 12px 0 18px;
    font-size: 10.5px;
  }
  .gb-contract .contract-paper .document p { font-size: 10.5px; margin: 8px 0; line-height: 1.45; }
  .gb-contract .contract-paper .document ul { font-size: 10.5px; margin: 7px 0 14px 20px; }
  .gb-contract .contract-paper .document li { font-size: 10.5px; margin: 5px 0; line-height: 1.45; }
  .gb-contract .contract-paper .signature-row {
    display: flex;
    justify-content: center;
    align-items: stretch;
    gap: 12px;
    width: 100%;
    max-width: 920px;
    margin: 12px auto 20px;
  }
  .gb-contract .contract-paper .signature-col {
    flex: 0 0 calc(50% - 6px);
    width: calc(50% - 6px);
    max-width: calc(50% - 6px);
    border: 1px solid #b8c2cc;
    background: #fff;
  }
  .gb-contract .contract-paper .signature-col-title {
    background: #eef2f6;
    color: #17365d;
    font-size: 11px;
    font-weight: 700;
    padding: 8px 10px;
    border-bottom: 1px solid #b8c2cc;
    text-align: center;
  }
  .gb-contract .contract-paper .signature-box {
    height: 160px;
    margin: 0;
    padding: 6px 10px 8px;
    border-bottom: 1px dashed #aab3bc;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #8a929a;
    font-size: 10px;
    background: #fff;
    overflow: hidden;
  }
  .gb-contract .contract-paper .signature-box--hotel,
  .gb-contract .contract-paper .signature-box--client {
    height: 160px;
  }
  .gb-contract .contract-paper .signature-box .signature-img {
    display: block;
    width: 96%;
    height: 148px;
    max-width: 96%;
    object-fit: contain;
    object-position: center;
    transform: scale(1.7);
    transform-origin: center center;
  }
  .gb-contract .contract-paper .signature-date {
    padding: 8px 10px;
    font-size: 10.5px;
    border-top: 1px solid #d8dde2;
  }
  .gb-contract .contract-paper .footer {
    position: static;
    margin-top: 16px;
    padding-top: 6px;
    border-top: 1px solid #d8dde2;
    text-align: center;
    color: #6b737c;
    font-size: 8.5px;
    background: transparent;
  }
  .gb-contract .contract-paper .field { color: #111; font-weight: 600; }
  .gb-contract .contract-add-row {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    margin: 0 0 8px;
    border: 0;
    background: #17365d;
    color: #fff;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 10px;
    cursor: pointer;
  }
  .gb-contract .editor-hint {
    font-size: 0.8125rem;
    color: var(--text-muted);
  }
  .gb-contract .contract-photo-manage-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.75rem;
  }
  .gb-contract .contract-photo-manage-item {
    position: relative;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    overflow: hidden;
    background: #fff;
  }
  .gb-contract .contract-photo-manage-item img {
    display: block;
    width: 100%;
    height: 110px;
    object-fit: cover;
  }
  .gb-contract .contract-photo-manage-item .photo-remove {
    position: absolute;
    top: 6px;
    right: 6px;
  }
  .gb-contract .contract-paper .contract-photos-section {
    margin: 14px 0 18px;
  }
  .gb-contract .contract-paper .contract-photos-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-top: 8px;
  }
  .gb-contract .contract-paper .contract-photo-item {
    border: 1px solid #b8c2cc;
    background: #f8fafc;
    padding: 6px;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .gb-contract .contract-paper .contract-photo-img {
    display: block;
    width: 100%;
    max-height: 180px;
    object-fit: contain;
  }
  .gb-contract .contract-paper .contract-photos-empty {
    font-size: 10.5px;
    color: #6b737c;
    margin: 8px 0 0;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="gb-contract">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo e(route('group-bookings.index')); ?>">Group Bookings</a></li>
          <li class="breadcrumb-item"><a href="<?php echo e(route('group-bookings.show', $enquiry)); ?>"><?php echo e($enquiry->block_id ?: 'Booking'); ?></a></li>
          <li class="breadcrumb-item active">Contract</li>
        </ol>
      </nav>
      <h1 class="page-title">Hotel contract</h1>
      <p class="page-subtitle mb-0">
        This contract is filled from this group booking. Click any text to edit it, then save.
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="<?php echo e(route('group-bookings.contract.pdf', $enquiry)); ?>" target="_blank" rel="noopener" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-earmark-pdf"></i> Preview PDF
      </a>
      <a href="<?php echo e(route('group-bookings.show', $enquiry)); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-body py-3">
          <div class="text-secondary small">Hotel</div>
          <div class="fw-semibold"><?php echo e($selectedHotel?->name ?: '—'); ?></div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
              <div class="fw-semibold">Hotel photos</div>
              <div class="text-secondary small mb-0">
                Attach hotel photos after Group &amp; Commercial Details. They appear in the contract below.
              </div>
            </div>
            <div class="text-secondary small"><?php echo e($enquiry->contractPhotos->count()); ?> / 20</div>
          </div>

          <?php if($enquiry->contractPhotos->isNotEmpty()): ?>
            <div class="contract-photo-manage-grid mb-3">
              <?php $__currentLoopData = $enquiry->contractPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="contract-photo-manage-item">
                  <img src="<?php echo e(route('group-bookings.contract.photos.show', [$enquiry, $photo])); ?>" alt="<?php echo e($photo->original_name ?: 'Hotel photo'); ?>">
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
                    <?php if (! ($enquiry->is_cancel)): ?>
                      <form method="POST" action="<?php echo e(route('group-bookings.contract.photos.destroy', [$enquiry, $photo])); ?>" class="photo-remove" data-confirm-title="Remove photo" data-confirm="Remove this photo from the contract?" data-confirm-button="Remove">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger" title="Remove photo">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          <?php else: ?>
            <div class="text-secondary small mb-3">No photos attached yet.</div>
          <?php endif; ?>

          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
            <?php if (! ($enquiry->is_cancel)): ?>
              <form method="POST" action="<?php echo e(route('group-bookings.contract.photos.store', $enquiry)); ?>" enctype="multipart/form-data" class="row g-2 align-items-end">
                <?php echo csrf_field(); ?>
                <div class="col-md-8">
                  <label for="contract_photos" class="form-label">Upload photos</label>
                  <input
                    type="file"
                    name="photos[]"
                    id="contract_photos"
                    class="form-control <?php $__errorArgs = ['photos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php $__errorArgs = ['photos.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    multiple
                    required
                  >
                  <div class="form-text">JPG, PNG, WEBP, or GIF. Max 4 MB each. Up to 10 files per upload.</div>
                  <?php $__errorArgs = ['photos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  <?php $__errorArgs = ['photos.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-image"></i> Attach photos
                  </button>
                </div>
              </form>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>

      <form method="POST" action="<?php echo e(route('group-bookings.contract.update', $enquiry)); ?>" id="gbContractForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="booking_contract_html" id="booking_contract_html" value="">

        <div class="card">
          <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
              <div class="editor-hint">Hotel, client, dates, rooms, and rates come from this group. Click the contract to edit any wording.</div>
              <div class="btn-group btn-group-sm" role="group" aria-label="Format">
                <button type="button" class="btn btn-outline-secondary" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="insertUnorderedList" title="Bullets"><i class="bi bi-list-ul"></i></button>
              </div>
            </div>
            <div class="contract-paper-wrap">
              <div
                id="contractEditor"
                class="contract-paper"
                contenteditable="true"
                role="textbox"
                aria-label="Editable booking contract"
              ><?php echo $contractHtml; ?></div>
            </div>
            <?php $__errorArgs = ['booking_contract_html'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block mt-2"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $enquiry)): ?>
          <?php if (! ($enquiry->is_cancel)): ?>
            <button type="submit" class="btn btn-accent mt-3">
              <i class="bi bi-check-lg"></i> Save editable contract
            </button>
          <?php endif; ?>
        <?php endif; ?>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  (function () {
    var form = document.getElementById('gbContractForm');
    var editor = document.getElementById('contractEditor');
    var htmlInput = document.getElementById('booking_contract_html');

    document.querySelectorAll('[data-cmd]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.execCommand(btn.getAttribute('data-cmd'), false, null);
        if (editor) editor.focus();
      });
    });

    function bindRemoveButton(row) {
      if (!row || row.querySelector('.contract-remove-row')) return;
      var cell = row.cells && row.cells[0];
      if (!cell) return;

      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'contract-remove-row';
      button.setAttribute('contenteditable', 'false');
      button.title = 'Remove';
      button.innerHTML = '<i class="bi bi-dash-lg"></i>';
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        row.remove();
      });
      cell.appendChild(button);
    }

    function addCommercialRow(table) {
      var row = document.createElement('tr');
      var label = document.createElement('td');
      var value = document.createElement('td');
      label.textContent = 'New detail';
      value.textContent = '—';
      row.appendChild(label);
      row.appendChild(value);
      table.appendChild(row);
      bindRemoveButton(row);

      var range = document.createRange();
      range.selectNodeContents(label);
      var selection = window.getSelection();
      selection.removeAllRanges();
      selection.addRange(range);
      row.scrollIntoView({ block: 'center' });
    }

    function ensureCommercialAddButton() {
      if (!editor) return;
      var table = editor.querySelector('table.requirements');
      if (!table) return;

      Array.prototype.forEach.call(table.rows, bindRemoveButton);

      if (editor.querySelector('.contract-add-row')) return;

      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'contract-add-row';
      button.setAttribute('contenteditable', 'false');
      button.innerHTML = '<i class="bi bi-plus-lg"></i> Add more';
      table.parentNode.insertBefore(button, table);
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        addCommercialRow(table);
      });
    }

    ensureCommercialAddButton();

    if (form && editor && htmlInput) {
      form.addEventListener('submit', function () {
        var copy = editor.cloneNode(true);
        copy.querySelectorAll('.contract-add-row, .contract-remove-row').forEach(function (button) {
          button.remove();
        });
        htmlInput.value = copy.innerHTML;
      });
    }
  })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/group-bookings/contract.blade.php ENDPATH**/ ?>