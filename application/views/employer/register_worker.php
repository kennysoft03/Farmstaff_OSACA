<div class="row">
  <div class="col-xl-9 col-lg-10">
    <div style="margin-bottom:16px;">
      <a href="<?php echo site_url('dashboard/workers'); ?>" class="btn btn-secondary btn-sm btn-bold"><i class="la la-arrow-left"></i> Back to Workers</a>
    </div>

    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-user"></i></span>
          <h3 class="kt-portlet__head-title">Register New Worker</h3>
        </div>
      </div>
      <div class="kt-portlet__body">
        <form method="post" action="<?php echo site_url('dashboard/register-worker'); ?>" enctype="multipart/form-data">

          <!-- Photo -->
          <div style="text-align:center;margin-bottom:28px;">
            <div style="width:96px;height:96px;border-radius:50%;background:#f7f8fa;border:3px solid #e2e5ec;margin:0 auto 12px;overflow:hidden;display:flex;align-items:center;justify-content:center;">
              <img id="photo-preview"
                   src="<?php echo base_url('assets/images/avatar-placeholder.svg'); ?>"
                   alt="Photo Preview"
                   style="width:100%;height:100%;object-fit:cover;">
              <div style="display:none;width:100%;height:100%;align-items:center;justify-content:center;font-size:2.5rem;">👤</div>
            </div>
            <label style="cursor:pointer;">
              <span class="btn btn-label-brand btn-sm btn-bold"><i class="la la-camera"></i> Upload Photo</span>
              <input type="file" id="worker-photo-input" name="photo" accept="image/jpeg,image/png,image/gif" style="display:none;">
            </label>
            <div class="form-text text-muted" style="font-size:.78rem;margin-top:4px;">JPEG/PNG, max 2MB</div>
          </div>

          <div class="kt-section__title" style="font-size:.9rem;font-weight:700;color:#595d6e;border-bottom:1px solid #ebedf2;padding-bottom:8px;margin-bottom:18px;">Personal Information</div>

          <div class="form-group row">
            <div class="col-md-6">
              <label class="col-form-label">First Name <span class="kt-font-danger">*</span></label>
              <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($this->input->post('firstname')??''); ?>" required>
            </div>
            <div class="col-md-6">
              <label class="col-form-label">Last Name <span class="kt-font-danger">*</span></label>
              <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($this->input->post('lastname')??''); ?>" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-4">
              <label class="col-form-label">Other Name</label>
              <input type="text" name="othername" class="form-control" value="<?php echo htmlspecialchars($this->input->post('othername')??''); ?>">
            </div>
            <div class="col-md-4">
              <label class="col-form-label">Gender <span class="kt-font-danger">*</span></label>
              <select name="gender" class="form-control no-select2" required>
                <option value="">— Select —</option>
                <option value="male"   <?php echo $this->input->post('gender')==='male'  ?'selected':''; ?>>Male</option>
                <option value="female" <?php echo $this->input->post('gender')==='female'?'selected':''; ?>>Female</option>
                <option value="other"  <?php echo $this->input->post('gender')==='other' ?'selected':''; ?>>Other</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="col-form-label">Date of Birth</label>
              <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($this->input->post('dob')??''); ?>">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-4">
              <label class="col-form-label">Phone Number <span class="kt-font-danger">*</span></label>
              <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($this->input->post('phone')??''); ?>" placeholder="08012345678" required>
            </div>
            <div class="col-md-4">
              <label class="col-form-label">Alternate Phone</label>
              <input type="tel" name="alt_phone" class="form-control" value="<?php echo htmlspecialchars($this->input->post('alt_phone')??''); ?>">
            </div>
            <div class="col-md-4">
              <label class="col-form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($this->input->post('email')??''); ?>">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label class="col-form-label">LGA</label>
              <select name="lga" class="form-control">
                <option value="">— Select LGA —</option>
                <?php foreach($lgas as $l): ?>
                  <option value="<?php echo $l; ?>" <?php echo ($this->input->post('lga')===$l)?'selected':''; ?>><?php echo $l; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="col-form-label">Address</label>
              <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($this->input->post('address')??''); ?>" placeholder="Residential address">
            </div>
          </div>

          <div class="kt-section__title" style="font-size:.9rem;font-weight:700;color:#595d6e;border-bottom:1px solid #ebedf2;padding-bottom:8px;margin:24px 0 18px;">Identity Verification</div>

          <div class="form-group row">
            <div class="col-md-4">
              <label class="col-form-label">ID Type</label>
              <select name="id_type" class="form-control no-select2">
                <option value="">— Select —</option>
                <?php foreach(['NIN','Voters Card','Drivers License','International Passport','Staff ID'] as $t): ?>
                  <option value="<?php echo $t; ?>" <?php echo ($this->input->post('id_type')===$t)?'selected':''; ?>><?php echo $t; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="col-form-label">ID Number</label>
              <input type="text" name="id_number" class="form-control" value="<?php echo htmlspecialchars($this->input->post('id_number')??''); ?>">
            </div>
            <div class="col-md-4">
              <label class="col-form-label">ID Document <span style="font-size:.75rem;color:#a2a3b7;">(scan/photo)</span></label>
              <input type="file" name="id_document" class="form-control" accept="image/*,.pdf">
            </div>
          </div>

          <div class="kt-section__title" style="font-size:.9rem;font-weight:700;color:#595d6e;border-bottom:1px solid #ebedf2;padding-bottom:8px;margin:24px 0 18px;">Employment Details</div>

          <div class="form-group row">
            <div class="col-md-6">
              <label class="col-form-label">Role / Position <span class="kt-font-danger">*</span></label>
              <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($this->input->post('role')??''); ?>" placeholder="e.g. Tractor Operator, Farm Hand" required>
            </div>
            <div class="col-md-6">
              <label class="col-form-label">Start Date <span class="kt-font-danger">*</span></label>
              <input type="date" name="start_date" class="form-control" value="<?php echo $this->input->post('start_date')?:date('Y-m-d'); ?>" required>
            </div>
          </div>

          <div class="kt-section__title" style="font-size:.9rem;font-weight:700;color:#595d6e;border-bottom:1px solid #ebedf2;padding-bottom:8px;margin:24px 0 18px;">Skills <span style="font-weight:400;color:#a2a3b7;">(optional)</span></div>

          <div class="form-group">
            <label class="col-form-label">Add Skills</label>
            <div style="display:flex;gap:8px;margin-bottom:8px;">
              <input type="text" id="skill-input" class="form-control" placeholder="Type a skill and press Enter (e.g. Tractor Driving)">
              <button type="button" id="add-skill-btn" class="btn btn-label-brand btn-bold" style="white-space:nowrap;">Add</button>
            </div>
            <div id="skill-tags" style="display:flex;flex-wrap:wrap;gap:4px;min-height:30px;"></div>
            <!-- Common skills reference -->
            <div style="margin-top:10px;">
              <span style="font-size:.78rem;color:#a2a3b7;">Quick add:</span>
              <?php foreach(['Tractor Driving','Harvesting','Spraying','Land Preparation','Crop Planting','Livestock Care','Irrigation','Fertilizer Application'] as $sk): ?>
                <span onclick="document.getElementById('skill-input').value='<?php echo $sk; ?>'" class="kt-badge kt-badge--secondary kt-badge--inline kt-badge--pill" style="cursor:pointer;margin:2px;"><?php echo $sk; ?></span>
              <?php endforeach; ?>
            </div>
          </div>

          <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:16px;border-top:1px solid #ebedf2;margin-top:8px;">
            <a href="<?php echo site_url('dashboard/workers'); ?>" class="btn btn-secondary btn-bold">Cancel</a>
            <button type="submit" class="btn btn-success btn-bold"><i class="la la-check"></i> Register Worker</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Photo preview — inline so it runs before footer bundle
document.getElementById('worker-photo-input').addEventListener('change', function () {
    var file = this.files && this.files[0];
    if (!file) return;

    // Validate type
    var allowed = ['image/jpeg','image/jpg','image/png','image/gif'];
    if (allowed.indexOf(file.type) === -1) {
        alert('Please select a JPEG or PNG image.');
        this.value = '';
        return;
    }
    // Validate size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image must be smaller than 2MB.');
        this.value = '';
        return;
    }

    var reader = new FileReader();
    reader.onload = function (e) {
        var img = document.getElementById('photo-preview');
        img.src = e.target.result;
        img.style.display = 'block';
        // Hide the fallback emoji div if visible
        if (img.nextElementSibling) img.nextElementSibling.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
