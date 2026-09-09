<!-- Filters -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:14px 20px;">
    <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
      <div class="form-group" style="margin:0;min-width:160px;">
        <label class="form-label">Module</label>
        <select name="module" class="form-control">
          <option value="">All Modules</option>
          <?php foreach(['auth','workers','employers','incidents','ratings','reports'] as $m): ?>
            <option value="<?php echo $m; ?>" <?php echo ($filters['module']===$m)?'selected':''; ?>><?php echo ucfirst($m); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin:0;min-width:140px;">
        <label class="form-label">Actor Type</label>
        <select name="actor_type" class="form-control">
          <option value="">All</option>
          <option value="admin"    <?php echo $filters['actor_type']==='admin'   ?'selected':''; ?>>Admin</option>
          <option value="employer" <?php echo $filters['actor_type']==='employer'?'selected':''; ?>>Employer</option>
          <option value="system"   <?php echo $filters['actor_type']==='system'  ?'selected':''; ?>>System</option>
        </select>
      </div>
      <div style="align-self:flex-end;padding-bottom:1px;display:flex;gap:8px;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="<?php echo site_url('admin/audit'); ?>" class="btn btn-outline">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><h5>🔍 Audit Trail</h5><span style="font-size:.82rem;color:var(--gray-600);">All system activity</span></div>
  <div class="table-responsive">
    <?php if (empty($logs)): ?>
      <div style="padding:40px;text-align:center;color:var(--gray-600);">No audit logs found.</div>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Date & Time</th><th>Actor</th><th>Type</th><th>Action</th><th>Module</th><th>Description</th><th>IP</th></tr></thead>
        <tbody>
          <?php foreach ($logs as $l): ?>
          <tr>
            <td style="font-size:.8rem;white-space:nowrap;"><?php echo date('d M Y<br>g:i A',strtotime($l['created_at'])); ?></td>
            <td style="font-size:.85rem;font-weight:600;"><?php echo htmlspecialchars($l['actor_name']??'—'); ?></td>
            <td><span class="badge <?php echo $l['actor_type']==='admin'?'badge-green':($l['actor_type']==='employer'?'badge-info':'badge-secondary'); ?>"><?php echo $l['actor_type']; ?></span></td>
            <td style="font-size:.82rem;font-weight:600;"><?php echo htmlspecialchars(str_replace('_',' ',$l['action'])); ?></td>
            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($l['module']); ?></span></td>
            <td style="font-size:.8rem;max-width:250px;"><?php echo htmlspecialchars(substr($l['description']??'',0,80)); ?></td>
            <td style="font-size:.75rem;color:var(--gray-600);"><?php echo htmlspecialchars($l['ip_address']??'—'); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <?php if (count($logs) >= 50): ?>
    <div class="card-footer">
      <div class="pagination">
        <a href="?page=<?php echo $page-1; ?>&module=<?php echo urlencode($filters['module']??''); ?>&actor_type=<?php echo urlencode($filters['actor_type']??''); ?>" class="page-btn <?php echo $page<=1?'disabled':''; ?>">← Prev</a>
        <span class="page-btn active"><?php echo $page; ?></span>
        <a href="?page=<?php echo $page+1; ?>&module=<?php echo urlencode($filters['module']??''); ?>&actor_type=<?php echo urlencode($filters['actor_type']??''); ?>" class="page-btn">Next →</a>
      </div>
    </div>
  <?php endif; ?>
</div>
