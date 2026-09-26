<div class="offcanvas offcanvas-end" tabindex="-1" id="filtersOffcanvas">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title"><i class="bi bi-funnel me-2"></i>Advanced Filters</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" id="globalFiltersBody">
    <p class="text-secondary small">Page-specific filters will appear here as modules are connected.</p>
    <div class="d-grid gap-2">
      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Close</button>
    </div>
  </div>
</div>

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
          <select class="form-select">
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

<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this record? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" disabled>Delete</button>
      </div>
    </div>
  </div>
</div>
