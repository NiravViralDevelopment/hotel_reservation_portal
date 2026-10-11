{{-- Immediate page loader: visible on refresh / navigation --}}
<style>
  #hgbmsPageLoader{position:fixed;inset:0;z-index:12000;display:flex;align-items:center;justify-content:center;background:rgba(243,246,251,.92);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);transition:opacity .28s ease,visibility .28s ease}
  #hgbmsPageLoader.is-hidden{opacity:0;visibility:hidden;pointer-events:none}
  #hgbmsPageLoader .hgbms-loader-card{display:flex;flex-direction:column;align-items:center;gap:.85rem;padding:1.25rem 1.5rem;border-radius:1rem;background:#fff;border:1px solid #e3e8f1;box-shadow:0 16px 40px rgba(2,34,90,.12);min-width:180px}
  #hgbmsPageLoader .hgbms-loader-ring{width:42px;height:42px;border-radius:50%;border:3px solid #d5deeb;border-top-color:#02225a;animation:hgbmsLoaderSpin .75s linear infinite}
  #hgbmsPageLoader .hgbms-loader-text{font-family:Inter,system-ui,sans-serif;font-size:.875rem;font-weight:600;color:#02225a;letter-spacing:.01em}
  #hgbmsPageLoader .hgbms-loader-sub{font-family:Inter,system-ui,sans-serif;font-size:.75rem;color:#6b7280}
  [data-theme="dark"] #hgbmsPageLoader{background:rgba(8,17,30,.88)}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-card{background:#101b2e;border-color:#2a3a52;box-shadow:0 16px 40px rgba(0,0,0,.45)}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-text{color:#e8eef8}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-sub{color:#a8b3c7}
  [data-theme="dark"] #hgbmsPageLoader .hgbms-loader-ring{border-color:#2a3a52;border-top-color:#b7cff8}
  @keyframes hgbmsLoaderSpin{to{transform:rotate(360deg)}}
</style>
<div id="hgbmsPageLoader" role="status" aria-live="polite" aria-busy="true" aria-label="Loading">
  <div class="hgbms-loader-card">
    <div class="hgbms-loader-ring" aria-hidden="true"></div>
    <div class="hgbms-loader-text">Loading</div>
    <div class="hgbms-loader-sub">Please wait…</div>
  </div>
</div>
