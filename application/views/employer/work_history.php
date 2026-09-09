<div style="margin-bottom:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-outline btn-sm">← Back to Profile</a>
  <h5 style="margin:0;">📋 Work History: <?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h5>
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/add-history'); ?>" class="btn btn-primary btn-sm" style="margin-left:auto;">+ Add Record</a>
</div>

<div class="card">
  <div class="table-responsive">
    <?php if (empty($history)): ?>
      <div style="padding:40px;text-align:center;color:var(--gray-600);">
        <div style="font-size:2.5rem;margin-bottom:10px;">📋</div>
        <p>No work history recorded yet.</p>
        <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/add-history'); ?>" class="btn btn-primary btn-sm">Add First Record</a>
      </div>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Role</th><th>Farm</th><th>Start</th><th>End</th><th>Duration</th><th>Status</th><th>Remarks</th></tr></thead>
        <tbody>
          <?php foreach ($history as $h): ?>
          <tr>
            <td style="font-weight:600;"><?php echo htmlspecialchars($h['role']); ?></td>
            <td><?php echo htmlspecialchars($h['farm_name']??'—'); ?></td>
            <td><?php echo date('d M Y', strtotime($h['start_date'])); ?></td>
            <td><?php echo $h['is_current'] ? '<strong style="color:var(--green-dark);">Present</strong>' : (!empty($h['end_date']) ? date('d M Y', strtotime($h['end_date'])) : '—'); ?></td>
            <td style="font-size:.82rem;"><?php echo !empty($h['duration_days']) ? ceil($h['duration_days']/30).' months' : '—'; ?></td>
            <td><span class="badge <?php echo $h['status']==='active'?'badge-success':($h['status']==='completed'?'badge-secondary':'badge-danger'); ?>"><?php echo $h['status']; ?></span></td>
            <td style="font-size:.8rem;max-width:200px;"><?php echo htmlspecialchars(substr($h['employer_remarks']??'',0,80)); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
