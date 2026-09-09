<div class="row">
  <div class="col-xl-8">
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-gear"></i></span>
          <h3 class="kt-portlet__head-title">My Farm Profile</h3>
        </div>
      </div>
      <div class="kt-portlet__body">
        <form method="post" action="<?php echo site_url('dashboard/profile'); ?>" enctype="multipart/form-data">

          <!-- Logo -->
          <div style="text-align:center;margin-bottom:24px;">
            <div style="width:80px;height:80px;border-radius:50%;background:#f7f8fa;border:3px solid #e2e5ec;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 10px;overflow:hidden;" id="logo-preview">
              <?php if(!empty($employer['logo'])): ?><img src="<?php echo base_url($employer['logo']); ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>🏡<?php endif; ?>
            </div>
            <label style="cursor:pointer;"><span class="btn btn-label-brand btn-sm btn-bold"><i class="la la-camera"></i> Change Logo</span>
              <input type="file" name="logo" accept="image/*" class="photo-input" data-preview="logo-preview" style="display:none;">
            </label>
          </div>

          <div class="form-group row">
            <div class="col-md-6"><label class="col-form-label">Farm / Business Name</label><input type="text" name="farm_name" class="form-control" value="<?php echo htmlspecialchars($employer['farm_name']); ?>" required></div>
            <div class="col-md-6"><label class="col-form-label">Contact Person</label><input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($employer['contact_person']); ?>" required></div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label class="col-form-label">Farm Type</label>
              <select name="farm_type" class="form-control">
                <?php foreach(['Crop Farming','Livestock Farming','Poultry Farming','Fish Farming','Mixed Farming','Plantation','Agro-processing','Other'] as $t): ?>
                  <option value="<?php echo $t; ?>" <?php echo $employer['farm_type']===$t?'selected':''; ?>><?php echo $t; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6"><label class="col-form-label">Farm Size</label><input type="text" name="farm_size" class="form-control" value="<?php echo htmlspecialchars($employer['farm_size']??''); ?>" placeholder="e.g. 50 hectares"></div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label class="col-form-label">LGA</label>
              <select name="lga" class="form-control">
                <?php foreach($lgas as $l): ?><option value="<?php echo $l; ?>" <?php echo $employer['lga']===$l?'selected':''; ?>><?php echo $l; ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6"><label class="col-form-label">Business Reg. Number</label><input type="text" name="reg_number" class="form-control" value="<?php echo htmlspecialchars($employer['reg_number']??''); ?>"></div>
          </div>
          <div class="form-group"><label class="col-form-label">Address</label><textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($employer['address']??''); ?></textarea></div>

          <div class="kt-separator kt-separator--dashed"></div>
          <h5 style="margin:18px 0 14px;"><i class="flaticon2-lock-1 kt-font-brand"></i> Change Password <small style="color:#a2a3b7;font-size:.8rem;">(leave blank to keep current)</small></h5>
          <div class="form-group row">
            <div class="col-md-6"><label class="col-form-label">New Password</label><input type="password" name="new_password" class="form-control" placeholder="Min 8 characters" minlength="8"></div>
            <div class="col-md-6"><label class="col-form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password"></div>
          </div>

          <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:16px;border-top:1px solid #ebedf2;">
            <button type="submit" class="btn btn-success btn-bold"><i class="la la-check"></i> Save Profile</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Profile summary card -->
  <div class="col-xl-4">
    <div class="kt-portlet">
      <div class="kt-portlet__body kt-portlet__body--center" style="text-align:center;padding:28px;">
        <div style="width:72px;height:72px;border-radius:50%;background:#f7f8fa;border:3px solid #e2e5ec;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 12px;overflow:hidden;">
          <?php if(!empty($employer['logo'])): ?><img src="<?php echo base_url($employer['logo']); ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>🏡<?php endif; ?>
        </div>
        <h5 style="margin:0 0 4px;"><?php echo htmlspecialchars($employer['farm_name']); ?></h5>
        <span class="kt-badge kt-badge--<?php echo $employer['status']==='active'?'success':'warning'; ?> kt-badge--inline kt-badge--pill"><?php echo ucfirst($employer['status']); ?></span>
        <div style="margin-top:16px;font-size:.85rem;">
          <div style="padding:6px 0;border-bottom:1px solid #f7f8fa;"><span style="color:#a2a3b7;">Email: </span><strong><?php echo htmlspecialchars($employer['email']); ?></strong></div>
          <div style="padding:6px 0;border-bottom:1px solid #f7f8fa;"><span style="color:#a2a3b7;">Phone: </span><strong><?php echo htmlspecialchars($employer['phone']); ?></strong></div>
          <div style="padding:6px 0;"><span style="color:#a2a3b7;">Reputation: </span>
            <strong class="kt-font-warning"><?php echo number_format((float)$employer['reputation_score'],1); ?>/5</strong>
            <span style="color:#a2a3b7;font-size:.78rem;">(<?php echo $employer['total_ratings']; ?> ratings)</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
