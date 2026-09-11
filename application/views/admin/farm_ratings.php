<!-- Filter -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:14px 20px;">
    <form method="get" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
      <div class="form-group" style="margin:0;min-width:160px;">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="">All</option>
          <?php foreach(['pending','approved','rejected'] as $s): ?><option value="<?php echo $s; ?>" <?php echo $filters['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div style="align-self:flex-end;padding-bottom:1px;display:flex;gap:8px;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="<?php echo site_url('admin/farm-ratings'); ?>" class="btn btn-outline">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><h5>⭐ Farm Ratings <span style="font-weight:400;color:var(--gray-600);font-size:.88rem;">(<?php echo number_format($total); ?>)</span></h5></div>
  <div class="table-responsive">
    <?php if (empty($ratings)): ?>
      <div style="padding:40px;text-align:center;color:var(--gray-600);">✅ No ratings found.</div>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Farm</th><th>Rating</th><th>Conditions</th><th>Safety</th><th>Payment</th><th>Treatment</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach ($ratings as $r): ?>
          <tr>
            <td style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($r['farm_name']??'—'); ?></td>
            <td><span style="color:var(--gold);font-size:1.1rem;"><?php echo str_repeat('★',(int)$r['overall_rating']); ?></span><span style="font-size:.75rem;color:var(--gray-600);"> (<?php echo $r['overall_rating']; ?>/5)</span></td>
            <td style="text-align:center;"><?php echo $r['working_conditions']??'—'; ?></td>
            <td style="text-align:center;"><?php echo $r['safety']??'—'; ?></td>
            <td style="text-align:center;"><?php echo $r['payment_promptness']??'—'; ?></td>
            <td style="text-align:center;"><?php echo $r['treatment']??'—'; ?></td>
           
            <td><span class="badge <?php echo $r['status']==='approved'?'badge-success':($r['status']==='pending'?'badge-warning':'badge-danger'); ?>"><?php echo $r['status']; ?></span></td>
            <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($r['created_at'])); ?></td>
            <td>
              <?php if ($r['status']==='pending'): ?>
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <form method="post" action="<?php echo site_url('admin/farm-rating/'.$r['id'].'/review'); ?>">
                    <input type="hidden" name="status" value="approved">
                    <input type="hidden" name="admin_notes" value="Approved by administrator.">
                    <button class="btn btn-sm btn-primary" style="width:100%;">✅ Approve</button>
                  </form>
                  <form method="post" action="<?php echo site_url('admin/farm-rating/'.$r['id'].'/review'); ?>">
                    <input type="hidden" name="status" value="rejected">
                    <input type="hidden" name="admin_notes" value="Rejected by administrator.">
                    <button class="btn btn-sm btn-danger" style="width:100%;" data-confirm="Reject this rating?">❌ Reject</button>
                  </form>
                </div>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <?php if ($total > $limit): ?>
  <div class="card-footer">
    <div class="pagination">
      <?php $base=site_url('admin/farm-ratings?page='); $qs='&status='.urlencode($filters['status']??''); $pages=ceil($total/$limit);
      for($p=1;$p<=$pages;$p++): ?><a href="<?php echo $base.$p.$qs; ?>" class="page-btn <?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
