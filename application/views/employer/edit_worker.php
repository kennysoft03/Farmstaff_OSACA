<div style="max-width:820px;">
  <div style="margin-bottom:16px;">
    <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-outline btn-sm">← Back to Profile</a>
  </div>

  <div class="card">
    <div class="card-header"><h5>✏️ Edit Worker: <?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h5></div>
    <div class="card-body">
      <form method="post" action="<?php echo site_url('dashboard/worker/'.$worker['id'].'/edit'); ?>" enctype="multipart/form-data">
        <div style="text-align:center;margin-bottom:24px;">
          <div style="width:80px;height:80px;border-radius:50%;background:var(--green-pale);border:3px solid var(--green-light);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 10px;overflow:hidden;" id="photo-preview">
            <?php if (!empty($worker['photo'])): ?><img src="<?php echo base_url($worker['photo']); ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>👤<?php endif; ?>
          </div>
          <label style="cursor:pointer;"><span class="btn btn-outline btn-sm">📷 Change Photo</span><input type="file" name="photo" accept="image/*" class="photo-input" data-preview="photo-preview" style="display:none;"></label>
        </div>

        <div class="form-row">
          <div class="form-group"><label class="form-label">First Name</label><input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($worker['firstname']); ?>" required></div>
          <div class="form-group"><label class="form-label">Last Name</label><input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($worker['lastname']); ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Other Name</label><input type="text" name="othername" class="form-control" value="<?php echo htmlspecialchars($worker['othername']??''); ?>"></div>
          <div class="form-group">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-control">
              <?php foreach(['male','female','other'] as $g): ?><option value="<?php echo $g; ?>" <?php echo $worker['gender']===$g?'selected':''; ?>><?php echo ucfirst($g); ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Date of Birth</label><input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($worker['dob']??''); ?>"></div>
          <div class="form-group"><label class="form-label">Alt Phone</label><input type="tel" name="alt_phone" class="form-control" value="<?php echo htmlspecialchars($worker['alt_phone']??''); ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($worker['email']??''); ?>"></div>
          <div class="form-group">
            <label class="form-label">LGA</label>
            <select name="lga" class="form-control">
              <option value="">— Select —</option>
              <?php foreach($lgas as $l): ?><option value="<?php echo $l; ?>" <?php echo $worker['lga']===$l?'selected':''; ?>><?php echo $l; ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($worker['address']??''); ?>"></div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">ID Type</label>
            <select name="id_type" class="form-control">
              <?php foreach(['NIN','Voters Card','Drivers License','International Passport','Staff ID'] as $t): ?><option value="<?php echo $t; ?>" <?php echo $worker['id_type']===$t?'selected':''; ?>><?php echo $t; ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label class="form-label">ID Number</label><input type="text" name="id_number" class="form-control" value="<?php echo htmlspecialchars($worker['id_number']??''); ?>"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <?php foreach(['active','inactive','flagged','suspended'] as $s): ?><option value="<?php echo $s; ?>" <?php echo $worker['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option><?php endforeach; ?>
          </select>
        </div>
        <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
          <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
