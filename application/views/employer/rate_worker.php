<div style="max-width:640px;">
  <div style="margin-bottom:16px;"><a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-outline btn-sm">← Back to Profile</a></div>

  <div class="card">
    <div class="card-header"><h5>⭐ Rate Worker: <?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h5></div>
    <div class="card-body">
      <form method="post" action="<?php echo site_url('dashboard/worker/'.$worker['id'].'/rate'); ?>">
        <p style="color:var(--gray-600);font-size:.9rem;margin-bottom:20px;">
          Rate this worker's overall performance and specific attributes. Ratings directly affect their Trust Score.
        </p>

        <?php
        $criteria = [
          ['name'=>'overall_rating', 'label'=>'Overall Performance', 'required'=>true],
          ['name'=>'work_quality',   'label'=>'Work Quality'],
          ['name'=>'punctuality',    'label'=>'Punctuality'],
          ['name'=>'teamwork',       'label'=>'Teamwork & Cooperation'],
          ['name'=>'reliability',    'label'=>'Reliability & Dependability'],
        ];
        foreach ($criteria as $c): ?>
        <div class="form-group">
          <label class="form-label"><?php echo $c['label']; ?> <?php echo !empty($c['required'])?'<span style="color:var(--danger);">*</span>':''; ?></label>
          <div style="display:flex;gap:8px;align-items:center;">
            <?php for($i=1;$i<=5;$i++): ?>
              <label style="cursor:pointer;display:flex;align-items:center;gap:4px;font-size:1.3rem;">
                <input type="radio" name="<?php echo $c['name']; ?>" value="<?php echo $i; ?>" <?php echo !empty($c['required'])&&$i===3?'checked':''; ?> style="display:none;">
                <span class="star-disp" data-val="<?php echo $i; ?>" style="color:var(--gray-400);">☆</span>
              </label>
            <?php endfor; ?>
            <span id="lbl_<?php echo $c['name']; ?>" style="font-size:.8rem;color:var(--gray-600);margin-left:8px;"></span>
          </div>
        </div>
        <?php endforeach; ?>

        <div class="form-group">
          <label class="form-label">Review / Comments</label>
          <textarea name="review_text" class="form-control" rows="4" placeholder="Share your experience working with this worker..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Would you rehire this worker?</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="radio" name="would_rehire" value="1"> Yes</label>
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="radio" name="would_rehire" value="0"> No</label>
          </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;">
          <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-gold">⭐ Submit Rating</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Simple star rating display
document.querySelectorAll('.form-group').forEach(function(group) {
  var radios = group.querySelectorAll('input[type=radio]');
  if (!radios.length) return;
  var stars  = group.querySelectorAll('.star-disp');
  var name   = radios[0] ? radios[0].name : null;
  var lbl    = name ? document.getElementById('lbl_'+name) : null;
  var labels = ['','Poor','Fair','Good','Very Good','Excellent'];

  function render(val) {
    stars.forEach(function(s,i) {
      s.style.color = i < val ? 'var(--gold)' : 'var(--gray-400)';
      s.textContent = i < val ? '★' : '☆';
    });
    if (lbl) lbl.textContent = labels[val] || '';
  }

  // Set initial from checked radio
  radios.forEach(function(r,i) { if (r.checked) render(i+1); });

  stars.forEach(function(s,i) {
    s.parentElement.addEventListener('mouseenter', function() { render(i+1); });
    group.addEventListener('mouseleave', function() {
      var checked = group.querySelector('input[type=radio]:checked');
      render(checked ? parseInt(checked.value) : 0);
    });
    s.parentElement.querySelector('input').addEventListener('change', function() { render(i+1); });
  });
});
</script>
