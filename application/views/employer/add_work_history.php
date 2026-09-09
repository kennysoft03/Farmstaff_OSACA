<div style="max-width:640px;">
  <div style="margin-bottom:16px;">
    <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/history'); ?>" class="btn btn-outline btn-sm">← Back to Work History</a>
  </div>
  <div class="card">
    <div class="card-header"><h5>📋 Add Work History Record</h5></div>
    <div class="card-body">
      <form method="post" action="<?php echo site_url('dashboard/worker/'.$worker['id'].'/add-history'); ?>">
        <div class="form-group">
          <label class="form-label">Role / Position <span style="color:var(--danger);">*</span></label>
          <input type="text" name="role" class="form-control" placeholder="e.g. Senior Farm Hand" required>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Start Date <span style="color:var(--danger);">*</span></label><input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
          <div class="form-group"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control"></div>
        </div>
        <div class="form-group">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox" name="is_current" value="1"> <span class="form-label" style="margin:0;">This is the worker's current position</span>
          </label>
        </div>
        <div class="form-group"><label class="form-label">Responsibilities</label><textarea name="responsibilities" class="form-control" placeholder="Key duties and responsibilities..."></textarea></div>
        <div class="form-group"><label class="form-label">Leaving Reason</label><input type="text" name="leaving_reason" class="form-control" placeholder="Reason for leaving (if ended)"></div>
        <div class="form-group"><label class="form-label">Employer Remarks</label><textarea name="employer_remarks" class="form-control" rows="3" placeholder="Overall remarks about this employment period..."></textarea></div>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
          <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/history'); ?>" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Record</button>
        </div>
      </form>
    </div>
  </div>
</div>
