<?php
$emp = isset($employer) ? $employer : [];
?>
<!-- Stat Cards -->
<div class="row">
  <div class="col-xl-3 col-md-6">
    <div class="kt-widget24">
      <div class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--space-md kt-portlet--border-bottom-brand">
        <div class="kt-portlet__body kt-portlet__body--center">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Total Workers</h4>
            </div>
            <span class="kt-widget24__stats kt-font-brand"><?php echo number_format((int)$worker_count); ?></span>
          </div>
          <div class="progress progress--sm"><div class="progress-bar kt-bg-brand" style="width:100%"></div></div>
          <div class="kt-widget24__action"><span class="kt-widget24__change">Registered on your farm</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--space-md kt-portlet--border-bottom-success">
      <div class="kt-portlet__body kt-portlet__body--center">
        <div class="kt-widget24__details">
          <div class="kt-widget24__info"><h4 class="kt-widget24__title">Active Workers</h4></div>
          <span class="kt-widget24__stats kt-font-success"><?php echo number_format((int)$active_workers); ?></span>
        </div>
        <div class="progress progress--sm"><div class="progress-bar kt-bg-success" style="width:<?php echo $worker_count > 0 ? round(($active_workers/$worker_count)*100) : 0; ?>%"></div></div>
        <div class="kt-widget24__action"><span class="kt-widget24__change">Currently active</span></div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--space-md kt-portlet--border-bottom-danger">
      <div class="kt-portlet__body kt-portlet__body--center">
        <div class="kt-widget24__details">
          <div class="kt-widget24__info"><h4 class="kt-widget24__title">Pending Incidents</h4></div>
          <span class="kt-widget24__stats kt-font-danger"><?php echo number_format((int)$pending_incidents); ?></span>
        </div>
        <div class="progress progress--sm"><div class="progress-bar kt-bg-danger" style="width:<?php echo $pending_incidents > 0 ? 100 : 0; ?>%"></div></div>
        <div class="kt-widget24__action"><span class="kt-widget24__change">Awaiting admin review</span></div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--space-md kt-portlet--border-bottom-warning">
      <div class="kt-portlet__body kt-portlet__body--center">
        <div class="kt-widget24__details">
          <div class="kt-widget24__info"><h4 class="kt-widget24__title">Farm Reputation</h4></div>
          <span class="kt-widget24__stats kt-font-warning">
            <?php echo !empty($emp['reputation_score']) ? number_format((float)$emp['reputation_score'],1) : '—'; ?>/5
          </span>
        </div>
        <div class="progress progress--sm"><div class="progress-bar kt-bg-warning" style="width:<?php echo !empty($emp['reputation_score']) ? ($emp['reputation_score']/5)*100 : 0; ?>%"></div></div>
        <div class="kt-widget24__action"><span class="kt-widget24__change"><?php echo $emp['total_ratings'] ?? 0; ?> ratings</span></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Recent Workers -->
  <div class="col-xl-8">
    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-user"></i></span>
          <h3 class="kt-portlet__head-title">Recent Workers</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
          <a href="<?php echo site_url('dashboard/workers'); ?>" class="btn btn-label-brand btn-sm btn-bold">View All</a>
        </div>
      </div>
      <div class="kt-portlet__body kt-portlet__body--fit">
        <?php if (empty($recent_workers)): ?>
          <div class="kt-portlet__body" style="text-align:center;padding:40px;">
            <i class="flaticon2-user" style="font-size:3rem;color:#d1d5e4;"></i>
            <p style="color:#a2a3b7;margin:14px 0;">No workers registered yet.</p>
            <a href="<?php echo site_url('dashboard/register-worker'); ?>" class="btn btn-success btn-sm btn-bold">Register First Worker</a>
          </div>
        <?php else: ?>
          <div class="kt-datatable kt-datatable--default kt-datatable--loaded">
            <table class="kt-datatable__table" style="width:100%;">
              <thead class="kt-datatable__head">
                <tr class="kt-datatable__row">
                  <th class="kt-datatable__cell">Worker</th>
                  <th class="kt-datatable__cell">Registry ID</th>
                  <th class="kt-datatable__cell">Role</th>
                  <th class="kt-datatable__cell">Status</th>
                  <th class="kt-datatable__cell">Action</th>
                </tr>
              </thead>
              <tbody class="kt-datatable__body">
                <?php foreach ($recent_workers as $w): ?>
                <tr class="kt-datatable__row">
                  <td class="kt-datatable__cell">
                    <div class="kt-user-card-v2">
                      <div class="kt-user-card-v2__pic">
                        <?php if (!empty($w['photo'])): ?>
                          <img src="<?php echo base_url($w['photo']); ?>" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                          <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded">
                            <?php echo strtoupper(substr($w['firstname'],0,1)); ?>
                          </span>
                        <?php endif; ?>
                      </div>
                      <div class="kt-user-card-v2__details">
                        <span class="kt-user-card-v2__name"><?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?></span>
                        <span class="kt-user-card-v2__desc"><?php echo htmlspecialchars($w['phone']); ?></span>
                      </div>
                    </div>
                  </td>
                  <td class="kt-datatable__cell"><span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill" style="font-family:monospace;"><?php echo htmlspecialchars($w['worker_id']); ?></span></td>
                  <td class="kt-datatable__cell"><span><?php echo htmlspecialchars($w['current_role'] ?? '—'); ?></span></td>
                  <td class="kt-datatable__cell">
                    <?php $sc=['active'=>'success','inactive'=>'secondary','flagged'=>'warning','suspended'=>'danger']; ?>
                    <span class="kt-badge kt-badge--<?php echo $sc[$w['status']]??'secondary'; ?> kt-badge--inline kt-badge--pill"><?php echo $w['status']; ?></span>
                  </td>
                  <td class="kt-datatable__cell">
                    <a href="<?php echo site_url('dashboard/worker/'.$w['id']); ?>" class="btn btn-sm btn-label-brand btn-bold">View</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Right column -->
  <div class="col-xl-4">

    <!-- Quick Actions -->
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-rocket-1"></i></span>
          <h3 class="kt-portlet__head-title">Quick Actions</h3>
        </div>
      </div>
      <div class="kt-portlet__body" style="display:flex;flex-direction:column;gap:8px;">
        <a href="<?php echo site_url('dashboard/register-worker'); ?>" class="btn btn-success btn-block btn-bold"><i class="la la-user-plus"></i> Register New Worker</a>
        <a href="<?php echo site_url('dashboard/background-check'); ?>" class="btn btn-label-brand btn-block btn-bold"><i class="la la-search"></i> Background Check</a>
        <a href="<?php echo site_url('dashboard/workers'); ?>"          class="btn btn-label-brand btn-block btn-bold"><i class="la la-users"></i> View All Workers</a>
      </div>
    </div>

    <!-- Recent Incidents -->
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-danger flaticon-warning-sign"></i></span>
          <h3 class="kt-portlet__head-title">Recent Incidents</h3>
        </div>
      </div>
      <div class="kt-portlet__body kt-portlet__body--fit">
        <?php if (empty($recent_incidents)): ?>
          <div style="padding:16px;color:#a2a3b7;font-size:.88rem;text-align:center;">No incidents reported. ✅</div>
        <?php else: ?>
          <?php foreach ($recent_incidents as $inc): ?>
            <div style="padding:12px 20px;border-bottom:1px solid #f7f8fa;">
              <div style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($inc['worker_name']??'—'); ?></div>
              <div style="font-size:.78rem;color:#a2a3b7;"><?php echo htmlspecialchars($inc['incident_type']); ?></div>
              <?php $sc=['pending'=>'warning','reviewed'=>'info','accepted'=>'danger','rejected'=>'success']; ?>
              <span class="kt-badge kt-badge--<?php echo $sc[$inc['status']]??'secondary'; ?> kt-badge--inline kt-badge--pill" style="margin-top:4px;"><?php echo $inc['status']; ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Farm Info -->
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-success flaticon2-world"></i></span>
          <h3 class="kt-portlet__head-title">Farm Info</h3>
        </div>
      </div>
      <div class="kt-portlet__body">
        <?php $rows=[['Farm',$emp['farm_name']??''],['Type',$emp['farm_type']??'—'],['LGA',$emp['lga']??'—'],['Status',ucfirst($emp['status']??'')]];
        foreach($rows as [$k,$v]): ?>
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f7f8fa;font-size:.88rem;">
            <span style="color:#a2a3b7;"><?php echo $k; ?></span>
            <span style="font-weight:600;"><?php echo htmlspecialchars($v); ?></span>
          </div>
        <?php endforeach; ?>
        <a href="<?php echo site_url('dashboard/profile'); ?>" class="btn btn-label-brand btn-sm btn-block btn-bold" style="margin-top:12px;">Edit Profile</a>
      </div>
    </div>

  </div>
</div>
