<!-- Filters -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:16px 20px;">
    <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
      <div class="form-group" style="margin:0;flex:1;min-width:200px;">
        <label class="form-label">Search</label>
        <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($filters['search']??''); ?>" placeholder="Name, phone or registry ID...">
      </div>
      <div class="form-group" style="margin:0;min-width:140px;">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="">All Statuses</option>
          <?php foreach(['active','inactive','flagged','suspended'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo ($filters['status']===$s)?'selected':''; ?>><?php echo ucfirst($s); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:1px;">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="<?php echo site_url('admin/workers'); ?>" class="btn btn-outline">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>👥 Workers Registry <span style="font-weight:400;color:var(--gray-600);font-size:.88rem;">(<?php echo number_format($total); ?> records)</span></h5>
  </div>
  <div class="table-responsive">
    <?php if (empty($workers)): ?>
      <div style="padding:40px;text-align:center;color:var(--gray-600);">
        <div style="font-size:3rem;margin-bottom:10px;">👥</div>
        <p>No workers match your search.</p>
      </div>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr><th>Worker</th><th>Registry ID</th><th>Phone</th><th>Employer</th><th>Trust Score</th><th>Status</th><th>Registered</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($workers as $w): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:50%;background:var(--green-pale);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--green-dark);overflow:hidden;flex-shrink:0;font-size:.8rem;">
                  <?php if (!empty($w['photo'])): ?><img src="<?php echo base_url($w['photo']); ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: echo strtoupper(substr($w['firstname'],0,1)); endif; ?>
                </div>
                <div>
                  <div style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?></div>
                  <div style="font-size:.75rem;color:var(--gray-600);"><?php echo htmlspecialchars($w['lga']??''); ?></div>
                </div>
              </div>
            </td>
            <td><span class="worker-id-pill"><?php echo htmlspecialchars($w['worker_id']); ?></span></td>
            <td style="font-size:.88rem;"><?php echo htmlspecialchars($w['phone']); ?></td>
            <td style="font-size:.85rem;"><?php echo htmlspecialchars($w['employer_name']??'—'); ?></td>
            <td>
              <?php $ts=(float)$w['trust_score']; $tc=$ts>=70?'var(--green-dark)':($ts>=40?'var(--gold)':'var(--danger)'); ?>
              <span style="font-weight:700;color:<?php echo $tc; ?>;"><?php echo number_format($ts,0); ?>/100</span>
            </td>
            <td><span class="badge <?php echo ['active'=>'badge-success','inactive'=>'badge-secondary','flagged'=>'badge-warning','suspended'=>'badge-danger'][$w['status']]??'badge-secondary'; ?>"><?php echo $w['status']; ?></span></td>
            <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($w['created_at'])); ?></td>
            <td><a href="<?php echo site_url('admin/worker/'.$w['id']); ?>" class="btn btn-outline btn-sm">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <?php if ($total > $limit): ?>
  <div class="card-footer">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <span style="font-size:.85rem;color:var(--gray-600);">Showing <?php echo min(($page-1)*$limit+1,$total); ?>–<?php echo min($page*$limit,$total); ?> of <?php echo number_format($total); ?></span>
      <div class="pagination">
        <?php $base=site_url('admin/workers?page='); $qs='&search='.urlencode($filters['search']??'').'&status='.urlencode($filters['status']??''); $pages=ceil($total/$limit);
        for($p=1;$p<=$pages;$p++): ?><a href="<?php echo $base.$p.$qs; ?>" class="page-btn <?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
