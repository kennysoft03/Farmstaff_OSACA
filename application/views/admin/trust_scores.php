<h5 style="margin-bottom:16px;">💯 Worker Trust Score Management</h5>

<div class="card">
  <div class="card-header"><h5>Active Workers — Trust Score Overview</h5></div>
  <div class="table-responsive">
    <?php if (empty($workers)): ?>
      <p style="padding:16px;color:var(--gray-600);">No active workers found.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Worker</th><th>Registry ID</th><th>Trust Score</th><th>Score Meter</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($workers as $w): ?>
          <?php $ts=(float)$w['trust_score']; $tc=$ts>=70?'var(--green-dark)':($ts>=40?'var(--gold)':'var(--danger)'); ?>
          <tr>
            <td>
              <div style="font-weight:600;"><?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?></div>
              <div style="font-size:.75rem;color:var(--gray-600);"><?php echo htmlspecialchars($w['employer_name']??'—'); ?></div>
            </td>
            <td><span class="worker-id-pill"><?php echo htmlspecialchars($w['worker_id']); ?></span></td>
            <td><span style="font-size:1.2rem;font-weight:800;color:<?php echo $tc; ?>;"><?php echo number_format($ts,0); ?></span><span style="color:var(--gray-500);font-size:.8rem;">/100</span></td>
            <td style="min-width:140px;">
              <div class="att-bar" style="margin-top:4px;"><div class="att-bar-fill" style="width:<?php echo $ts; ?>%;background:<?php echo $tc; ?>;"></div></div>
            </td>
            <td><span class="badge <?php echo $w['status']==='active'?'badge-success':'badge-secondary'; ?>"><?php echo $w['status']; ?></span></td>
            <td><a href="<?php echo site_url('admin/worker/'.$w['id']); ?>" class="btn btn-outline btn-sm">Adjust</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<div class="card" style="margin-top:20px;">
  <div class="card-body" style="padding:20px;">
    <h6 style="margin-bottom:12px;">Trust Score Reference</h6>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;">
      <div style="padding:14px;background:#d4edda;border-radius:8px;text-align:center;border-left:4px solid var(--green-dark);">
        <div style="font-size:1.4rem;font-weight:800;color:var(--green-dark);">70–100</div>
        <div style="font-size:.82rem;font-weight:600;">High Trust</div>
        <div style="font-size:.75rem;color:var(--gray-600);">Excellent record</div>
      </div>
      <div style="padding:14px;background:#fff3cd;border-radius:8px;text-align:center;border-left:4px solid var(--gold);">
        <div style="font-size:1.4rem;font-weight:800;color:var(--gold);">40–69</div>
        <div style="font-size:.82rem;font-weight:600;">Medium Trust</div>
        <div style="font-size:.75rem;color:var(--gray-600);">Some concerns</div>
      </div>
      <div style="padding:14px;background:#f8d7da;border-radius:8px;text-align:center;border-left:4px solid var(--danger);">
        <div style="font-size:1.4rem;font-weight:800;color:var(--danger);">0–39</div>
        <div style="font-size:.82rem;font-weight:600;">Low Trust</div>
        <div style="font-size:.75rem;color:var(--gray-600);">Review required</div>
      </div>
    </div>
  </div>
</div>
