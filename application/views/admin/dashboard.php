<?php
$s = $stats;
$total_employers = number_format((int)($s['total_employers']??0));
$active_employers= number_format((int)($s['active_employers']??0));
$total_workers   = number_format((int)($s['total_workers']??0));
$active_workers  = number_format((int)($s['active_workers']??0));
$pending_inc     = (int)($s['pending_incidents']??0);
$pending_rat     = (int)($s['pending_ratings']??0);
$avg_rating      = number_format((float)($s['avg_farm_rating']??0), 1);
?>

<!-- KPI Cards -->
<div class="stat-cards">
  <div class="stat-card green"><div class="sc-icon">🏚️</div><div><div class="sc-val"><?php echo $total_employers; ?></div><div class="sc-lbl">Total Employers</div></div></div>
  <div class="stat-card green"><div class="sc-icon">👥</div><div><div class="sc-val"><?php echo $total_workers; ?></div><div class="sc-lbl">Total Workers</div></div></div>
  <div class="stat-card red">
    <div class="sc-icon">⚠️</div>
    <div>
      <div class="sc-val" style="<?php echo $pending_inc>0?'color:var(--danger)':''; ?>"><?php echo $pending_inc; ?></div>
      <div class="sc-lbl">Pending Incidents</div>
    </div>
  </div>
  <div class="stat-card gold">
    <div class="sc-icon">⭐</div>
    <div>
      <div class="sc-val" style="<?php echo $pending_rat>0?'color:var(--warning)':''; ?>"><?php echo $pending_rat; ?></div>
      <div class="sc-lbl">Pending Ratings</div>
    </div>
  </div>
  <div class="stat-card blue"><div class="sc-icon">✅</div><div><div class="sc-val"><?php echo number_format((int)($s['verified_profiles']??0)); ?></div><div class="sc-lbl">Verified Profiles</div></div></div>
  <div class="stat-card gold"><div class="sc-icon">📊</div><div><div class="sc-val"><?php echo $avg_rating; ?>/5</div><div class="sc-lbl">Avg Farm Rating</div></div></div>
</div>

<!-- Alerts banner -->
<?php if ($pending_inc > 0 || $pending_rat > 0): ?>
<div class="alert alert-warning" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
  <strong>⚡ Action Required:</strong>
  <?php if ($pending_inc > 0): ?><a href="<?php echo site_url('admin/incidents?status=pending'); ?>" class="btn btn-sm btn-outline" style="border-color:var(--warning);color:var(--warning);">Review <?php echo $pending_inc; ?> incident(s)</a><?php endif; ?>
  <?php if ($pending_rat > 0): ?><a href="<?php echo site_url('admin/farm-ratings?status=pending'); ?>" class="btn btn-sm btn-outline" style="border-color:var(--warning);color:var(--warning);">Review <?php echo $pending_rat; ?> farm rating(s)</a><?php endif; ?>
</div>
<?php endif; ?>

