<div class="hgbms-toaster" id="hgbmsToaster" aria-live="polite" aria-atomic="true"></div>

<?php
  $toasts = [];
  if (session('success')) {
      $toasts[] = ['type' => 'success', 'message' => session('success')];
  }
  if (session('error')) {
      $toasts[] = ['type' => 'error', 'message' => session('error')];
  }
  if (session('status')) {
      $toasts[] = ['type' => 'success', 'message' => session('status')];
  }
  if (session('warning')) {
      $toasts[] = ['type' => 'warning', 'message' => session('warning')];
  }
  if (session('info')) {
      $toasts[] = ['type' => 'info', 'message' => session('info')];
  }
?>

<?php if(count($toasts)): ?>
  <script>
    window.HGBMS_FLASH_TOASTS = <?php echo json_encode($toasts, 15, 512) ?>;
  </script>
<?php endif; ?>
<?php /**PATH D:\working\h_r_p\resources\views/partials/toaster.blade.php ENDPATH**/ ?>