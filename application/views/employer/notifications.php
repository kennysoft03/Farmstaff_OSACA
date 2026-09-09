<div class="row">
  <div class="col-xl-8">
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <span class="kt-portlet__head-icon"><i class="kt-font-brand flaticon2-bell-alarm-symbol"></i></span>
          <h3 class="kt-portlet__head-title">Notifications</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
          <span style="font-size:.82rem;color:#a2a3b7;">All marked as read on open</span>
        </div>
      </div>
      <div class="kt-portlet__body kt-portlet__body--fit">
        <?php if(empty($notifications)): ?>
          <div style="text-align:center;padding:48px;">
            <i class="flaticon2-bell-alarm-symbol" style="font-size:3rem;color:#d1d5e4;"></i>
            <p style="color:#a2a3b7;margin:14px 0;">No notifications yet.</p>
          </div>
        <?php else: ?>
          <?php
          $colors=['info'=>'#0dcaf0','warning'=>'#ffb822','success'=>'#34bfa3','danger'=>'#fd3995'];
          foreach($notifications as $n):
          ?>
          <div style="padding:14px 20px;border-bottom:1px solid #f7f8fa;display:flex;gap:14px;align-items:flex-start;background:<?php echo $n['is_read']?'#fff':'#f0f9ff'; ?>;">
            <span style="display:block;width:10px;height:10px;border-radius:50%;background:<?php echo $colors[$n['type']]??'#aaa'; ?>;flex-shrink:0;margin-top:5px;"></span>
            <div style="flex:1;">
              <div style="font-weight:600;font-size:.9rem;"><?php echo htmlspecialchars($n['title']); ?></div>
              <div style="color:#a2a3b7;font-size:.85rem;margin-top:2px;"><?php echo htmlspecialchars($n['message']); ?></div>
              <div style="font-size:.75rem;color:#c5cbe3;margin-top:4px;"><?php echo date('d M Y, g:i A',strtotime($n['created_at'])); ?></div>
            </div>
            <?php if(!empty($n['link'])): ?>
              <a href="<?php echo site_url($n['link']); ?>" class="btn btn-label-brand btn-sm btn-bold" style="flex-shrink:0;">View →</a>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
