<!-- Filters -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:14px 20px;">
    <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
      <div class="form-group" style="margin:0;min-width:160px;">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="">All Statuses</option>
          <?php foreach(['pending','reviewed','accepted','rejected','appealed'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo ($filters['status']===$s)?'selected':''; ?> <?php echo $s==='pending'?'style="font-weight:700;color:var(--danger);"':''; ?>><?php echo ucfirst($s); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="align-self:flex-end;padding-bottom:1px;display:flex;gap:8px;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="<?php echo site_url('admin/incidents'); ?>" class="btn btn-outline">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>⚠️ Incident Reports <span style="font-weight:400;color:var(--gray-600);font-size:.88rem;">(<?php echo number_format($total); ?>)</span></h5>
  </div>
  <div class="table-responsive">
    <?php if (empty($incidents)): ?>
      <div style="padding:40px;text-align:center;color:var(--gray-600);">
        <div style="font-size:3rem;margin-bottom:10px;">✅</div>
        <p>No incidents found.</p>
      </div>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Worker</th><th>Farm</th><th>Type</th><th>Date</th><th>Severity</th><th>Status</th><th>Reported</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach ($incidents as $i): ?>
          <tr>
            <td>
              <div style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($i['worker_name']??'—'); ?></div>
              <div style="font-size:.75rem;color:var(--gray-600);"><?php echo htmlspecialchars($i['reg_id']??''); ?></div>
            </td>
            <td style="font-size:.88rem;"><?php echo htmlspecialchars($i['farm_name']??'—'); ?></td>
            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($i['incident_type']); ?></span></td>
            <td style="font-size:.82rem;"><?php echo date('d M Y',strtotime($i['incident_date'])); ?></td>
            <td><span class="badge <?php echo $i['severity']==='severe'?'badge-danger':($i['severity']==='moderate'?'badge-warning':'badge-info'); ?>"><?php echo $i['severity']; ?></span></td>
            <td><span class="badge <?php echo ['pending'=>'badge-warning','accepted'=>'badge-danger','rejected'=>'badge-success','reviewed'=>'badge-info','appealed'=>'badge-secondary'][$i['status']]??'badge-secondary'; ?>"><?php echo $i['status']; ?></span></td>
            <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($i['created_at'])); ?></td>
            <td style="white-space:nowrap;">
              <a href="<?php echo site_url('admin/incident/'.$i['id']); ?>" class="btn btn-outline btn-sm">View</a>
              <?php if ($i['status']==='pending'): ?><a href="<?php echo site_url('admin/incident/'.$i['id'].'/review'); ?>" class="btn btn-sm btn-outline" style="color:var(--danger);border-color:var(--danger);">Review</a><?php endif; ?>
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
      <?php $base=site_url('admin/incidents?page='); $qs='&status='.urlencode($filters['status']??''); $pages=ceil($total/$limit);
      for($p=1;$p<=$pages;$p++): ?><a href="<?php echo $base.$p.$qs; ?>" class="page-btn <?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
