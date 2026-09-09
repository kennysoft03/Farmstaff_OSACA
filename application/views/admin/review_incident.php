<div style="max-width:800px;">
  <div style="margin-bottom:16px;"><a href="<?php echo site_url('admin/incidents'); ?>" class="btn btn-outline btn-sm">← All Incidents</a></div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <!-- Incident Details -->
    <div class="card">
      <div class="card-header" style="border-left:4px solid var(--danger);"><h5 style="color:var(--danger);">⚠️ Incident Details</h5></div>
      <div class="card-body" style="font-size:.9rem;">
        <?php $info=['Worker'=>htmlspecialchars($incident['worker_name']??'—'),'Registry ID'=>htmlspecialchars($incident['reg_id']??'—'),'Farm'=>htmlspecialchars($incident['farm_name']??'—'),'Type'=>ucfirst($incident['incident_type']),'Date'=>date('d M Y',strtotime($incident['incident_date'])),'Severity'=>ucfirst($incident['severity']),'Status'=>ucfirst($incident['status']),'Reported'=>date('d M Y',strtotime($incident['created_at']))];
        foreach($info as $k=>$v): ?>
          <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gray-200);">
            <span style="color:var(--gray-600);"><?php echo $k; ?></span>
            <span style="font-weight:600;"><?php echo $v; ?></span>
          </div>
        <?php endforeach; ?>

        <div style="margin-top:16px;">
          <div style="font-weight:600;margin-bottom:6px;">Description:</div>
          <p style="color:var(--gray-700);line-height:1.7;margin:0;font-size:.88rem;"><?php echo nl2br(htmlspecialchars($incident['description'])); ?></p>
        </div>

        <?php if (!empty($incident['evidence_file'])): ?>
          <div style="margin-top:12px;">
            <div style="font-weight:600;margin-bottom:6px;">Evidence File:</div>
            <a href="<?php echo base_url($incident['evidence_file']); ?>" target="_blank" class="btn btn-outline btn-sm">📎 View Evidence</a>
          </div>
        <?php endif; ?>

        <?php if (!empty($incident['admin_notes'])): ?>
          <div style="margin-top:12px;padding:12px;background:var(--gray-100);border-radius:8px;">
            <div style="font-weight:600;margin-bottom:4px;">Previous Admin Notes:</div>
            <p style="margin:0;font-size:.85rem;color:var(--gray-700);"><?php echo htmlspecialchars($incident['admin_notes']); ?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Review Form -->
    <div class="card">
      <div class="card-header"><h5>📋 Review Decision</h5></div>
      <div class="card-body">
        <div class="alert alert-info" style="font-size:.85rem;">
          <strong>ℹ️</strong> If you <strong>Accept</strong> this incident, you can assign disciplinary points which will reduce the worker's Trust Score. Rejected reports have no effect on the worker.
        </div>

        <form method="post" action="<?php echo site_url('admin/incident/'.$incident['id'].'/review'); ?>">
          <div class="form-group">
            <label class="form-label">Decision <span style="color:var(--danger);">*</span></label>
            <select name="status" class="form-control" required id="review-status">
              <option value="">— Select Decision —</option>
              <option value="reviewed">Reviewed (no action)</option>
              <option value="accepted">Accept — Apply to Record</option>
              <option value="rejected">Reject — No Record Impact</option>
            </select>
          </div>

          <div id="points-section" style="display:none;">
            <div class="form-group">
              <label class="form-label">Disciplinary Points (Trust Score reduction)</label>
              <input type="number" name="disciplinary_points" class="form-control" min="0" max="50" value="0" placeholder="0–50">
              <span class="form-text">Minor=5, Moderate=10, Severe=20–30</span>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Admin Notes <span style="color:var(--danger);">*</span></label>
            <textarea name="admin_notes" class="form-control" rows="5" placeholder="Provide your review notes and justification for the decision taken..." required minlength="10"></textarea>
          </div>

          <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="<?php echo site_url('admin/incidents'); ?>" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Submit Review</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('review-status') && document.getElementById('review-status').addEventListener('change', function(){
  document.getElementById('points-section').style.display = this.value === 'accepted' ? 'block' : 'none';
});
</script>
