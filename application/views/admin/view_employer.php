<div style="margin-bottom:16px;"><a href="<?php echo site_url('admin/employers'); ?>" class="btn btn-outline btn-sm">← Employers</a></div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start;">
  <!-- Profile -->
  <div>
    <div class="card">
      <div style="background:linear-gradient(135deg,#1a2640,#1a5c2a);padding:24px;text-align:center;border-radius:12px 12px 0 0;">
        <div style="width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,.2);border:3px solid rgba(255,255,255,.5);display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 12px;">
          <?php if (!empty($employer['logo'])): ?><img src="<?php echo base_url($employer['logo']); ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>🏚️<?php endif; ?>
        </div>
        <h5 style="color:#fff;margin:0 0 6px;"><?php echo htmlspecialchars($employer['farm_name']); ?></h5>
        <span class="badge <?php echo $employer['status']==='active'?'badge-success':($employer['status']==='pending'?'badge-warning':'badge-danger'); ?>"><?php echo $employer['status']; ?></span>
      </div>
      <div class="card-body" style="padding:18px;">
        <?php $info=['Contact'=>$employer['contact_person'],'Email'=>$employer['email'],'Phone'=>$employer['phone'],'LGA'=>$employer['lga']??'','Farm Type'=>$employer['farm_type']??'','Farm Size'=>$employer['farm_size']??'','Reg. Number'=>$employer['reg_number']??'','Reputation'=>number_format((float)$employer['reputation_score'],1).' / 5','Total Ratings'=>$employer['total_ratings'],'Joined'=>date('d M Y',strtotime($employer['created_at']))];
        foreach($info as $k=>$v): if($v==='') continue; ?>
          <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--gray-200);font-size:.83rem;">
            <span style="color:var(--gray-600);"><?php echo $k; ?></span>
            <span style="font-weight:600;text-align:right;"><?php echo htmlspecialchars($v); ?></span>
          </div>
        <?php endforeach; ?>

        <!-- Status change -->
        <form method="post" action="<?php echo site_url('admin/employer/'.$employer['id']); ?>" style="margin-top:14px;">
          <input type="hidden" name="action" value="update_status">
          <div class="form-group">
            <label class="form-label">Update Status</label>
            <select name="status" class="form-control">
              <?php foreach(['active','pending','suspended'] as $s): ?><option value="<?php echo $s; ?>" <?php echo $employer['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option><?php endforeach; ?>
            </select>
          </div>
          <button class="btn btn-primary btn-sm btn-block">Update Status</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Right -->
  <div style="display:flex;flex-direction:column;gap:20px;">

    <!-- Workers -->
    <div class="card">
      <div class="card-header"><h5>👥 Registered Workers (<?php echo count($workers); ?>)</h5></div>
      <div class="table-responsive">
        <?php if (empty($workers)): ?><p style="padding:14px;color:var(--gray-600);">No workers.</p>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Name</th><th>Registry ID</th><th>Trust</th><th>Status</th><th></th></tr></thead>
            <tbody>
              <?php foreach (array_slice($workers,0,10) as $w): ?>
              <tr>
                <td style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?></td>
                <td><span class="worker-id-pill"><?php echo htmlspecialchars($w['worker_id']); ?></span></td>
                <td><?php $ts=(float)$w['trust_score'];?><span style="font-weight:700;color:<?php echo $ts>=70?'var(--green-dark)':($ts>=40?'var(--gold)':'var(--danger)'); ?>;"><?php echo number_format($ts,0); ?></span></td>
                <td><span class="badge <?php echo $w['status']==='active'?'badge-success':'badge-secondary'; ?>"><?php echo $w['status']; ?></span></td>
                <td><a href="<?php echo site_url('admin/worker/'.$w['id']); ?>" class="btn btn-outline btn-sm">View</a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Farm Ratings -->
    <div class="card">
      <div class="card-header"><h5>⭐ Farm Ratings (<?php echo count($ratings); ?>)</h5></div>
      <div class="table-responsive">
        <?php if (empty($ratings)): ?><p style="padding:14px;color:var(--gray-600);">No ratings.</p>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Rating</th><th>Worker</th><th>Comment</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach ($ratings as $r): ?>
              <tr>
                <td><span style="color:var(--gold);"><?php echo str_repeat('★',(int)$r['overall_rating']); ?></span></td>
                <td style="font-size:.85rem;"><?php echo $r['is_anonymous']?'Anonymous':htmlspecialchars($r['worker_name']??''); ?></td>
                <td style="font-size:.82rem;max-width:200px;"><?php echo htmlspecialchars(substr($r['review_text']??'',0,60)); ?></td>
                <td><span class="badge <?php echo $r['status']==='approved'?'badge-success':($r['status']==='pending'?'badge-warning':'badge-danger'); ?>"><?php echo $r['status']; ?></span></td>
                <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($r['created_at'])); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
