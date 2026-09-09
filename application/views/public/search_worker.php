<div style="min-height:80vh;background:var(--gray-100);padding:48px 0;">
  <div class="container" style="max-width:800px;">
    <h2 style="margin-bottom:4px;">🔍 Worker Background Check</h2>
    <p style="color:var(--gray-600);margin-bottom:28px;">Search by phone number or registry ID to view a worker's verified profile.</p>

    <!-- Search Form -->
    <div class="card" style="margin-bottom:28px;">
      <div class="card-body">
        <form method="get" action="<?php echo site_url('search-worker'); ?>">
          <div style="display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:end;">
            <div class="form-group" style="margin:0;">
              <label class="form-label">Search By</label>
              <select name="type" class="form-control" style="min-width:140px;">
                <option value="phone"     <?php echo ($type==='phone')    ?'selected':''; ?>>Phone Number</option>
                <option value="worker_id" <?php echo ($type==='worker_id')?'selected':''; ?>>Registry ID</option>
              </select>
            </div>
            <div class="form-group" style="margin:0;">
              <label class="form-label">Search Query</label>
              <input type="text" name="q" class="form-control"
                value="<?php echo htmlspecialchars($query); ?>"
                placeholder="Enter phone number or FSR-XXXXXX ID" autofocus>
            </div>
            <div class="form-group" style="margin:0;">
              <label class="form-label">&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block">Search</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php if ($query !== '' && !$worker): ?>
      <div class="alert alert-warning">
        <strong>No worker found</strong> for "<?php echo htmlspecialchars($query); ?>". This worker may not be registered in the Farm Staff Registry yet.
      </div>
    <?php endif; ?>

    <?php if ($profile): ?>
      <?php include(APPPATH . 'views/public/partials/worker_profile_card.php'); ?>
    <?php endif; ?>

    <?php if ($query === ''): ?>
      <div class="card">
        <div class="card-body" style="text-align:center;padding:40px;">
          <div style="font-size:3rem;margin-bottom:14px;">🛡️</div>
          <h4>Verify Before You Hire</h4>
          <p style="color:var(--gray-600);max-width:440px;margin:0 auto;">
            Search any worker using their phone number or Farm Staff Registry ID to instantly access their verified work history, skills and trust score.
          </p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
