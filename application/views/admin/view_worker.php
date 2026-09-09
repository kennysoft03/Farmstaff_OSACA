<?php $ts=(float)$worker['trust_score']; $ring=$ts>=70?'high':($ts>=40?'medium':'low'); ?>

<div style="margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;">
  <a href="<?php echo site_url('admin/workers'); ?>" class="btn btn-outline btn-sm">← Workers</a>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start;">
  <!-- Profile sidebar -->
  <div>
    <div class="card">
      <div style="background:linear-gradient(135deg,#1a2640,#1a5c2a);padding:24px;text-align:center;border-radius:12px 12px 0 0;">
        <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.2);border:3px solid rgba(255,255,255,.5);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 12px;overflow:hidden;">
          <?php if (!empty($worker['photo'])): ?><img src="<?php echo base_url($worker['photo']); ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>👤<?php endif; ?>
        </div>
        <h5 style="color:#fff;margin:0 0 4px;"><?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h5>
        <span class="worker-id-pill" style="background:rgba(255,255,255,.2);"><?php echo htmlspecialchars($worker['worker_id']); ?></span>
      </div>
      <div class="card-body" style="padding:20px;">
        <div style="text-align:center;margin-bottom:14px;">
          <div class="trust-score-ring <?php echo $ring; ?>" style="margin:0 auto;"><?php echo number_format($ts,0); ?><span style="font-size:.55rem;">/100</span></div>
          <div style="font-size:.72rem;color:var(--gray-600);margin-top:5px;">Trust Score</div>
        </div>

        <?php $info=['Phone'=>$worker['phone'],'Alt Phone'=>$worker['alt_phone']??'','Email'=>$worker['email']??'','Gender'=>ucfirst($worker['gender']??''),'LGA'=>$worker['lga']??'','ID Type'=>$worker['id_type']??'','ID Number'=>$worker['id_number']??'','Employer'=>$employer['farm_name']??'—'];
        foreach($info as $k=>$v): if(!$v) continue; ?>
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--gray-200);font-size:.83rem;">
            <span style="color:var(--gray-600);"><?php echo $k; ?></span>
            <span style="font-weight:600;text-align:right;max-width:150px;word-break:break-word;"><?php echo htmlspecialchars($v); ?></span>
          </div>
        <?php endforeach; ?>

        <!-- Admin Actions -->
        <div style="margin-top:16px;">
          <form method="post" action="<?php echo site_url('admin/worker/'.$worker['id']); ?>">
            <input type="hidden" name="action" value="update_status">
            <div class="form-group"><label class="form-label">Update Status</label>
              <select name="status" class="form-control">
                <?php foreach(['active','inactive','flagged','suspended'] as $s): ?><option value="<?php echo $s; ?>" <?php echo $worker['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option><?php endforeach; ?>
              </select>
            </div>
            <button class="btn btn-primary btn-sm btn-block">Update Status</button>
          </form>
        </div>

        <!-- Trust Score Adjustment -->
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--gray-200);">
          <form method="post" action="<?php echo site_url('admin/worker/'.$worker['id']); ?>">
            <input type="hidden" name="action" value="adjust_score">
            <div class="form-group"><label class="form-label">Adjust Trust Score</label>
              <input type="number" name="score_change" class="form-control" placeholder="e.g. +10 or -5" step="1">
            </div>
            <div class="form-group"><input type="text" name="score_reason" class="form-control" placeholder="Reason for adjustment" required></div>
            <button class="btn btn-gold btn-sm btn-block">Apply Adjustment</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Details -->
  <div style="display:flex;flex-direction:column;gap:20px;">

    <!-- Work History -->
    <div class="card">
      <div class="card-header"><h5>📋 Work History</h5></div>
      <div class="table-responsive">
        <?php if (empty($work_history)): ?>
          <p style="padding:14px;color:var(--gray-600);">No work history.</p>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Role</th><th>Farm</th><th>Start</th><th>End</th><th>Duration</th></tr></thead>
            <tbody>
              <?php foreach ($work_history as $h): ?>
              <tr>
                <td style="font-weight:600;font-size:.88rem;"><?php echo htmlspecialchars($h['role']); ?></td>
                <td style="font-size:.85rem;"><?php echo htmlspecialchars($h['farm_name']??'—'); ?></td>
                <td style="font-size:.82rem;"><?php echo date('d M Y',strtotime($h['start_date'])); ?></td>
                <td style="font-size:.82rem;"><?php echo $h['is_current']?'<strong style="color:var(--green-dark);">Present</strong>':(!empty($h['end_date'])?date('d M Y',strtotime($h['end_date'])):'—'); ?></td>
                <td style="font-size:.8rem;"><?php echo !empty($h['duration_days'])?ceil($h['duration_days']/30).' mo':'—'; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Skills + Verify -->
    <div class="card">
      <div class="card-header"><h5>🏆 Skills</h5></div>
      <div class="card-body">
        <?php if (empty($skills)): ?>
          <p style="color:var(--gray-600);">No skills recorded.</p>
        <?php else: ?>
          <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <?php foreach ($skills as $s): ?>
              <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:var(--green-pale);border-radius:20px;border:1px solid rgba(26,92,42,.2);">
                <span style="font-size:.85rem;font-weight:600;"><?php echo htmlspecialchars($s['skill_name']); ?></span>
                <?php if ($s['verified']): ?>
                  <span style="color:var(--green-dark);font-size:.75rem;">✅ Verified</span>
                <?php else: ?>
                  <form method="post" action="<?php echo site_url('admin/worker/'.$worker['id']); ?>" style="display:inline;">
                    <input type="hidden" name="action" value="verify_skill">
                    <input type="hidden" name="skill_id" value="<?php echo $s['id']; ?>">
                    <button class="btn btn-sm" style="padding:2px 8px;font-size:.7rem;border:1px solid var(--green-dark);color:var(--green-dark);background:none;cursor:pointer;border-radius:4px;">Verify</button>
                  </form>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Incidents -->
    <div class="card">
      <div class="card-header"><h5>⚠️ Incident History</h5></div>
      <div class="table-responsive">
        <?php if (empty($incidents)): ?>
          <p style="padding:14px;color:var(--gray-600);margin:0;">No incidents on record.</p>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Type</th><th>Farm</th><th>Date</th><th>Severity</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
              <?php foreach ($incidents as $i): ?>
              <tr>
                <td style="font-size:.88rem;"><?php echo htmlspecialchars($i['incident_type']); ?></td>
                <td style="font-size:.85rem;"><?php echo htmlspecialchars($i['farm_name']??'—'); ?></td>
                <td style="font-size:.82rem;"><?php echo date('d M Y',strtotime($i['incident_date'])); ?></td>
                <td><span class="badge <?php echo $i['severity']==='severe'?'badge-danger':($i['severity']==='moderate'?'badge-warning':'badge-info'); ?>"><?php echo $i['severity']; ?></span></td>
                <td><span class="badge <?php echo ['accepted'=>'badge-danger','pending'=>'badge-warning','rejected'=>'badge-success','reviewed'=>'badge-info'][$i['status']]??'badge-secondary'; ?>"><?php echo $i['status']; ?></span></td>
                <td><?php if($i['status']==='pending'): ?><a href="<?php echo site_url('admin/incident/'.$i['id'].'/review'); ?>" class="btn btn-sm btn-outline" style="color:var(--danger);border-color:var(--danger);">Review</a><?php endif; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Trust Score Log -->
    <?php if (!empty($trust_log)): ?>
    <div class="card">
      <div class="card-header"><h5>📊 Trust Score History</h5></div>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Date</th><th>Change</th><th>Old</th><th>New</th><th>Reason</th></tr></thead>
          <tbody>
            <?php foreach (array_slice($trust_log,0,10) as $l): ?>
            <tr>
              <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($l['created_at'])); ?></td>
              <td style="font-weight:700;color:<?php echo $l['change_amount']>=0?'var(--green-dark)':'var(--danger)'; ?>;"><?php echo ($l['change_amount']>=0?'+':'').$l['change_amount']; ?></td>
              <td><?php echo number_format($l['old_score'],0); ?></td>
              <td style="font-weight:700;"><?php echo number_format($l['new_score'],0); ?></td>
              <td style="font-size:.82rem;"><?php echo htmlspecialchars(substr($l['change_reason'],0,60)); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
