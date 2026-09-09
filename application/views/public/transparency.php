<div style="background:linear-gradient(135deg,var(--green-dark),#2d7a3e);padding:60px 0;text-align:center;color:#fff;">
  <div class="container">
    <h1 style="color:#fff;margin-bottom:12px;">For Transparency</h1>
    <p style="color:rgba(255,255,255,.85);max-width:560px;margin:0 auto;font-size:1.05rem;">How the Farm Staff Registry ensures fair, accurate and trusted records for everyone involved.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:28px;">
      <?php
      $items = [
        ['icon'=>'⚖️','title'=>'Fair for Workers','desc'=>'All incident reports are reviewed by administrators before they can affect a worker\'s official record. Workers are protected from false or unfair claims.'],
        ['icon'=>'🔐','title'=>'Confidential Reporting','desc'=>'Incident reports and farm ratings are handled discreetly. Workers can rate farms anonymously, ensuring honest feedback without fear.'],
        ['icon'=>'🛡️','title'=>'Admin Moderation','desc'=>'Trained administrators review all sensitive reports, ratings and disputes. Only verified and approved information affects official records.'],
        ['icon'=>'📊','title'=>'Auditable Records','desc'=>'All changes to worker records are logged in an audit trail. Administrators can see who made changes, when, and what was modified.'],
        ['icon'=>'🏆','title'=>'Verified Skills','desc'=>'Skill records are verified by both employers and administrators. Only confirmed, observable skills are marked as verified on a worker\'s profile.'],
        ['icon'=>'📉','title'=>'Improving Over Time','desc'=>'Disciplinary points reduce over time through good conduct. Workers can improve their standing — the system rewards rehabilitation.'],
      ];
      foreach ($items as $item): ?>
      <div class="card" style="padding:0;">
        <div class="card-body" style="padding:28px;">
          <div style="font-size:2.2rem;margin-bottom:14px;"><?php echo $item['icon']; ?></div>
          <h4 style="margin-bottom:10px;"><?php echo $item['title']; ?></h4>
          <p style="color:var(--gray-600);font-size:.92rem;line-height:1.7;margin:0;"><?php echo $item['desc']; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section style="background:var(--green-pale);padding:56px 0;text-align:center;">
  <div class="container" style="max-width:680px;">
    <h2 style="margin-bottom:16px;">Our Commitment</h2>
    <p style="color:var(--gray-600);font-size:1rem;line-height:1.8;margin-bottom:0;">
      The Farm Staff Registry is committed to maintaining accurate, fair and private records. We do not share personal data outside the platform. Every employer using the platform agrees to our terms of ethical use. Administrators receive ongoing training to ensure consistent, unbiased moderation. Together, we are building a more trustworthy agricultural employment ecosystem in Ondo State.
    </p>
  </div>
</section>
