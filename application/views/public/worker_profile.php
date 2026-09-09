<div style="min-height:80vh;background:var(--gray-100);padding:48px 0;">
  <div class="container" style="max-width:860px;">
    <div style="margin-bottom:20px;">
      <a href="javascript:history.back()" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <?php if (!empty($profile)): ?>
      <?php include(APPPATH . 'views/public/partials/worker_profile_card.php'); ?>
    <?php else: ?>
      <div class="alert alert-warning">Worker profile not found.</div>
    <?php endif; ?>
  </div>
</div>
