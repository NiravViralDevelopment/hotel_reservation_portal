<style>
  .enquiry-form-layout .enquiry-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .enquiry-form-layout .enquiry-section-title i { color: var(--brand-accent); }
  .enquiry-form-layout .enquiry-section-hint {
    display: block;
    margin-top: 0.2rem;
    font-size: 0.75rem;
    font-weight: 400;
    color: var(--text-secondary);
  }
  .enquiry-form-layout .field-auto-badge {
    display: inline-block;
    margin-left: 0.35rem;
    padding: 0.05rem 0.4rem;
    border-radius: 999px;
    border: 1px solid color-mix(in srgb, var(--brand-accent) 35%, var(--border-color));
    color: var(--brand-accent);
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    vertical-align: middle;
  }
  .enquiry-form-layout .enquiry-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.25rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
  .enquiry-form-layout select.day-auto,
  .enquiry-form-layout input.nights-auto,
  .enquiry-form-layout input.cxl-due-auto,
  .enquiry-form-layout input.room-period-auto {
    pointer-events: none;
    background-color: var(--bs-secondary-bg);
  }
  .enquiry-form-layout .room-rate-card {
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1rem 1rem 0.85rem;
    height: 100%;
    background: var(--bg-body);
  }
  .enquiry-form-layout .room-rate-card h3 {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0 0 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  .enquiry-form-layout .room-rate-card h3 i { color: var(--brand-accent); }
  .enquiry-form-layout .revenue-panel {
    height: 100%;
    border: 1px solid color-mix(in srgb, var(--brand-accent) 32%, var(--border-color));
    border-radius: 0.75rem;
    padding: 1.15rem 1.25rem;
    background: color-mix(in srgb, var(--brand-accent) 9%, var(--bg-surface, #fff));
  }
  .enquiry-form-layout .revenue-panel .form-control {
    font-size: 1.35rem;
    font-weight: 650;
    background: var(--bg-surface, #fff);
  }
  .enquiry-form-layout .revenue-panel .form-text {
    margin-bottom: 0;
  }
  .enquiry-form-layout .stay-date-breakdown {
    margin-top: 1.25rem;
  }
  .enquiry-form-layout .stay-date-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0 0 0.2rem;
  }
  .enquiry-form-layout .stay-date-hint {
    margin: 0 0 0.75rem;
    font-size: 0.75rem;
    color: var(--text-secondary);
  }
  .enquiry-form-layout .stay-date-table {
    width: 100%;
    margin: 0;
    font-size: 0.8125rem;
  }
  .enquiry-form-layout .stay-date-table th {
    white-space: nowrap;
    font-size: 0.75rem;
  }
  .enquiry-form-layout .stay-date-table td {
    vertical-align: middle;
  }
  .enquiry-form-layout .stay-date-table .form-control {
    min-width: 4.75rem;
  }
  .enquiry-form-layout .stay-date-table tfoot td,
  .enquiry-form-layout .stay-date-table tfoot th {
    font-weight: 650;
    background: var(--bs-secondary-bg);
    border-top: 2px solid var(--border-color);
  }
</style>
<?php /**PATH D:\working\h_r_p\resources\views/enquiries/partials/entry-form-styles.blade.php ENDPATH**/ ?>