<div style="max-width:700px;">
  <div style="margin-bottom:16px;display:flex;gap:10px;">
    <a href="<?php echo site_url('admin/incidents'); ?>" class="btn btn-outline btn-sm">← Incidents</a>
    <?php if ($incident['status']==='pending'): ?>
      <a href="<?php echo site_url('admin/incident/'.$incident['id'].'/review'); ?>" class="btn btn-primary btn-sm">Review This Incident</a>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-header" style="border-left:4px solid var(--danger);"><h5>⚠️ Incident Report #<?php echo $incident['id']; ?></h5></div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
        <?php $meta=['Worker'=>$incident['worker_name']??'—','Registry ID'=>$incident['reg_id']??'—','Farm'=>$incident['farm_name']??'—','Incident Type'=>ucfirst($incident['incident_type']),'Incident Date'=>date('d M Y',strtotime($incident['incident_date'])),'Severity'=>ucfirst($incident['severity']),'Status'=>ucfirst($incident['status']),'Reported On'=>date('d M Y g:i A',strtotime($incident['created_at']))];
        foreach($meta as $k=>$v): ?>
          <div style="padding:10px;background:var(--gray-100);border-radius:8px;">
            <div style="font-size:.72rem;color:var(--gray-600);margin-bottom:2px;"><?php echo $k; ?></div>
            <div style="font-weight:700;font-size:.88rem;"><?php echo htmlspecialchars($v); ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-bottom:16px;">
        <h6>Description</h6>
        <p style="color:var(--gray-700);line-height:1.8;font-size:.92rem;"><?php echo nl2br(htmlspecialchars($incident['description'])); ?></p>
      </div>

      <?php if (!empty($incident['evidence_file'])): ?>
        <div style="margin-bottom:16px;"><h6>Evidence</h6><a href="<?php echo base_url($incident['evidence_file']); ?>" target="_blank" class="btn btn-outline">📎 Open Evidence File</a></div>
      <?php endif; ?>

      <?php if (!empty($incident['admin_notes'])): ?>
        <div style="background:var(--green-pale);border-radius:8px;padding:16px;">
          <h6 style="color:var(--green-dark);margin-bottom:6px;">Admin Review Notes</h6>
          <p style="margin:0;font-size:.9rem;"><?php echo nl2br(htmlspecialchars($incident['admin_notes'])); ?></p>
          <?php if (!empty($incident['reviewed_by_name'])): ?><div style="font-size:.75rem;color:var(--gray-600);margin-top:8px;">— <?php echo htmlspecialchars($incident['reviewed_by_name']); ?>, <?php echo date('d M Y',strtotime($incident['reviewed_at']??'')); ?></div><?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
