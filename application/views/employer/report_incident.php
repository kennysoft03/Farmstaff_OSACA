<div class="row">
  <div class="col-xl-7">
    <div style="margin-bottom:16px;">
      <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-secondary btn-sm btn-bold"><i class="la la-arrow-left"></i> Back to Profile</a>
    </div>

    <div class="alert alert-warning alert-dismissible fade show">
      <div class="alert-icon"><i class="flaticon-warning"></i></div>
      <div class="alert-text"><strong>Important:</strong> Incident reports are reviewed by platform administrators before they affect a worker's record. Please ensure all information is accurate and truthful.</div>
      <div class="alert-close"><button class="close" data-dismiss="alert"><span><i class="la la-close"></i></span></button></div>
    </div>

    <div class="kt-portlet">
      <div class="kt-portlet__head" style="border-left:4px solid #fd3995;">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-danger flaticon-warning-sign"></i></span>
          <h3 class="kt-portlet__head-title">Report Incident — <?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h3>
        </div>
      </div>
      <div class="kt-portlet__body">
        <form method="post" action="<?php echo site_url('dashboard/worker/'.$worker['id'].'/incident'); ?>" enctype="multipart/form-data">
          <div class="form-group row">
            <div class="col-md-6">
              <label class="col-form-label">Incident Type <span class="kt-font-danger">*</span></label>
              <select name="incident_type" class="form-control no-select2" required>
                <option value="">— Select Type —</option>
                <?php foreach(['theft','misconduct','assault','insubordination','negligence','fraud','other'] as $t): ?>
                  <option value="<?php echo $t; ?>"><?php echo ucfirst($t); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="col-form-label">Severity <span class="kt-font-danger">*</span></label>
              <select name="severity" class="form-control no-select2" required>
                <option value="">— Select Severity —</option>
                <option value="minor">Minor — No lasting impact</option>
                <option value="moderate">Moderate — Significant concern</option>
                <option value="severe">Severe — Serious misconduct</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-form-label">Incident Date <span class="kt-font-danger">*</span></label>
            <input type="date" name="incident_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label class="col-form-label">Description <span class="kt-font-danger">*</span></label>
            <textarea name="description" class="form-control" rows="6" placeholder="Provide a detailed, factual description of what happened. Include dates, times, witnesses and any other relevant information. Minimum 30 characters." required minlength="30"></textarea>
          </div>
          <div class="form-group">
            <label class="col-form-label">Supporting Evidence</label>
            <input type="file" name="evidence_file" class="form-control" accept="image/*,.pdf,.doc,.docx">
            <span class="form-text text-muted">Upload a photo, document or PDF as evidence (max 5MB)</span>
          </div>
          <div class="kt-portlet__foot" style="display:flex;gap:10px;justify-content:flex-end;padding:0;border:none;margin-top:16px;">
            <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-secondary btn-bold">Cancel</a>
            <button type="submit" class="btn btn-danger btn-bold"><i class="la la-exclamation-triangle"></i> Submit Incident Report</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