<!-- Main grid -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">

  <!-- Recent Workers -->
  <div class="card">
    <div class="card-header">
      <h5>👥 Recent Worker Registrations</h5>
      <a href="<?php echo site_url('admin/workers'); ?>" class="btn btn-outline btn-sm">All Workers</a>
    </div>
    <div class="table-responsive">
      <?php if (empty($recent_workers)): ?>
        <p style="padding:20px;color:var(--gray-600);text-align:center;">No workers yet.</p>
      <?php else: ?>
        <table class="table">
          <thead><tr><th>Worker</th><th>ID</th><th>Trust</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($recent_workers as $w): ?>
            <tr>
              <td>
                <div style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?></div>
                <div style="font-size:.75rem;color:var(--gray-600);"><?php echo htmlspecialchars($w['employer_name']??'—'); ?></div>
              </td>
              <td><span class="worker-id-pill"><?php echo htmlspecialchars($w['worker_id']); ?></span></td>
              <td><?php $ts=(float)$w['trust_score'];?><span style="font-weight:700;color:<?php echo $ts>=70?'var(--green-dark)':($ts>=40?'var(--gold)':'var(--danger)'); ?>;"><?php echo number_format($ts,0); ?></span></td>
              <td><span class="badge <?php echo $w['status']==='active'?'badge-success':($w['status']==='flagged'?'badge-warning':'badge-danger'); ?>"><?php echo $w['status']; ?></span></td>
              <td><a href="<?php echo site_url('admin/worker/'.$w['id']); ?>" class="btn btn-outline btn-sm">View</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right panel -->
  <div style="display:flex;flex-direction:column;gap:20px;">

    <!-- Pending Incidents -->
    <div class="card">
      <div class="card-header">
        <h5 style="color:var(--danger);">⚠️ Pending Incidents</h5>
        <a href="<?php echo site_url('admin/incidents'); ?>" class="btn btn-outline btn-sm">All</a>
      </div>
      <div class="card-body" style="padding:0;">
        <?php if (empty($pending_incidents)): ?>
          <p style="padding:14px 16px;color:var(--gray-600);font-size:.85rem;margin:0;">No pending incidents. ✅</p>
        <?php else: ?>
          <?php foreach ($pending_incidents as $i): ?>
            <div style="padding:12px 16px;border-bottom:1px solid var(--gray-200);">
              <div style="font-weight:600;font-size:.88rem;"><?php echo htmlspecialchars($i['worker_name']??'—'); ?></div>
              <div style="font-size:.78rem;color:var(--gray-600);"><?php echo htmlspecialchars($i['incident_type']); ?> · <?php echo htmlspecialchars($i['farm_name']??'—'); ?></div>
              <a href="<?php echo site_url('admin/incident/'.$i['id'].'/review'); ?>" class="btn btn-sm btn-outline" style="margin-top:6px;border-color:var(--danger);color:var(--danger);">Review</a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Pending Farm Ratings -->
    <div class="card">
      <div class="card-header">
        <h5>⭐ Pending Ratings</h5>
        <a href="<?php echo site_url('admin/farm-ratings'); ?>" class="btn btn-outline btn-sm">All</a>
      </div>
      <div class="card-body" style="padding:0;">
        <?php if (empty($pending_ratings)): ?>
          <p style="padding:14px 16px;color:var(--gray-600);font-size:.85rem;margin:0;">No pending ratings. ✅</p>
        <?php else: ?>
          <?php foreach ($pending_ratings as $r): ?>
            <div style="padding:12px 16px;border-bottom:1px solid var(--gray-200);">
              <div style="font-weight:600;font-size:.88rem;"><?php echo htmlspecialchars($r['farm_name']??'—'); ?></div>
              <div style="color:var(--gold);font-size:.9rem;"><?php echo str_repeat('★',(int)$r['overall_rating']); ?></div>
              <a href="<?php echo site_url('admin/farm-ratings?status=pending'); ?>" class="btn btn-sm btn-outline" style="margin-top:6px;">Review</a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Admin Links -->
    <div class="card">
      <div class="card-header"><h5>⚡ Quick Actions</h5></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:8px;padding:14px;">
        <a href="<?php echo site_url('admin/workers'); ?>" class="btn btn-outline btn-block btn-sm">👥 Manage Workers</a>
        <a href="<?php echo site_url('admin/employers'); ?>" class="btn btn-outline btn-block btn-sm">🏚️ Manage Employers</a>
        <a href="<?php echo site_url('admin/reports'); ?>" class="btn btn-outline btn-block btn-sm">📈 View Reports</a>
        <a href="<?php echo site_url('admin/audit'); ?>" class="btn btn-outline btn-block btn-sm">🔍 Audit Trail</a>
        <a href="<?php echo site_url('admin/settings'); ?>" class="btn btn-outline btn-block btn-sm">⚙️ Settings</a>
      </div>
    </div>
  </div>
</div>

<!-- Recent Employers -->
<div class="card" style="margin-top:24px;">
  <div class="card-header">
    <h5>🏚️ Recent Employer Registrations</h5>
    <a href="<?php echo site_url('admin/employers'); ?>" class="btn btn-outline btn-sm">All Employers</a>
  </div>
  <div class="table-responsive">
    <?php if (empty($recent_employers)): ?>
      <p style="padding:20px;color:var(--gray-600);">No employers yet.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Farm Name</th><th>Contact</th><th>Phone</th><th>LGA</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($recent_employers as $e): ?>
          <tr>
            <td style="font-weight:600;"><?php echo htmlspecialchars($e['farm_name']); ?></td>
            <td style="font-size:.88rem;"><?php echo htmlspecialchars($e['contact_person']); ?></td>
            <td style="font-size:.85rem;"><?php echo htmlspecialchars($e['phone']); ?></td>
            <td style="font-size:.85rem;"><?php echo htmlspecialchars($e['lga']??'—'); ?></td>
            <td><span class="badge <?php echo $e['status']==='active'?'badge-success':($e['status']==='pending'?'badge-warning':'badge-danger'); ?>"><?php echo $e['status']; ?></span></td>
            <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($e['created_at'])); ?></td>
            <td><a href="<?php echo site_url('admin/employer/'.$e['id']); ?>" class="btn btn-outline btn-sm">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
