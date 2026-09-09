<?php
$months_list = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$att_by_date  = [];
foreach ($attendance as $a) { $att_by_date[$a['attendance_date']] = $a; }
// Build calendar days for this month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
?>

<div style="margin-bottom:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id']); ?>" class="btn btn-outline btn-sm">← Back to Profile</a>
  <h5 style="margin:0;">📅 Attendance: <?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h5>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

  <!-- Log Attendance -->
  <div class="card">
    <div class="card-header"><h5>+ Log Attendance</h5></div>
    <div class="card-body">
      <form method="post" action="<?php echo site_url('dashboard/worker/'.$worker['id'].'/attendance'); ?>">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Date <span style="color:var(--danger);">*</span></label>
            <input type="date" name="attendance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Status <span style="color:var(--danger);">*</span></label>
            <select name="att_status" class="form-control" required>
              <?php foreach(['present'=>'Present','absent'=>'Absent','late'=>'Late','half-day'=>'Half Day','leave'=>'On Leave'] as $v=>$l): ?>
                <option value="<?php echo $v; ?>"><?php echo $l; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Check In</label><input type="time" name="check_in" class="form-control"></div>
          <div class="form-group"><label class="form-label">Check Out</label><input type="time" name="check_out" class="form-control"></div>
        </div>
        <div class="form-group"><label class="form-label">Notes</label><input type="text" name="notes" class="form-control" placeholder="Optional notes..."></div>
        <button type="submit" class="btn btn-primary btn-block">Save Attendance</button>
      </form>
    </div>
  </div>

  <!-- Summary -->
  <div>
    <div class="card" style="margin-bottom:16px;">
      <div class="card-header"><h5>📊 Summary</h5></div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;text-align:center;">
          <?php foreach(['total'=>['📋','Total','var(--gray-800)'],'present'=>['✅','Present','var(--green-dark)'],'late'=>['⏰','Late','var(--gold)'],'absent'=>['❌','Absent','var(--danger)']] as $k=>[$icon,$label,$col]): ?>
          <div style="padding:12px;background:var(--gray-100);border-radius:8px;">
            <div style="font-size:1.5rem;"><?php echo $icon; ?></div>
            <div style="font-size:1.4rem;font-weight:800;color:<?php echo $col; ?>;"><?php echo $att_summary[$k]??0; ?></div>
            <div style="font-size:.75rem;color:var(--gray-600);"><?php echo $label; ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:14px;">
          <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:6px;"><span>Attendance Score</span><strong><?php echo $att_summary['score']??0; ?>%</strong></div>
          <div class="att-bar"><div class="att-bar-fill" style="width:<?php echo $att_summary['score']??0; ?>%;"></div></div>
        </div>
      </div>
    </div>

    <!-- Month nav -->
    <div class="card">
      <div class="card-body" style="padding:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
          <?php
          $pm = $month - 1; $py = $year;
          if ($pm < 1) { $pm = 12; $py--; }
          $nm = $month + 1; $ny = $year;
          if ($nm > 12) { $nm = 1; $ny++; }
          ?>
          <a href="?month=<?php echo $pm; ?>&year=<?php echo $py; ?>" class="btn btn-outline btn-sm">← Prev</a>
          <strong style="font-size:.95rem;"><?php echo $months_list[$month-1]; ?> <?php echo $year; ?></strong>
          <a href="?month=<?php echo $nm; ?>&year=<?php echo $ny; ?>" class="btn btn-outline btn-sm">Next →</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Calendar / Records Table -->
<div class="card" style="margin-top:20px;">
  <div class="card-header"><h5>📆 <?php echo $months_list[$month-1]; ?> <?php echo $year; ?> Attendance Records</h5></div>
  <div class="table-responsive">
    <?php if (empty($attendance)): ?>
      <p style="padding:20px;color:var(--gray-600);text-align:center;">No attendance records for this month.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Date</th><th>Day</th><th>Status</th><th>Check In</th><th>Check Out</th><th>Notes</th></tr></thead>
        <tbody>
          <?php foreach ($attendance as $a): ?>
          <tr>
            <td><?php echo date('d M Y', strtotime($a['attendance_date'])); ?></td>
            <td style="font-size:.82rem;"><?php echo date('l', strtotime($a['attendance_date'])); ?></td>
            <td>
              <?php $sc=['present'=>'badge-success','absent'=>'badge-danger','late'=>'badge-warning','half-day'=>'badge-info','leave'=>'badge-secondary']; ?>
              <span class="badge <?php echo $sc[$a['status']]??'badge-secondary'; ?>"><?php echo $a['status']; ?></span>
            </td>
            <td style="font-size:.85rem;"><?php echo $a['check_in'] ? date('g:i A', strtotime($a['check_in'])) : '—'; ?></td>
            <td style="font-size:.85rem;"><?php echo $a['check_out'] ? date('g:i A', strtotime($a['check_out'])) : '—'; ?></td>
            <td style="font-size:.82rem;"><?php echo htmlspecialchars($a['notes']??''); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
