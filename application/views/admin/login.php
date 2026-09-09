<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — <?php echo SITE_NAME; ?></title>
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#1a2640">
<link rel="icon"       type="image/png" sizes="32x32" href="<?php echo base_url('assets/images/farvicon.png'); ?>">
<link rel="shortcut icon"              href="<?php echo base_url('assets/images/farvicon.png'); ?>">
<link rel="apple-touch-icon"           href="<?php echo base_url('assets/images/farvicon.png'); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url('assets/css/farmstaff.css'); ?>">
<style>
  body {
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    background: #0d1a0f url('<?php echo base_url('assetsa/media/bg/bg-1.jpg'); ?>') no-repeat center center fixed;
    background-size: cover;
    position: relative;
  }
  /* Dark overlay on top of bg image for contrast */
  body::before {
    content: '';
    position: fixed; inset: 0; z-index: 0;
    background: rgba(0, 0, 0, .58);
  }
  .login-wrap {
    position: relative; z-index: 1;
    width: 100%; max-width: 400px;
  }
  .login-logo  { text-align: center; margin-bottom: 28px; }
  .login-logo img { height: 64px; margin-bottom: 12px; }
  .login-logo h2 { color: #fff; margin: 0 0 4px; font-size: 1.3rem; font-weight: 700; }
  .login-logo p  { color: rgba(255,255,255,.6); font-size: .82rem; margin: 0; }
</style>
</head>
<body>

<div class="login-wrap">
  <div class="login-logo">
    <img src="<?php echo base_url('assets/images/logo_white.png'); ?>" alt="<?php echo SITE_NAME; ?>">
    <h2>Administrator Portal</h2>
    <p></p>
  </div>

  <div class="card">
    <div class="card-header" style="text-align:center;background:var(--green-dark);border-radius:12px 12px 0 0;">
      <h5 style="color:#fff;margin:0;padding:4px 0;">🔐 Admin Login</h5>
    </div>
    <div class="card-body" style="padding:28px;">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="post" action="<?php echo site_url('admin/login'); ?>">
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control"
            value="<?php echo htmlspecialchars($this->input->post('username') ?? ''); ?>"
            placeholder="Admin username" required autofocus>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Admin password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
          Sign In to Admin Panel
        </button>
      </form>

      <hr style="margin:20px 0;border-color:var(--gray-200);">
      <p style="text-align:center;font-size:.8rem;color:var(--gray-600);margin:0;">
        Default credentials: <strong>admin</strong> / <strong>Admin@1234</strong><br>
        <small>Run <a href="<?php echo site_url('admin/create-admin'); ?>">create-admin</a> if first setup</small>
      </p>
    </div>
  </div>

  <p style="text-align:center;margin-top:20px;font-size:.8rem;color:rgba(255,255,255,.5);">
    <a href="<?php echo site_url('/'); ?>" style="color:rgba(255,255,255,.5);">← Back to Public Site</a>
  </p>
</div>

<script src="<?php echo base_url('assets/js/farmstaff.js'); ?>"></script>
</body>
</html>
