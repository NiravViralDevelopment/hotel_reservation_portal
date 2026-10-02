{{-- Immediate page loader: visible on refresh / navigation --}}
<style>
  #hgbmsPageLoader{position:fixed;inset:0;z-index:12000;display:flex;align-items:center;justify-content:center;background:rgba(248,250,252,.92);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);transition:opacity .28s ease,visibility .28s ease}
  #hgbmsPageLoader.is-hidden{opacity:0;visibility:hidden;pointer-events:none}
  #hgbmsPageLoader .hgbms-loader-card{display:flex;flex-direction:column;align-items:center;gap:.85rem;padding:1.25rem 1.5rem;border-radius:1rem;background:#fff;border:1px solid #e5e7eb;box-shadow:0 16px 40px rgba(15,23,42,.12);min-width:180px}
  #hgbmsPageLoader .hgbms-loader-ring{width:42px;height:42px;border-radius:50%;border:3px solid #d1d5db;border-top-color:#0d9488;animation:hgbmsLoaderSpin .75s linear infinite}
  #hgbmsPageLoader .hgbms-loader-text{font-family:Inter,system-ui,sans-serif;font-size:.875rem;font-weight:600;color:#1e3a5f;letter-spacing:.01em}
  #hgbmsPageLoader .hgbms-loader-sub{font-family:Inter,system-ui,sans-serif;font-size:.75rem;color:#6b7280}
  [data-theme="dark"] #hgbmsPageLoader{background:rgba(15,23,42,.88)}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-card{background:#1f2937;border-color:#374151;box-shadow:0 16px 40px rgba(0,0,0,.45)}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-text{color:#f3f4f6}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-sub{color:#9ca3af}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-ring{border-color:#4b5563;border-top-color:#14b8a6}
  @keyframes hgbmsLoaderSpin{to{transform:rotate(360deg)}}
</style>
<div id="hgbmsPageLoader" role="status" aria-live="polite" aria-busy="true" aria-label="Loading">
  <div class="hgbms-loader-card">
    <div class="hgbms-loader-ring" aria-hidden="true"></div>
    <div class="hgbms-loader-text">Loading</div>
    <div class="hgbms-loader-sub">Please wait…</div>
  </div>
</div>
