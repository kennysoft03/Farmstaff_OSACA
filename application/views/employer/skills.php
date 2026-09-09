<div style="max-width:820px;">
  <div style="margin-bottom:16px;display:flex;gap:10px;align-items:center;">
    <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-outline btn-sm">← Back to Profile</a>
    <h5 style="margin:0;">🏆 Skills: <?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h5>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <!-- Add Skill -->
    <div class="card">
      <div class="card-header"><h5>+ Add Skill</h5></div>
      <div class="card-body">
        <form method="post" action="<?php echo site_url('dashboard/worker/'.$worker['id'].'/skills'); ?>">
          <div class="form-group">
            <label class="form-label">Skill Name <span style="color:var(--danger);">*</span></label>
            <input type="text" name="skill_name" class="form-control" placeholder="e.g. Tractor Driving, Spraying, Harvesting" required>
          </div>
          <div class="form-group">
            <label class="form-label">Proficiency</label>
            <select name="proficiency" class="form-control">
              <?php foreach(['beginner','intermediate','skilled','expert'] as $p): ?><option value="<?php echo $p; ?>"><?php echo ucfirst($p); ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Rating (1–5 stars)</label>
            <select name="rating" class="form-control">
              <?php for($i=1;$i<=5;$i++): ?><option value="<?php echo $i; ?>" <?php echo $i===3?'selected':''; ?>><?php echo str_repeat('★',$i).' ('.$i.')'; ?></option><?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Add Skill</button>
        </form>
      </div>
    </div>

    <!-- Current Skills -->
    <div class="card">
      <div class="card-header"><h5>Current Skills (<?php echo count($skills); ?>)</h5></div>
      <div class="card-body" style="padding:0;">
        <?php if (empty($skills)): ?>
          <p style="padding:16px;color:var(--gray-600);margin:0;text-align:center;">No skills recorded yet.</p>
        <?php else: ?>
          <?php foreach ($skills as $s): ?>
          <div style="padding:12px 16px;border-bottom:1px solid var(--gray-200);display:flex;justify-content:space-between;align-items:center;">
            <div>
              <div style="font-weight:600;font-size:.9rem;">
                <?php if ($s['verified']): ?><span style="color:var(--green-dark);">✅ </span><?php endif; ?>
                <?php echo htmlspecialchars($s['skill_name']); ?>
              </div>
              <div style="font-size:.78rem;color:var(--gray-600);">
                <?php echo ucfirst($s['proficiency']); ?>
                &nbsp;|&nbsp;<?php echo str_repeat('★',(int)$s['rating']); ?>
                <?php if ($s['verified']): ?>&nbsp;| <span style="color:var(--green-dark);">Verified</span><?php endif; ?>
              </div>
              <?php if (!empty($s['notes'])): ?><div style="font-size:.75rem;color:var(--gray-500);"><?php echo htmlspecialchars($s['notes']); ?></div><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Common Agricultural Skills reference -->
  <div class="card" style="margin-top:20px;">
    <div class="card-header"><h5>💡 Common Agricultural Skills</h5></div>
    <div class="card-body">
      <div style="display:flex;flex-wrap:wrap;gap:6px;">
        <?php foreach(['Tractor Driving','Land Preparation','Crop Planting','Harvesting','Spraying Pesticides','Irrigation Management','Livestock Care','Poultry Management','Fish Farming','Fertilizer Application','Record Keeping','Equipment Maintenance','Greenhouse Management','Post-Harvest Handling','First Aid','Team Leadership'] as $skill): ?>
          <span class="skill-tag" style="cursor:pointer;" onclick="document.querySelector('[name=skill_name]').value='<?php echo $skill; ?>'">
            <?php echo $skill; ?>
          </span>
        <?php endforeach; ?>
      </div>
      <p style="font-size:.78rem;color:var(--gray-600);margin:10px 0 0;">Click a skill to auto-fill the form above.</p>
    </div>
  </div>
</div>
