<!-- HERO -->
<div style="background:linear-gradient(135deg,var(--green-dark),#2d7a3e);padding:60px 0;text-align:center;color:#fff;">
  <div class="container">
    <h1 style="color:#fff;margin-bottom:12px;">About Farm Staff Registry</h1>
    <p style="color:rgba(255,255,255,.85);max-width:580px;margin:0 auto;font-size:1.05rem;line-height:1.7;">
      A centralized digital platform by <?php echo OSACA_NAME; ?> to help agricultural employers register, manage, verify and monitor farm workers.
    </p>
  </div>
</div>

<!-- MISSION -->
<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;">
      <div>
        <div class="section-divider" style="justify-content:flex-start;"></div>
        <h2>Our Mission</h2>
        <p style="color:var(--gray-600);line-height:1.8;font-size:1rem;">
          The Farm Staff Registry was established to bring trust, transparency and accountability to agricultural employment in Ondo State. We provide employers with reliable information about a worker's identity, work history, skills, attendance and conduct — helping them make better-informed hiring and workforce management decisions.
        </p>
        <p style="color:var(--gray-600);line-height:1.8;font-size:1rem;">
          We also protect workers by ensuring fair treatment records and giving them a platform through which their experience and skills are acknowledged and verified.
        </p>
      </div>
      <div style="background:var(--green-pale);border-radius:16px;padding:40px;text-align:center;">
        <div style="font-size:4rem;margin-bottom:16px;">🌾</div>
        <h3 style="color:var(--green-dark);margin-bottom:12px;">Trust. Transparency.<br>Better Farms.</h3>
        <p style="color:var(--gray-600);font-size:.9rem;margin:0;">Operated by <?php echo OSACA_NAME; ?></p>
      </div>
    </div>
  </div>
</section>

<!-- WHAT WE DO -->
<section class="section section-alt">
  <div class="container">
    <div class="section-divider"></div>
    <h2 class="section-title">What We Do</h2>
    <p class="section-sub">Core functions of the Farm Staff Registry platform</p>
    <div class="feature-grid">
      <?php
      $features = [
        ['icon'=>'🪪','title'=>'Worker Registration','desc'=>'Employers register workers with verified identity documents, photos and contact details, creating a single central profile.'],
        ['icon'=>'📋','title'=>'Work History','desc'=>'Complete employment records across all farms — role, start date, end date, duration and employer remarks.'],
        ['icon'=>'🏆','title'=>'Skills Verification','desc'=>'Practical agricultural skills verified and rated by employers, building a credible skills portfolio for each worker.'],
        ['icon'=>'📅','title'=>'Attendance Records','desc'=>'Daily attendance logging generates a reliability score that helps employers assess worker punctuality.'],
        ['icon'=>'⚠️','title'=>'Incident Reporting','desc'=>'Confidential misconduct reports reviewed by administrators before affecting a worker\'s official record.'],
        ['icon'=>'💯','title'=>'Trust Score','desc'=>'A transparent scoring system reflecting verified work performance, conduct and disciplinary history.'],
        ['icon'=>'🔍','title'=>'Background Checks','desc'=>'Employers search any worker before hiring using phone number or registry ID to access verified records.'],
        ['icon'=>'🏚️','title'=>'Farm Reputation','desc'=>'Workers anonymously rate farms on conditions, safety, payment and treatment — creating a credible Farm Reputation Score.'],
      ];
      foreach ($features as $f): ?>
      <div class="feature-card">
        <div class="fc-icon"><?php echo $f['icon']; ?></div>
        <h4><?php echo $f['title']; ?></h4>
        <p><?php echo $f['desc']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section" id="faq">
  <div class="container" style="max-width:800px;">
    <div class="section-divider"></div>
    <h2 class="section-title">Frequently Asked Questions</h2>

    <?php
    $faqs = [
      ['q'=>'Who can register on the Farm Staff Registry?', 'a'=>'Any agricultural employer operating in Ondo State can register. Employers must provide their farm name, contact details and agree to the platform terms.'],
      ['q'=>'Is it free to use?', 'a'=>'The Farm Staff Registry is operated by OSACA as a public service for the agricultural community of Ondo State.'],
      ['q'=>'How is worker data kept confidential?', 'a'=>'Worker data is only accessible to registered and verified employers. Incident reports are reviewed by administrators before affecting public records. Workers cannot be identified without proper authorization.'],
      ['q'=>'Can a worker dispute an incident report?', 'a'=>'Yes. Incident reports are reviewed by administrators before being accepted. Workers can appeal through their current employer or by contacting OSACA directly.'],
      ['q'=>'How is the Trust Score calculated?', 'a'=>'The Trust Score starts at 50 and adjusts based on verified performance ratings, attendance records, skill verifications and accepted incident reports. Disciplinary points reduce over time as workers improve their conduct.'],
      ['q'=>'What happens if an employer submits a false report?', 'a'=>'Administrators review all incident reports and farm ratings for accuracy. False reports can result in employer account suspension.'],
    ];
    foreach ($faqs as $i => $faq): ?>
    <div style="border:1px solid var(--gray-200);border-radius:8px;margin-bottom:12px;overflow:hidden;">
      <div style="padding:18px 20px;background:#fff;font-weight:600;cursor:pointer;display:flex;justify-content:space-between;align-items:center;" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none'">
        <?php echo htmlspecialchars($faq['q']); ?>
        <span style="font-size:1.2rem;color:var(--green-dark);">+</span>
      </div>
      <div style="padding:0 20px 18px;color:var(--gray-600);font-size:.92rem;line-height:1.7;display:none;">
        <?php echo htmlspecialchars($faq['a']); ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- CONTACT -->
<section class="section section-alt" id="contact">
  <div class="container" style="max-width:680px;text-align:center;">
    <div class="section-divider"></div>
    <h2 class="section-title">Contact &amp; Support</h2>
    <p style="color:var(--gray-600);margin-bottom:32px;">Have questions or need assistance? Reach out to the Farm Staff Registry support team.</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;">
      <div class="card" style="text-align:center;padding:24px;">
        <div style="font-size:2rem;margin-bottom:10px;">📧</div>
        <h6>Email</h6>
        <p style="font-size:.88rem;color:var(--gray-600);"><?php echo SITE_EMAIL; ?></p>
      </div>
      <div class="card" style="text-align:center;padding:24px;">
        <div style="font-size:2rem;margin-bottom:10px;">📞</div>
        <h6>Phone</h6>
        <p style="font-size:.88rem;color:var(--gray-600);"><?php echo SITE_PHONE; ?></p>
      </div>
      <div class="card" style="text-align:center;padding:24px;">
        <div style="font-size:2rem;margin-bottom:10px;">🏢</div>
        <h6>Office</h6>
        <p style="font-size:.88rem;color:var(--gray-600);">OSACA Secretariat, Akure, Ondo State</p>
      </div>
    </div>
  </div>
</section>
