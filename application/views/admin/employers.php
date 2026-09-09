<!-- Filters -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:16px 20px;">
    <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
      <div class="form-group" style="margin:0;flex:1;min-width:200px;">
        <label class="form-label">Search</label>
        <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($filters['search']??''); ?>" placeholder="Farm name, contact, email or phone...">
      </div>
      <div class="form-group" style="margin:0;min-width:140px;">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="">All</option>
          <?php foreach(['active','pending','suspended'] as $s): ?><option value="<?php echo $s; ?>" <?php echo ($filters['status']===$s)?'selected':''; ?>><?php echo ucfirst($s); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:1px;">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="<?php echo site_url('admin/employers'); ?>" class="btn btn-outline">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>🏚️ Employers <span style="font-weight:400;color:var(--gray-600);font-size:.88rem;">(<?php echo number_format($total); ?>)</span></h5>
  </div>
  <div class="table-responsive">
    <?php if (empty($employers)): ?>
      <div style="padding:40px;text-align:center;color:var(--gray-600);"><p>No employers found.</p></div>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Farm Name</th><th>Contact</th><th>Phone / Email</th><th>LGA</th><th>Farm Type</th><th>Rating</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($employers as $e): ?>
          <tr>
            <td style="font-weight:600;"><?php echo htmlspecialchars($e['farm_name']); ?></td>
            <td style="font-size:.88rem;"><?php echo htmlspecialchars($e['contact_person']); ?></td>
            <td style="font-size:.82rem;"><?php echo htmlspecialchars($e['phone']); ?><br><span style="color:var(--gray-600);"><?php echo htmlspecialchars($e['email']); ?></span></td>
            <td style="font-size:.85rem;"><?php echo htmlspecialchars($e['lga']??'—'); ?></td>
            <td style="font-size:.82rem;"><?php echo htmlspecialchars($e['farm_type']??'—'); ?></td>
            <td>
              <?php if ($e['total_ratings'] > 0): ?>
                <span style="color:var(--gold);font-weight:700;"><?php echo number_format($e['reputation_score'],1); ?>⭐</span>
                <span style="font-size:.72rem;color:var(--gray-600);">(<?php echo $e['total_ratings']; ?>)</span>
              <?php else: ?>
                <span style="color:var(--gray-400);">—</span>
              <?php endif; ?>
            </td>
            <td><span class="badge <?php echo $e['status']==='active'?'badge-success':($e['status']==='pending'?'badge-warning':'badge-danger'); ?>"><?php echo $e['status']; ?></span></td>
            <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($e['created_at'])); ?></td>
            <td><a href="<?php echo site_url('admin/employer/'.$e['id']); ?>" class="btn btn-outline btn-sm">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <?php if ($total > $limit): ?>
  <div class="card-footer">
    <div class="pagination">
      <?php $base=site_url('admin/employers?page='); $qs='&search='.urlencode($filters['search']??'').'&status='.urlencode($filters['status']??''); $pages=ceil($total/$limit);
      for($p=1;$p<=$pages;$p++): ?><a href="<?php echo $base.$p.$qs; ?>" class="page-btn <?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
