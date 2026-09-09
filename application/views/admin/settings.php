<div style="max-width:900px;">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

    <!-- Create Admin User -->
    <div class="card">
      <div class="card-header"><h5>➕ Create Admin User</h5></div>
      <div class="card-body">
        <form method="post" action="<?php echo site_url('admin/settings'); ?>">
          <input type="hidden" name="action" value="create_admin">
          <div class="form-row">
            <div class="form-group"><label class="form-label">Surname</label><input type="text" name="new_surname" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Other Names</label><input type="text" name="new_othernames" class="form-control" required></div>
          </div>
          <div class="form-group"><label class="form-label">Email</label><input type="email" name="new_email" class="form-control" required></div>
          <div class="form-group"><label class="form-label">Username</label><input type="text" name="new_username" class="form-control" required minlength="4"></div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Password</label><input type="password" name="new_password" class="form-control" required minlength="8"></div>
            <div class="form-group">
              <label class="form-label">Role</label>
              <select name="new_role" class="form-control">
                <option value="admin">Admin</option>
                <option value="moderator">Moderator</option>
                <option value="superadmin">Super Admin</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Create Admin User</button>
        </form>
      </div>
    </div>

    <!-- Existing Admins -->
    <div class="card">
      <div class="card-header"><h5>👤 Admin Users</h5></div>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Last Login</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach ($admins as $a): ?>
            <tr>
              <td style="font-weight:600;font-size:.88rem;"><?php echo htmlspecialchars($a['surname'].' '.$a['othernames']); ?></td>
              <td style="font-size:.85rem;"><?php echo htmlspecialchars($a['username']); ?></td>
              <td><span class="badge <?php echo $a['role']==='superadmin'?'badge-green':($a['role']==='admin'?'badge-info':'badge-secondary'); ?>"><?php echo $a['role']; ?></span></td>
              <td style="font-size:.78rem;color:var(--gray-600);"><?php echo $a['last_login']?date('d M Y',strtotime($a['last_login'])):'Never'; ?></td>
              <td><span class="badge <?php echo $a['status']?'badge-success':'badge-danger'; ?>"><?php echo $a['status']?'Active':'Inactive'; ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- System Info -->
  <div class="card" style="margin-top:20px;">
    <div class="card-header"><h5>⚙️ System Information</h5></div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;font-size:.88rem;">
        <div><span style="color:var(--gray-600);">Platform:</span> <strong><?php echo SITE_NAME; ?></strong></div>
        <div><span style="color:var(--gray-600);">Organisation:</span> <strong>OSACA</strong></div>
        <div><span style="color:var(--gray-600);">PHP Version:</span> <strong><?php echo phpversion(); ?></strong></div>
        <div><span style="color:var(--gray-600);">CodeIgniter:</span> <strong><?php echo CI_VERSION; ?></strong></div>
        <div><span style="color:var(--gray-600);">Base URL:</span> <strong><?php echo base_url(); ?></strong></div>
        <div><span style="color:var(--gray-600);">Server Time:</span> <strong><?php echo date('d M Y, g:i A'); ?></strong></div>
      </div>
    </div>
  </div>
</div>
