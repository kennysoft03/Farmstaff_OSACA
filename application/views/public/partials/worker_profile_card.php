<?php
// Expects $profile array (from get_worker_full_profile)
$w       = $profile;
$score   = (float)($w['trust_score'] ?? 50);
$ring    = $score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'low');
$history = $w['work_history'] ?? [];
$skills  = $w['skills'] ?? [];
$ratings = $w['ratings'] ?? [];
$incidents = array_filter($w['incidents'] ?? [], fn($i) => $i['status'] === 'accepted');
?>

<div class="card" style="margin-bottom:20px;">
  <!-- Profile header -->
  <div style="background:linear-gradient(135deg,var(--green-dark),#2d7a3e);padding:28px 28px 0;border-radius:12px 12px 0 0;">
    <div style="display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap;">
      <!-- Avatar -->
      <div class="worker-avatar" style="border-color:#fff;flex-shrink:0;">
        <?php if (!empty($w['photo']) && file_exists(FCPATH . $w['photo'])): ?>
          <img src="<?php echo base_url($w['photo']); ?>" alt="Worker Photo">
        <?php else: ?>
          👤
        <?php endif; ?>
      </div>
      <!-- Name & ID -->
      <div style="flex:1;color:#fff;padding-bottom:20px;">
        <div style="margin-bottom:6px;">
          <span class="worker-id-pill" style="background:rgba(255,255,255,.25);"><?php echo htmlspecialchars($w['worker_id']); ?></span>
          <?php
          $status_colors = ['active'=>'#28a745','inactive'=>'#6c757d','flagged'=>'#ffc107','suspended'=>'#dc3545'];
          $sc = $status_colors[$w['status']] ?? '#6c757d';
          ?>
          <span style="background:<?php echo $sc; ?>;color:#fff;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;margin-left:8px;text-transform:uppercase;">
            <?php echo htmlspecialchars($w['status']); ?>
          </span>
        </div>
        <h3 style="color:#fff;margin:0 0 4px;"><?php echo htmlspecialchars($w['firstname'] . ' ' . $w['lastname']); ?></h3>
        <?php if (!empty($history)): ?>
          <div style="color:rgba(255,255,255,.8);font-size:.9rem;">
            <?php echo htmlspecialchars($history[0]['role']); ?> @ <?php echo htmlspecialchars($history[0]['farm_name'] ?? '—'); ?>
          </div>
        <?php endif; ?>
        <div style="color:rgba(255,255,255,.7);font-size:.82rem;margin-top:4px;">
          📍 <?php echo htmlspecialchars($w['lga'] . ', ' . $w['state']); ?>
          &nbsp;|&nbsp; 📅 Registered <?php echo date('M Y', strtotime($w['created_at'])); ?>
        </div>
      </div>
      <!-- Trust Score Ring -->
      <div style="text-align:center;padding-bottom:20px;">
        <div class="trust-score-ring <?php echo $ring; ?>" style="background:rgba(255,255,255,.95);">
          <?php echo number_format($score, 0); ?>
          <span style="font-size:.6rem;font-weight:500;opacity:.7;">/ 100</span>
        </div>
        <div style="color:rgba(255,255,255,.8);font-size:.72rem;margin-top:6px;">Trust Score</div>
      </div>
    </div>
  </div>

  <!-- Body tabs -->
  <div class="card-body" style="padding:0;">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:0;border-bottom:1px solid var(--gray-200);">
      <!-- Work History -->
      <div style="padding:24px;border-right:1px solid var(--gray-200);">
        <h6 style="margin-bottom:14px;color:var(--green-dark);">📋 Work History (<?php echo count($history); ?>)</h6>
        <?php if (empty($history)): ?>
          <p style="color:var(--gray-600);font-size:.85rem;">No work history recorded.</p>
        <?php else: ?>
          <?php foreach (array_slice($history, 0, 3) as $h): ?>
          <div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--gray-200);">
            <div style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($h['role']); ?></div>
            <div style="font-size:.8rem;color:var(--gray-600);">
              <?php echo htmlspecialchars($h['farm_name'] ?? '—'); ?>
              &nbsp;|&nbsp; <?php echo date('M Y', strtotime($h['start_date'])); ?>
              — <?php echo $h['is_current'] ? '<span style="color:var(--green-dark);font-weight:600;">Present</span>' : date('M Y', strtotime($h['end_date'])); ?>
            </div>
            <?php if (!empty($h['duration_days'])): ?>
              <div style="font-size:.75rem;color:var(--gray-600);"><?php echo ceil($h['duration_days']/30); ?> months</div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
          <?php if (count($history) > 3): ?>
            <div style="font-size:.8rem;color:var(--green-dark);">+ <?php echo count($history)-3; ?> more records</div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Skills -->
      <div style="padding:24px;">
        <h6 style="margin-bottom:14px;color:var(--green-dark);">🏆 Skills (<?php echo count($skills); ?>)</h6>
        <?php if (empty($skills)): ?>
          <p style="color:var(--gray-600);font-size:.85rem;">No skills recorded.</p>
        <?php else: ?>
          <div>
            <?php foreach ($skills as $skill): ?>
              <span class="skill-tag <?php echo $skill['verified'] ? 'verified' : ''; ?>">
                <?php echo htmlspecialchars($skill['skill_name']); ?>
                <span style="font-size:.7rem;opacity:.7;">(<?php echo $skill['proficiency']; ?>)</span>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Summary row -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));padding:20px 24px;gap:20px;">
      <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:800;color:var(--green-dark);"><?php echo count($history); ?></div>
        <div style="font-size:.78rem;color:var(--gray-600);">Employment Records</div>
      </div>
      <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:800;color:var(--gold);"><?php echo count($ratings); ?></div>
        <div style="font-size:.78rem;color:var(--gray-600);">Performance Ratings</div>
      </div>
      <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:800;color:var(--green-light);"><?php echo count(array_filter($skills, fn($s)=>$s['verified'])); ?></div>
        <div style="font-size:.78rem;color:var(--gray-600);">Verified Skills</div>
      </div>
      <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:800;color:<?php echo count($incidents)>0?'var(--danger)':'var(--green-dark)'; ?>;"><?php echo count($incidents); ?></div>
        <div style="font-size:.78rem;color:var(--gray-600);">Accepted Incidents</div>
      </div>
    </div>
  </div>
</div>
