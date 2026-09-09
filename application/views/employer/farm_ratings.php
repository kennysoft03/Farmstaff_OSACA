<div style="max-width:860px;">
  <h5 style="margin-bottom:16px;">🏚️ Farm Ratings — <?php echo htmlspecialchars($employer['farm_name']); ?></h5>

  <?php if (empty($ratings)): ?>
    <div class="card" style="text-align:center;padding:48px 20px;">
      <div style="font-size:3rem;margin-bottom:12px;">⭐</div>
      <h4>No Ratings Yet</h4>
      <p style="color:var(--gray-600);">Farm ratings from workers will appear here once submitted and approved.</p>
    </div>
  <?php else: ?>
    <?php
    $approved = array_filter($ratings, fn($r) => $r['status'] === 'approved');
    $avg = count($approved) ? array_sum(array_column($approved, 'overall_rating')) / count($approved) : 0;
    ?>
    <!-- Summary -->
    <div class="card" style="margin-bottom:20px;">
      <div class="card-body" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="text-align:center;min-width:120px;">
          <div style="font-size:3rem;font-weight:800;color:var(--gold);"><?php echo number_format($avg,1); ?></div>
          <div style="color:var(--gold);font-size:1.3rem;"><?php echo str_repeat('★', round($avg)); ?><?php echo str_repeat('☆', 5-round($avg)); ?></div>
          <div style="font-size:.78rem;color:var(--gray-600);">Average Rating</div>
        </div>
        <div style="flex:1;">
          <div style="font-size:.9rem;color:var(--gray-600);margin-bottom:10px;"><?php echo count($approved); ?> approved rating(s) | <?php echo count($ratings)-count($approved); ?> pending</div>
          <?php foreach(['working_conditions','safety','payment_promptness','treatment'] as $field): ?>
            <?php $vals=array_filter(array_column($approved,$field)); $avg_field=count($vals)?array_sum($vals)/count($vals):0; ?>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;font-size:.85rem;">
              <span style="min-width:170px;"><?php echo ucwords(str_replace('_',' ',$field)); ?></span>
              <div style="flex:1;background:var(--gray-200);border-radius:4px;height:6px;overflow:hidden;">
                <div style="height:100%;background:var(--gold);width:<?php echo $avg_field*20; ?>%;border-radius:4px;"></div>
              </div>
              <span style="min-width:30px;font-weight:700;"><?php echo number_format($avg_field,1); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- List -->
    <div class="card">
      <div class="card-header"><h5>All Ratings</h5></div>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Rating</th><th>Conditions</th><th>Safety</th><th>Payment</th><th>Treatment</th><th>Comment</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach ($ratings as $r): ?>
            <tr>
              <td><span style="color:var(--gold);font-size:1.1rem;"><?php echo str_repeat('★',(int)$r['overall_rating']); ?></span></td>
              <td><?php echo $r['working_conditions']??'—'; ?>/5</td>
              <td><?php echo $r['safety']??'—'; ?>/5</td>
              <td><?php echo $r['payment_promptness']??'—'; ?>/5</td>
              <td><?php echo $r['treatment']??'—'; ?>/5</td>
              <td style="font-size:.82rem;max-width:180px;"><?php echo htmlspecialchars(substr($r['review_text']??'',0,60)); ?></td>
              <td><span class="badge <?php echo $r['status']==='approved'?'badge-success':($r['status']==='pending'?'badge-warning':'badge-danger'); ?>"><?php echo $r['status']; ?></span></td>
              <td style="font-size:.8rem;"><?php echo date('d M Y',strtotime($r['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>
