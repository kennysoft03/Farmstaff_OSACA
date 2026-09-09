<?php $ts=(float)$worker['trust_score']; $tc=$ts>=70?'success':($ts>=40?'warning':'danger'); ?>

<!-- Action bar -->
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
  <a href="<?php echo site_url('dashboard/workers'); ?>"                          class="btn btn-secondary btn-sm btn-bold"><i class="la la-arrow-left"></i> Workers</a>
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/edit'); ?>"    class="btn btn-label-brand btn-sm btn-bold"><i class="la la-edit"></i> Edit</a>
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/history'); ?>" class="btn btn-label-brand btn-sm btn-bold"><i class="la la-briefcase"></i> Work History</a>
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/skills'); ?>"  class="btn btn-label-success btn-sm btn-bold"><i class="la la-star"></i> Skills</a>
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/attendance'); ?>" class="btn btn-label-success btn-sm btn-bold"><i class="la la-calendar"></i> Attendance</a>
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/incident'); ?>"   class="btn btn-label-danger btn-sm btn-bold"><i class="la la-exclamation-triangle"></i> Report Incident</a>
  <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/rate'); ?>"       class="btn btn-warning btn-sm btn-bold"><i class="la la-star-o"></i> Rate Worker</a>
</div>

<div class="row">
  <!-- Left: Profile -->
  <div class="col-xl-3 col-lg-4">
    <div class="kt-portlet kt-portlet--height-fluid">
      <div class="kt-portlet__body kt-portlet__body--center kt-portlet__body--middle" style="background:linear-gradient(135deg,#111,#1a3a1a);border-radius:4px 4px 0 0;padding:28px;">
        <div style="text-align:center;">
          <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.15);border:3px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 12px;overflow:hidden;">
            <?php if(!empty($worker['photo'])): ?><img src="<?php echo base_url($worker['photo']); ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>👤<?php endif; ?>
          </div>
          <h5 style="color:#fff;margin:0 0 4px;"><?php echo htmlspecialchars($worker['firstname'].' '.$worker['lastname']); ?></h5>
          <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill" style="font-family:monospace;"><?php echo htmlspecialchars($worker['worker_id']); ?></span>
          <div style="margin-top:14px;">
            <div style="font-size:2rem;font-weight:800;color:#fff;"><?php echo number_format($ts,0); ?><span style="font-size:.9rem;opacity:.6;">/100</span></div>
            <div style="font-size:.75rem;color:rgba(255,255,255,.6);">Trust Score</div>
          </div>
        </div>
      </div>
      <div class="kt-portlet__body" style="padding:18px;">
        <?php $info=['Phone'=>$worker['phone'],'Alt Phone'=>$worker['alt_phone']??'','Email'=>$worker['email']??'','Gender'=>ucfirst($worker['gender']??''),'DOB'=>!empty($worker['dob'])?date('d M Y',strtotime($worker['dob'])):'','LGA'=>$worker['lga']??'','ID Type'=>$worker['id_type']??'','ID No.'=>$worker['id_number']??''];
        foreach($info as $k=>$v): if(!$v) continue; ?>
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f7f8fa;font-size:.85rem;">
            <span style="color:#a2a3b7;"><?php echo $k; ?></span>
            <span style="font-weight:600;"><?php echo htmlspecialchars($v); ?></span>
          </div>
        <?php endforeach; ?>
        <div style="margin-top:12px;text-align:center;">
          <?php $sc=['active'=>'success','inactive'=>'secondary','flagged'=>'warning','suspended'=>'danger']; ?>
          <span class="kt-badge kt-badge--<?php echo $sc[$worker['status']]??'secondary'; ?> kt-badge--inline kt-badge--pill" style="font-size:.88rem;padding:6px 16px;"><?php echo strtoupper($worker['status']); ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right -->
  <div class="col-xl-9 col-lg-8">

    <!-- Attendance Summary -->
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-success flaticon2-calendar-2"></i></span>
          <h3 class="kt-portlet__head-title">Attendance Summary</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
          <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/attendance'); ?>" class="btn btn-label-success btn-sm btn-bold">Log Attendance</a>
        </div>
      </div>
      <div class="kt-portlet__body">
        <?php if(!empty($att_summary['total'])): ?>
          <div class="row" style="text-align:center;margin-bottom:14px;">
            <?php foreach(['total'=>['Total','brand'],'present'=>['Present','success'],'late'=>['Late','warning'],'absent'=>['Absent','danger']] as $k=>[$lbl,$col]): ?>
            <div class="col-3">
              <div style="font-size:1.6rem;font-weight:800;" class="kt-font-<?php echo $col; ?>"><?php echo $att_summary[$k]??0; ?></div>
              <div style="font-size:.78rem;color:#a2a3b7;"><?php echo $lbl; ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:6px;">
            <span>Attendance Score</span><strong><?php echo $att_summary['score']??0; ?>%</strong>
          </div>
          <div class="progress progress--sm"><div class="progress-bar kt-bg-success" style="width:<?php echo $att_summary['score']??0; ?>%;"></div></div>
        <?php else: ?>
          <p style="color:#a2a3b7;margin:0;">No attendance records yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Work History -->
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-calendar-8"></i></span>
          <h3 class="kt-portlet__head-title">Work History</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
          <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/add-history'); ?>" class="btn btn-label-brand btn-sm btn-bold">+ Add Record</a>
        </div>
      </div>
      <div class="kt-portlet__body kt-portlet__body--fit">
        <?php if(empty($work_history)): ?>
          <div style="padding:20px;color:#a2a3b7;">No work history recorded.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover" style="margin:0;">
              <thead class="thead-light"><tr><th>Role</th><th>Farm</th><th>Start</th><th>End</th><th>Duration</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach($work_history as $h): ?>
                <tr>
                  <td style="font-weight:600;"><?php echo htmlspecialchars($h['role']); ?></td>
                  <td><?php echo htmlspecialchars($h['farm_name']??'—'); ?></td>
                  <td><?php echo date('d M Y',strtotime($h['start_date'])); ?></td>
                  <td><?php echo $h['is_current']?'<span class="kt-font-success">Present</span>':(!empty($h['end_date'])?date('d M Y',strtotime($h['end_date'])):'—'); ?></td>
                  <td><?php echo !empty($h['duration_days'])?ceil($h['duration_days']/30).' mo':'—'; ?></td>
                  <td><span class="kt-badge kt-badge--<?php echo $h['status']==='active'?'success':'secondary'; ?> kt-badge--inline kt-badge--pill"><?php echo $h['status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Skills -->
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-warning flaticon-star"></i></span>
          <h3 class="kt-portlet__head-title">Skills</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
          <a href="<?php echo site_url('dashboard/worker/'.$worker['id'].'/skills'); ?>" class="btn btn-label-warning btn-sm btn-bold">Manage</a>
        </div>
      </div>
      <div class="kt-portlet__body">
        <?php if(empty($skills)): ?>
          <p style="color:#a2a3b7;margin:0;">No skills recorded yet.</p>
        <?php else: ?>
          <?php foreach($skills as $s): ?>
            <span class="kt-badge kt-badge--<?php echo $s['verified']?'success':'secondary'; ?> kt-badge--inline kt-badge--pill" style="margin:3px;" title="<?php echo $s['verified']?'Verified':'Unverified'; ?>">
              <?php echo $s['verified']?'✓ ':''; ?><?php echo htmlspecialchars($s['skill_name']); ?>
              <span style="opacity:.6;">(<?php echo str_repeat('★',(int)$s['rating']); ?>)</span>
            </span>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Incidents (if any) -->
    <?php if(!empty($incidents)): ?>
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-danger flaticon-warning-sign"></i></span>
          <h3 class="kt-portlet__head-title">Incidents (<?php echo count($incidents); ?>)</h3>
        </div>
      </div>
      <div class="kt-portlet__body kt-portlet__body--fit">
        <div class="table-responsive">
          <table class="table" style="margin:0;">
            <thead class="thead-light"><tr><th>Type</th><th>Date</th><th>Severity</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach($incidents as $inc): ?>
              <tr>
                <td><?php echo htmlspecialchars($inc['incident_type']); ?></td>
                <td><?php echo date('d M Y',strtotime($inc['incident_date'])); ?></td>
                <td><span class="kt-badge kt-badge--<?php echo $inc['severity']==='severe'?'danger':($inc['severity']==='moderate'?'warning':'info'); ?> kt-badge--inline kt-badge--pill"><?php echo $inc['severity']; ?></span></td>
                <td><span class="kt-badge kt-badge--<?php echo ['accepted'=>'danger','pending'=>'warning','rejected'=>'success'][$inc['status']]??'secondary'; ?> kt-badge--inline kt-badge--pill"><?php echo $inc['status']; ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>
