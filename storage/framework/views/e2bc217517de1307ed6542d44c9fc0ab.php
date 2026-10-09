<div class="modal fade" id="exportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-download me-2"></i>Export Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Format</label>
          <select class="form-select select2">
            <option>Excel (.xlsx)</option>
            <option>CSV (.csv)</option>
            <option>PDF</option>
          </select>
        </div>
        <p class="small text-secondary mb-0">Export will be enabled per module.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-accent" data-bs-dismiss="modal" disabled>
          <i class="bi bi-download me-1"></i>Export
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" id="confirmActionModalTitle">
          <i class="bi bi-exclamation-triangle-fill text-danger me-2" id="confirmActionModalIcon"></i>
          <span id="confirmActionModalHeading">Confirm</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2">
        <p class="mb-0 text-secondary" id="confirmActionModalMessage" style="white-space: pre-line;"></p>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmActionModalConfirm">
          <i class="bi bi-trash me-1" id="confirmActionModalConfirmIcon"></i>
          <span id="confirmActionModalConfirmLabel">Delete</span>
        </button>
      </div>
    </div>
  </div>
</div>
<?php /**PATH D:\working\h_r_p\resources\views/partials/modals.blade.php ENDPATH**/ ?>