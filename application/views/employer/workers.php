<!-- Filters -->
<div class="kt-portlet kt-portlet--mobile">
  <div class="kt-portlet__head kt-portlet__head--lg">
    <div class="kt-portlet__head-label">
      <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-search-1"></i></span>
      <h3 class="kt-portlet__head-title">Search Workers</h3>
    </div>
  </div>
  <div class="kt-portlet__body">
    <form method="get" class="row">
      <div class="col-md-5">
        <div class="kt-form__group">
          <label>Search</label>
          <input type="text" name="search" class="form-control"
            value="<?php echo htmlspecialchars($filters['search']??''); ?>"
            placeholder="Name, phone or registry ID...">
        </div>
      </div>
      <div class="col-md-3">
        <div class="kt-form__group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="">All Statuses</option>
            <?php foreach(['active','inactive','flagged','suspended'] as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo ($filters['status']===$s)?'selected':''; ?>><?php echo ucfirst($s); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="col-md-4" style="display:flex;align-items:flex-end;gap:8px;padding-bottom:2px;">
        <button type="submit" class="btn btn-success btn-bold"><i class="la la-search"></i> Search</button>
        <a href="<?php echo site_url('dashboard/workers'); ?>" class="btn btn-secondary btn-bold">Clear</a>
        <a href="<?php echo site_url('dashboard/register-worker'); ?>" class="btn btn-brand btn-bold"><i class="la la-plus"></i> Register Worker</a>
      </div>
    </form>
  </div>
</div>

<!-- Results -->
<div class="kt-portlet kt-portlet--mobile">
  <div class="kt-portlet__head kt-portlet__head--lg">
    <div class="kt-portlet__head-label">
      <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-user"></i></span>
      <h3 class="kt-portlet__head-title">Workers <span style="font-weight:400;color:#a2a3b7;font-size:.88rem;">(<?php echo number_format($total); ?>)</span></h3>
    </div>
    <div class="kt-portlet__head-toolbar">
      <a href="<?php echo site_url('dashboard/register-worker'); ?>" class="btn btn-success btn-sm btn-bold"><i class="la la-plus"></i> Register Worker</a>
    </div>
  </div>
  <div class="kt-portlet__body kt-portlet__body--fit">
    <?php if (empty($workers)): ?>
      <div style="text-align:center;padding:48px;">
        <i class="flaticon2-user" style="font-size:3rem;color:#d1d5e4;"></i>
        <h4 style="color:#a2a3b7;margin:14px 0 8px;">No workers found</h4>
        <p style="color:#a2a3b7;">Try adjusting your filters or <a href="<?php echo site_url('dashboard/register-worker'); ?>">register a new worker</a>.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover" style="margin:0;">
          <thead class="thead-light">
            <tr>
              <th>Worker</th><th>Registry ID</th><th>Phone</th>
              <th>Current Role</th><th>Trust Score</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($workers as $w): ?>
            <tr>
              <td>
                <div class="kt-user-card-v2">
                  <div class="kt-user-card-v2__pic">
                    <?php if (!empty($w['photo'])): ?>
                      <img src="<?php echo base_url($w['photo']); ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                      <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded"><?php echo strtoupper(substr($w['firstname'],0,1)); ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="kt-user-card-v2__details">
                    <span class="kt-user-card-v2__name"><?php echo htmlspecialchars($w['firstname'].' '.$w['lastname']); ?></span>
                    <span class="kt-user-card-v2__desc"><?php echo htmlspecialchars($w['lga']??''); ?></span>
                  </div>
                </div>
              </td>
              <td><span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill" style="font-family:monospace;"><?php echo htmlspecialchars($w['worker_id']); ?></span></td>
              <td><?php echo htmlspecialchars($w['phone']); ?></td>
              <td><?php echo htmlspecialchars($w['current_role']??'—'); ?></td>
              <td>
                <?php $ts=(float)$w['trust_score']; $tc=$ts>=70?'success':($ts>=40?'warning':'danger'); ?>
                <span class="kt-font-<?php echo $tc; ?>" style="font-weight:700;"><?php echo number_format($ts,0); ?>/100</span>
              </td>
              <td>
                <?php $sc=['active'=>'success','inactive'=>'secondary','flagged'=>'warning','suspended'=>'danger']; ?>
                <span class="kt-badge kt-badge--<?php echo $sc[$w['status']]??'secondary'; ?> kt-badge--inline kt-badge--pill"><?php echo $w['status']; ?></span>
              </td>
              <td>
                <a href="<?php echo site_url('dashboard/worker/'.$w['id']); ?>" class="btn btn-sm btn-label-brand btn-bold">View</a>
                <a href="<?php echo site_url('dashboard/worker/'.$w['id'].'/edit'); ?>" class="btn btn-sm btn-label-success btn-bold">Edit</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($total > $limit): ?>
  <div class="kt-portlet__foot">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
      <span style="color:#a2a3b7;font-size:.85rem;">
        Showing <?php echo min(($page-1)*$limit+1,$total); ?>–<?php echo min($page*$limit,$total); ?> of <?php echo number_format($total); ?>
      </span>
      <ul class="pagination pagination-sm" style="margin:0;">
        <?php
        $base=site_url('dashboard/workers?page=');
        $qs='&search='.urlencode($filters['search']??'').'&status='.urlencode($filters['status']??'');
        $pages=ceil($total/$limit);
        for($p=1;$p<=$pages;$p++): ?>
          <li class="page-item <?php echo $p==$page?'active':''; ?>">
            <a class="page-link" href="<?php echo $base.$p.$qs; ?>"><?php echo $p; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>
</div>
