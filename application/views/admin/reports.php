<?php
$wbm_labels = json_encode(array_column($workers_by_month, 'month'));
$wbm_values = json_encode(array_column($workers_by_month, 'total'));
$ebm_labels = json_encode(array_column($employers_by_month, 'month'));
$ebm_values = json_encode(array_column($employers_by_month, 'total'));
$inc_labels  = json_encode(array_unique(array_column($incident_stats, 'incident_type')));
$inc_values  = json_encode(array_column($incident_stats, 'total'));
$s = $stats;
?>

<!-- KPI Summary -->
<div class="stat-cards" style="margin-bottom:28px;">
  <div class="stat-card green"><div class="sc-icon">🏚️</div><div><div class="sc-val"><?php echo number_format($s['total_employers']??0); ?></div><div class="sc-lbl">Total Employers</div></div></div>
  <div class="stat-card green"><div class="sc-icon">👥</div><div><div class="sc-val"><?php echo number_format($s['total_workers']??0); ?></div><div class="sc-lbl">Total Workers</div></div></div>
  <div class="stat-card blue"><div class="sc-icon">✅</div><div><div class="sc-val"><?php echo number_format($s['verified_profiles']??0); ?></div><div class="sc-lbl">Verified Profiles</div></div></div>
  <div class="stat-card gold"><div class="sc-icon">📊</div><div><div class="sc-val"><?php echo number_format($s['avg_farm_rating']??0,1); ?>/5</div><div class="sc-lbl">Avg Farm Rating</div></div></div>
</div>

<!-- Charts Row -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px;">
  <div class="card">
    <div class="card-header"><h5>📈 Worker Registrations (12 months)</h5></div>
    <div class="card-body">
      <canvas id="workersChart" height="100"
        data-labels='<?php echo $wbm_labels; ?>'
        data-values='<?php echo $wbm_values; ?>'></canvas>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h5>⚠️ Incidents by Type</h5></div>
    <div class="card-body">
      <?php if (empty($incident_stats)): ?>
        <p style="color:var(--gray-600);text-align:center;padding:20px;">No incident data.</p>
      <?php else: ?>
        <canvas id="incidentChart" height="200"
          data-labels='<?php echo $inc_labels; ?>'
          data-values='<?php echo $inc_values; ?>'></canvas>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Top Workers -->
<div class="card" style="margin-bottom:24px;">
  <div class="card-header"><h5>🏆 Top Rated Workers</h5></div>
  <div class="table-responsive">
    <?php if (empty($top_workers)): ?>
      <p style="padding:16px;color:var(--gray-600);">No worker ratings yet.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Rank</th><th>Worker</th><th>Registry ID</th><th>Trust Score</th><th>Avg Rating</th><th>Ratings</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($top_workers as $i => $w): ?>
          <tr>
            <td style="font-weight:800;color:var(--green-dark);">#<?php echo $i+1; ?></td>
            <td style="font-weight:600;"><?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?></td>
            <td><span class="worker-id-pill"><?php echo htmlspecialchars($w['worker_id']); ?></span></td>
            <td>
              <?php $ts=(float)$w['trust_score']; ?>
              <span style="font-weight:700;color:<?php echo $ts>=70?'var(--green-dark)':($ts>=40?'var(--gold)':'var(--danger)'); ?>;"><?php echo number_format($ts,0); ?>/100</span>
            </td>
            <td><span style="color:var(--gold);"><?php echo !empty($w['avg_rating'])?str_repeat('★',round($w['avg_rating'])):'—'; ?></span> <?php echo !empty($w['avg_rating'])?number_format($w['avg_rating'],1):''; ?></td>
            <td><?php echo $w['rating_count']??0; ?></td>
            <td><a href="<?php echo site_url('admin/worker/'.$w['id']); ?>" class="btn btn-outline btn-sm">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<!-- Skill Distribution -->
<div class="card">
  <div class="card-header"><h5>🏆 Top Skills Across Platform</h5></div>
  <div class="card-body">
    <?php if (empty($skill_dist)): ?>
      <p style="color:var(--gray-600);">No skill data yet.</p>
    <?php else: ?>
      <?php $max = max(array_column($skill_dist,'total')); ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:10px;">
        <?php foreach ($skill_dist as $sk): ?>
          <div style="display:flex;align-items:center;gap:10px;">
            <span style="min-width:160px;font-size:.85rem;font-weight:600;"><?php echo htmlspecialchars($sk['skill_name']); ?></span>
            <div style="flex:1;background:var(--gray-200);border-radius:4px;height:10px;overflow:hidden;">
              <div style="height:100%;background:var(--green-light);width:<?php echo ($sk['total']/$max)*100; ?>%;border-radius:4px;"></div>
            </div>
            <span style="font-size:.8rem;font-weight:700;min-width:30px;"><?php echo $sk['total']; ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
