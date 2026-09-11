<div style="background:linear-gradient(135deg,var(--green-dark),#2d7a3e);padding:60px 0;text-align:center;color:#fff;">
  <div class="container">
    <h1 style="color:#fff;margin-bottom:12px;">Resources &amp; Help Guides</h1>
    <p style="color:rgba(255,255,255,.85);max-width:560px;margin:0 auto;">Everything you need to get the most out of the Farm Staff Registry platform.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;">
      <?php
      $guides = [
        ['icon'=>'📖','title'=>'Getting Started Guide','desc'=>'Step-by-step instructions for registering your farm and setting up your employer account.','tag'=>'For Employers'],
        ['icon'=>'👤','title'=>'How to Register Workers','desc'=>'Learn how to register farm workers, add their details and create their central profile.','tag'=>'For Employers'],
        ['icon'=>'🔍','title'=>'Running a Background Check','desc'=>'How to search for a worker before hiring using their phone number or registry ID.','tag'=>'For Employers'],
        ['icon'=>'📅','title'=>'Recording Attendance','desc'=>'Guide to daily and bulk attendance entry, and understanding the attendance score.','tag'=>'Attendance'],
        ['icon'=>'🏆','title'=>'Verifying Worker Skills','desc'=>'How to add, rate and request verification of agricultural skills for your workers.','tag'=>'Skills'],
        ['icon'=>'⚠️','title'=>'Reporting an Incident','desc'=>'The correct process for submitting a confidential incident report with supporting evidence.','tag'=>'Incidents'],
        ['icon'=>'⭐','title'=>'Rating a Worker','desc'=>'How to submit a fair performance rating for workers at the end of employment.','tag'=>'Ratings'],
        ['icon'=>'🏚️','title'=>'Understanding Farm Reputation','desc'=>'How farm reputation scores are calculated and what they mean for attracting quality workers.','tag'=>'Reputation'],
      ];
      foreach ($guides as $g): ?>
      <div class="card">
        <div class="card-body" style="padding:24px;">
          <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="font-size:2rem;flex-shrink:0;"><?php echo $g['icon']; ?></div>
            <div>
              <span class="badge badge-green" style="margin-bottom:8px;"><?php echo $g['tag']; ?></span>
              <h5 style="margin:0 0 8px;"><?php echo $g['title']; ?></h5>
              <p style="color:var(--gray-600);font-size:.88rem;line-height:1.6;margin:0 0 14px;"><?php echo $g['desc']; ?></p>
              <a href="<?php echo site_url('about'); ?>#contact" class="btn btn-outline btn-sm">Coming Soon</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section style="background:var(--green-pale);padding:48px 0;text-align:center;">
  <div class="container">
    <h3>Need Personal Assistance?</h3>
    <p style="color:var(--gray-600);margin-bottom:24px;">Contact the Farm Staff Registry support team for help with your account or for platform training.</p>
    <a href="<?php echo site_url('about'); ?>#contact" class="btn btn-primary btn-pill btn-lg">Contact Support</a>
  </div>
</section>
