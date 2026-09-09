<div style="min-height:80vh;padding:48px 20px;background:var(--gray-100);">
  <div style="max-width:720px;margin:0 auto;">

    <div style="text-align:center;margin-bottom:32px;">
      <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Logo" style="height:56px;margin-bottom:12px;">
      <h2 style="margin:0 0 6px;">Register as an Employer</h2>
      <p style="color:var(--gray-600);font-size:.92rem;">Create your Farm Staff Registry employer account</p>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>🏚️ Farm / Business Information</h5>
      </div>
      <div class="card-body">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo site_url('register'); ?>">

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="farm_name">Farm / Business Name <span style="color:var(--danger);">*</span></label>
              <input type="text" id="farm_name" name="farm_name" class="form-control"
                value="<?php echo htmlspecialchars($this->input->post('farm_name') ?? ''); ?>"
                placeholder="e.g. Greenfields Farms" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="contact_person">Contact Person <span style="color:var(--danger);">*</span></label>
              <input type="text" id="contact_person" name="contact_person" class="form-control"
                value="<?php echo htmlspecialchars($this->input->post('contact_person') ?? ''); ?>"
                placeholder="Full name of contact person" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="email">Email Address <span style="color:var(--danger);">*</span></label>
              <input type="email" id="email" name="email" class="form-control"
                value="<?php echo htmlspecialchars($this->input->post('email') ?? ''); ?>"
                placeholder="your@email.com" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="phone">Phone Number <span style="color:var(--danger);">*</span></label>
              <input type="tel" id="phone" name="phone" class="form-control"
                value="<?php echo htmlspecialchars($this->input->post('phone') ?? ''); ?>"
                placeholder="e.g. 08012345678" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="farm_type">Farm Type <span style="color:var(--danger);">*</span></label>
              <select id="farm_type" name="farm_type" class="form-control" required>
                <option value="">— Select Farm Type —</option>
                <?php foreach(['Crop Farming','Livestock Farming','Poultry Farming','Fish Farming','Mixed Farming','Plantation','Agro-processing','Other'] as $t): ?>
                  <option value="<?php echo $t; ?>" <?php echo ($this->input->post('farm_type')===$t)?'selected':''; ?>><?php echo $t; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="farm_size">Farm Size</label>
              <input type="text" id="farm_size" name="farm_size" class="form-control"
                value="<?php echo htmlspecialchars($this->input->post('farm_size') ?? ''); ?>"
                placeholder="e.g. 50 hectares">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="lga">LGA <span style="color:var(--danger);">*</span></label>
              <select id="lga" name="lga" class="form-control" required>
                <option value="">— Select LGA —</option>
                <?php foreach(['Akoko North-East','Akoko North-West','Akoko South-East','Akoko South-West','Akure North','Akure South','Ese Odo','Idanre','Ifedore','Ilaje','Ile Oluji/Okeigbo','Irele','Odigbo','Okitipupa','Ondo East','Ondo West','Ose','Owo'] as $l): ?>
                  <option value="<?php echo $l; ?>" <?php echo ($this->input->post('lga')===$l)?'selected':''; ?>><?php echo $l; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="reg_number">Business Reg. Number</label>
              <input type="text" id="reg_number" name="reg_number" class="form-control"
                value="<?php echo htmlspecialchars($this->input->post('reg_number') ?? ''); ?>"
                placeholder="CAC / RC Number (optional)">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="address">Farm Address</label>
            <textarea id="address" name="address" class="form-control" rows="2"
              placeholder="Full farm/business address"><?php echo htmlspecialchars($this->input->post('address') ?? ''); ?></textarea>
          </div>

          <hr style="margin:24px 0;border-color:var(--gray-200);">
          <h6 style="margin-bottom:16px;">🔐 Account Security</h6>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="password">Password <span style="color:var(--danger);">*</span></label>
              <input type="password" id="password" name="password" class="form-control"
                placeholder="Minimum 8 characters" required minlength="8">
              <span class="form-text">Must be at least 8 characters long.</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="confirm_password">Confirm Password <span style="color:var(--danger);">*</span></label>
              <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                placeholder="Re-enter your password" required>
            </div>
          </div>

          <div class="form-group" style="margin-top:8px;">
            <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:.88rem;">
              <input type="checkbox" name="agree" value="1" required style="margin-top:2px;width:16px;height:16px;">
              <span>I agree to the <a href="<?php echo site_url('about'); ?>#terms" target="_blank" style="color:var(--green-dark);">Terms of Use</a>
              and <a href="<?php echo site_url('about'); ?>#privacy" target="_blank" style="color:var(--green-dark);">Privacy Policy</a> of the Farm Staff Registry.</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
            Create Employer Account
          </button>
        </form>

        <p style="text-align:center;font-size:.88rem;color:var(--gray-600);margin:16px 0 0;">
          Already have an account? <a href="<?php echo site_url('login'); ?>" style="color:var(--green-dark);font-weight:600;">Sign In</a>
        </p>
      </div>
    </div>
  </div>
</div>
