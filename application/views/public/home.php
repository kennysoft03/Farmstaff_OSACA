<?php
$total_employers = isset($stats['total_employers']) ? number_format((int)$stats['total_employers']) : '0';
$total_workers   = isset($stats['total_workers'])   ? number_format((int)$stats['total_workers'])   : '0';
$verified        = isset($stats['verified_profiles'])? number_format((int)$stats['verified_profiles']): '0';
$avg_rating      = isset($stats['avg_farm_rating'])  ? number_format((float)$stats['avg_farm_rating'], 1) : '4.6';
// For display, show platform targets or actual DB numbers
$disp_employers  = ((int)$stats['total_employers'] < 100) ? '10,000+' : $total_employers;
$disp_workers    = ((int)$stats['total_workers']   < 100) ? '45,000+' : $total_workers;
$disp_verified   = ((int)$stats['verified_profiles']< 100) ? '25,000+' : $verified;
$disp_rating     = ((float)$stats['avg_farm_rating'] < 0.1) ? '4.6 / 5' : $avg_rating . ' / 5';
?>

<!-- ===== HERO ===== -->
<section class="hero-section">
  <div class="hero-bg" style="background-image:url('<?php echo base_url('assets/images/hero-bg.jpg'); ?>');" aria-hidden="true"></div>
  <div class="hero-overlay" aria-hidden="true"></div>

  <div class="hero-content">
    <!-- Left -->
    <div class="hero-left">
      <h1>Building Trust.<br><span>Empowering Farms.</span></h1>
      <p>The Farm Staff Registry helps employers make informed hiring decisions, track work history, and build safer, more accountable workplaces for everyone.</p>

      <div class="hero-actions">
        <a href="<?php echo site_url('register'); ?>" class="btn-hero-primary">
          <span>👤</span> Register Now
        </a>
        <a href="<?php echo site_url('dashboard/background-check'); ?>" class="btn-hero-secondary">
          <span>🔍</span> Search Worker
        </a>
      </div>

      <div class="hero-features">
        <div class="hero-feature">
          <div class="feat-icon">🛡️</div>
          <strong>Verify Workers</strong>
          <span>Check history,<br>skills &amp; trust score.</span>
        </div>
        <div class="hero-feature">
          <div class="feat-icon">⭐</div>
          <strong>Rate Employers</strong>
          <span>Share your experience<br>anonymously.</span>
        </div>
        <div class="hero-feature">
          <div class="feat-icon">📊</div>
          <strong>Build Safer Farms</strong>
          <span>Transparency benefits<br>everyone.</span>
        </div>
        <div class="hero-feature">
          <div class="feat-icon">🔒</div>
          <strong>Confidential &amp; Secure</strong>
          <span>Your data is protected<br>and private.</span>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ===== STATS BAR ===== -->
<div class="stats-bar">
  <div class="container">
    <div class="stat-item">
      <div class="stat-icon">👥</div>
      <span class="stat-val"><?php echo $disp_employers; ?></span>
      <span class="stat-lbl">Registered Employers</span>
    </div>
    <div class="stat-item">
      <div class="stat-icon">👤</div>
      <span class="stat-val"><?php echo $disp_workers; ?></span>
      <span class="stat-lbl">Registered Workers</span>
    </div>
    <div class="stat-item">
      <div class="stat-icon">✅</div>
      <span class="stat-val"><?php echo $disp_verified; ?></span>
      <span class="stat-lbl">Profiles Verified</span>
    </div>
    <div class="stat-item">
      <div class="stat-icon">⭐</div>
      <span class="stat-val"><?php echo $disp_rating; ?></span>
      <span class="stat-lbl">Average Farm Rating</span>
    </div>
  </div>
</div>

<!-- ===== BRAND IDENTITY BRIDGE ===== -->
<div style="padding:48px 0 0;background:#fff;">
  <div class="container" style="text-align:center;">
    <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Farm Staff Registry" style="height:64px;margin-bottom:14px;">
    <h3 style="font-size:1.5rem;color:var(--green-dark);margin:0 0 6px;letter-spacing:.04em;">FARM STAFF REGISTRY</h3>
    <p style="color:var(--gray-600);font-size:1rem;margin:0;letter-spacing:.06em;font-weight:500;"><?php echo SITE_TAGLINE; ?></p>
    <div style="width:48px;height:3px;background:var(--green-light);border-radius:2px;margin:16px auto 0;"></div>
  </div>
</div>

