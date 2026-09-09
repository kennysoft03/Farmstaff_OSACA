<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;background:var(--gray-100);">
  <div style="width:100%;max-width:440px;">

    <div style="text-align:center;margin-bottom:32px;">
      <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Logo" style="height:56px;margin-bottom:12px;">
      <h2 style="margin:0 0 6px;">Welcome Back</h2>
      <p style="color:var(--gray-600);font-size:.92rem;margin:0;">Sign in to your employer account</p>
    </div>

    <div class="card">
      <div class="card-body">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo site_url('login'); ?>">
          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control"
              value="<?php echo htmlspecialchars($this->input->post('email') ?? ''); ?>"
              placeholder="your@email.com" required autofocus>
          </div>
          <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control"
              placeholder="Enter your password" required>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
          </div>
        </form>

        <p style="text-align:center;font-size:.88rem;color:var(--gray-600);margin:0;">
          Don't have an account? <a href="<?php echo site_url('register'); ?>" style="color:var(--green-dark);font-weight:600;">Register Now</a>
        </p>
      </div>
    </div>

    <p style="text-align:center;font-size:.78rem;color:var(--gray-600);margin-top:20px;">
      Administrator? <a href="<?php echo site_url('admin'); ?>">Admin Login →</a>
    </p>
  </div>
</div>
