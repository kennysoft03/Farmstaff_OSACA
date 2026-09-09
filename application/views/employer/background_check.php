<div class="row">
  <div class="col-xl-8 col-lg-10">

    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-search-1"></i></span>
          <h3 class="kt-portlet__head-title">Worker Background Check</h3>
        </div>
      </div>
      <div class="kt-portlet__body">
        <p style="color:#a2a3b7;font-size:.9rem;margin-bottom:18px;">
          Search any worker by phone number or Registry ID to view their verified profile before hiring. All searches are logged for audit purposes.
        </p>
        <form method="get" action="<?php echo site_url('dashboard/background-check'); ?>">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Search By</label>
                <select name="type" class="form-control no-select2">
                  <option value="phone"     <?php echo ($type==='phone')    ?'selected':''; ?>>Phone Number</option>
                  <option value="worker_id" <?php echo ($type==='worker_id')?'selected':''; ?>>Registry ID</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Search Value</label>
                <input type="text" name="q" class="form-control"
                  value="<?php echo htmlspecialchars($query); ?>"
                  placeholder="<?php echo $type==='phone'?'e.g. 08012345678':'e.g. FSR-XXXXX99'; ?>"
                  autofocus>
              </div>
            </div>
            <div class="col-md-3" style="display:flex;align-items:flex-end;padding-bottom:2px;">
              <button type="submit" class="btn btn-success btn-bold btn-block"><i class="la la-search"></i> Search</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php if ($query !== '' && !$worker): ?>
      <div class="alert alert-warning alert-dismissible fade show">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text">
          <strong>No result found</strong> for "<?php echo htmlspecialchars($query); ?>".
          This phone number or registry ID is not registered in the Farm Staff Registry.
        </div>
        <div class="alert-close"><button class="close" data-dismiss="alert"><span><i class="la la-close"></i></span></button></div>
      </div>
    <?php endif; ?>

    <?php if ($profile): ?>
      <?php $w=$profile; $ts=(float)$w['trust_score']; $tc=$ts>=70?'success':($ts>=40?'warning':'danger'); ?>
      <div class="kt-portlet">
        <div class="kt-portlet__head" style="background:linear-gradient(135deg,#111,#1a3a1a);">
          <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
              <?php if(!empty($w['photo'])): ?>
                <img src="<?php echo base_url($w['photo']); ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.4);">
              <?php else: ?>
                <span class="kt-badge kt-badge--unified-light kt-badge--lg kt-badge--rounded">
                  <?php echo strtoupper(substr($w['firstname'],0,1)); ?>
                </span>
              <?php endif; ?>
            </span>
            <h3 class="kt-portlet__head-title" style="color:#fff;">
              <?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?>
              <small style="color:rgba(255,255,255,.6);font-family:monospace;margin-left:10px;"><?php echo htmlspecialchars($w['worker_id']); ?></small>
            </h3>
          </div>
          <div class="kt-portlet__head-toolbar">
            <span class="kt-font-<?php echo $tc; ?>" style="font-size:1.4rem;font-weight:800;color:#fff;">
              <?php echo number_format($ts,0); ?><small style="font-size:.7rem;opacity:.7;">/100</small>
            </span>
          </div>
        </div>
        <div class="kt-portlet__body">
          <div class="row">
            <!-- Work History -->
            <div class="col-md-6">
              <h6 class="kt-font-brand" style="margin-bottom:12px;"><i class="la la-briefcase"></i> Work History (<?php echo count($w['work_history']??[]); ?>)</h6>
              <?php if(empty($w['work_history'])): ?>
                <p style="color:#a2a3b7;font-size:.85rem;">No work history recorded.</p>
              <?php else: ?>
                <?php foreach(array_slice($w['work_history'],0,4) as $h): ?>
                <div style="padding:8px 0;border-bottom:1px solid #f7f8fa;font-size:.88rem;">
                  <div style="font-weight:600;"><?php echo htmlspecialchars($h['role']); ?></div>
                  <div style="color:#a2a3b7;"><?php echo htmlspecialchars($h['farm_name']??'—'); ?> &nbsp;·&nbsp;
                    <?php echo date('M Y',strtotime($h['start_date'])); ?> —
                    <?php echo $h['is_current']?'<span class="kt-font-success">Present</span>':date('M Y',strtotime($h['end_date']??'now')); ?>
                  </div>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <!-- Skills -->
            <div class="col-md-6">
              <h6 class="kt-font-brand" style="margin-bottom:12px;"><i class="la la-star"></i> Skills (<?php echo count($w['skills']??[]); ?>)</h6>
              <?php if(empty($w['skills'])): ?>
                <p style="color:#a2a3b7;font-size:.85rem;">No skills on record.</p>
              <?php else: ?>
                <div>
                  <?php foreach($w['skills'] as $s): ?>
                    <span class="kt-badge kt-badge--<?php echo $s['verified']?'success':'secondary'; ?> kt-badge--inline kt-badge--pill" style="margin:3px;">
                      <?php echo $s['verified']?'✓ ':''; ?><?php echo htmlspecialchars($s['skill_name']); ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- Summary numbers -->
              <div class="row" style="margin-top:20px;">
                <?php $accepted_inc=count(array_filter($w['incidents']??[],fn($i)=>$i['status']==='accepted')); ?>
                <?php foreach([['Work Records',count($w['work_history']??[]),'brand'],['Ratings',count($w['ratings']??[]),'warning'],['Verified Skills',count(array_filter($w['skills']??[],fn($s)=>$s['verified'])),'success'],['Incidents',$accepted_inc,$accepted_inc>0?'danger':'success']] as [$lbl,$val,$col]): ?>
                <div class="col-6" style="text-align:center;padding:10px 5px;">
                  <div style="font-size:1.4rem;font-weight:800;" class="kt-font-<?php echo $col; ?>"><?php echo $val; ?></div>
                  <div style="font-size:.75rem;color:#a2a3b7;"><?php echo $lbl; ?></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="kt-portlet__foot">
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?php echo site_url('dashboard/register-worker'); ?>" class="btn btn-success btn-bold btn-sm"><i class="la la-user-plus"></i> Register this Worker</a>
            <a href="<?php echo site_url('worker/profile/'.$w['worker_id']); ?>" class="btn btn-label-brand btn-bold btn-sm">Full Profile →</a>
          </div>
        </div>
      </div>
    <?php elseif($query === ''): ?>
      <div class="kt-portlet">
        <div class="kt-portlet__body" style="text-align:center;padding:48px 20px;">
          <i class="flaticon2-shield" style="font-size:3.5rem;color:#d1d5e4;"></i>
          <h4 style="margin:14px 0 8px;">Verify Workers Before Hiring</h4>
          <p style="color:#a2a3b7;max-width:440px;margin:0 auto;">
            Enter a worker's phone number or Farm Staff Registry ID above to access their verified work history, skills, attendance record and trust score.
          </p>
          <div class="row" style="margin-top:28px;max-width:500px;margin-left:auto;margin-right:auto;">
            <?php foreach([['flaticon2-shield','Verified Identity'],['la la-briefcase','Work History'],['la la-star','Skills Record'],['flaticon2-chart','Trust Score'],['la la-calendar','Attendance'],['flaticon-warning-sign','Incident Record']] as [$ic,$lb]): ?>
            <div class="col-4" style="padding:10px;">
              <div style="background:#f7f8fa;border-radius:8px;padding:14px 8px;text-align:center;">
                <i class="<?php echo $ic; ?>" style="font-size:1.6rem;color:#a2a3b7;"></i>
                <div style="font-size:.75rem;font-weight:600;color:#595d6e;margin-top:6px;"><?php echo $lb; ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>