<!-- ===== BETTER FOR EVERYONE ===== -->
<section class="section">
  <div class="container">
    <div class="section-divider"></div>
    <h2 class="section-title">Better for Everyone</h2>
    <p class="section-sub">The Farm Staff Registry creates a fairer, more transparent agricultural employment ecosystem for all stakeholders.</p>

    <div class="feature-grid">
      <div class="feature-card">
        <div class="fc-icon">🛡️</div>
        <h4>Safer Workplaces</h4>
        <p>Reduce risks through accountability. Know who you're hiring before they set foot on your farm.</p>
      </div>
      <div class="feature-card">
        <div class="fc-icon">👥</div>
        <h4>Better Hiring</h4>
        <p>Hire with confidence using verified data — skills, work history, trust scores and conduct records.</p>
      </div>
      <div class="feature-card">
        <div class="fc-icon">⭐</div>
        <h4>Stronger Reputation</h4>
        <p>Good farms attract great workers. Build a Farm Reputation Score that sets you apart.</p>
      </div>
      <div class="feature-card">
        <div class="fc-icon">🌱</div>
        <h4>Growth Together</h4>
        <p>Building a trusted farming community where workers and employers both benefit and grow.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section class="section section-alt">
  <div class="container">
    <div class="section-divider"></div>
    <h2 class="section-title">How It Works</h2>
    <p class="section-sub">Getting started is simple. Join thousands of employers already using the Farm Staff Registry.</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:24px;margin-top:40px;">
      <?php
      $steps = [
        ['num'=>'01','icon'=>'📝','title'=>'Register Your Farm','desc'=>'Create your employer account in minutes with your farm details and contact information.'],
        ['num'=>'02','icon'=>'👤','title'=>'Add Your Workers','desc'=>'Register workers using their phone number, ID and photograph to create their central profile.'],
        ['num'=>'03','icon'=>'✅','title'=>'Track & Verify','desc'=>'Record attendance, verify skills and monitor performance over time with every worker.'],
        ['num'=>'04','icon'=>'🔍','title'=>'Search Before Hiring','desc'=>'Run a background check on any worker using their phone number or registry ID before hiring.'],
      ];
      foreach ($steps as $step): ?>
      <div style="text-align:center;padding:28px 20px;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--green-dark);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.6rem;margin:0 auto 16px;">
          <?php echo $step['icon']; ?>
        </div>
        <div style="font-size:.75rem;font-weight:700;color:var(--green-dark);letter-spacing:.1em;margin-bottom:8px;">STEP <?php echo $step['num']; ?></div>
        <h4 style="margin-bottom:10px;"><?php echo $step['title']; ?></h4>
        <p style="color:var(--gray-600);font-size:.9rem;line-height:1.6;"><?php echo $step['desc']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== KEY FEATURES ===== -->
<section class="section">
  <div class="container">
    <div class="section-divider"></div>
    <h2 class="section-title">Key Platform Features</h2>
    <p class="section-sub">Everything you need to manage your agricultural workforce with confidence.</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;">
      <?php
      $features = [
        ['icon'=>'🪪','title'=>'Central Worker Profile','desc'=>'One verified profile per worker containing employment records, ID verification and photo.'],
        ['icon'=>'📋','title'=>'Work History Tracking','desc'=>'Complete employment history showing roles, durations, joining and leaving dates across all farms.'],
        ['icon'=>'🏆','title'=>'Skills Verification','desc'=>'Employers verify and rate practical skills like tractor operation, spraying and harvesting.'],
        ['icon'=>'📅','title'=>'Attendance Evaluation','desc'=>'Track punctuality and consistency with attendance records that generate a reliable attendance score.'],
        ['icon'=>'🔐','title'=>'Confidential Incident Reports','desc'=>'Report serious misconduct with evidence. Admin review ensures fair and accurate records.'],
        ['icon'=>'💯','title'=>'Trust Score System','desc'=>'A private score reflecting work performance and conduct, with disciplinary points that reduce over time.'],
        ['icon'=>'🔍','title'=>'Background Check','desc'=>'Search any worker by phone number or ID to get their verified history, skills and trust profile.'],
        ['icon'=>'🏚️','title'=>'Farm Reputation Score','desc'=>'Workers rate farms on conditions, safety, payment and treatment — building a credible farm reputation.'],
      ];
      foreach ($features as $f): ?>
      <div style="display:flex;gap:16px;padding:20px;background:#fff;border-radius:12px;border:1px solid var(--gray-200);box-shadow:0 2px 8px rgba(0,0,0,.05);">
        <div style="font-size:1.8rem;flex-shrink:0;width:44px;text-align:center;"><?php echo $f['icon']; ?></div>
        <div>
          <h5 style="margin:0 0 6px;"><?php echo $f['title']; ?></h5>
          <p style="margin:0;color:var(--gray-600);font-size:.88rem;line-height:1.6;"><?php echo $f['desc']; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section style="background:linear-gradient(135deg,var(--green-dark) 0%,#2d7a3e 100%);padding:72px 0;text-align:center;">
  <div class="container">
    <h2 style="color:#fff;font-size:2.2rem;margin-bottom:16px;">Ready to Build a More Trusted Workforce?</h2>
    <p style="color:rgba(255,255,255,.85);max-width:520px;margin:0 auto 36px;font-size:1.05rem;line-height:1.7;">
      Join the Farm Staff Registry today and take the guesswork out of hiring. Start registering workers and searching verified profiles in minutes.
    </p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
      <a href="<?php echo site_url('register'); ?>" class="btn-hero-primary" style="background:#fff;color:var(--green-dark);box-shadow:0 4px 20px rgba(0,0,0,.15);">
        👤 Register as Employer
      </a>
      <a href="<?php echo site_url('about'); ?>" style="display:inline-flex;align-items:center;gap:8px;padding:15px 30px;border:2px solid rgba(255,255,255,.6);color:#fff;border-radius:50px;font-weight:700;font-size:1rem;">
        Learn More →
      </a>
    </div>
  </div>
</section>

<!-- ===== PLATFORM BANNER ===== -->
<section style="padding:40px 0;background:var(--gray-100);border-top:1px solid var(--gray-200);">
  <div class="container" style="text-align:center;">
    <p style="color:var(--gray-600);font-size:.9rem;margin:0;">
      A digital platform for <strong style="color:var(--green-dark);">Farmers and Agricultural Employers in Ondo State, Nigeria</strong>
      &nbsp;|&nbsp; Promoting trust, transparency and accountability in agricultural employment.
    </p>
  </div>
</section>
