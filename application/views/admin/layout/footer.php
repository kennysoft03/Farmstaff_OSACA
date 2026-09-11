      </div><!-- /padding wrapper -->
    </div><!-- /kt-container -->
  </div><!-- /kt-content -->
  <!-- end:: Content -->

  <!-- begin:: Footer -->
  <div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
    <div class="kt-container kt-container--fluid">
      <div class="kt-footer__copyright">
        <?php echo date('Y'); ?> &nbsp;&copy;&nbsp;
        <a href="<?php echo site_url('/'); ?>" target="_blank" class="kt-link"><?php echo SITE_NAME; ?></a>
        &nbsp;—&nbsp; For Farmers in Ondo State, Nigeria
      </div>
      <div class="kt-footer__menu">
        <a href="<?php echo site_url('/'); ?>" target="_blank" class="kt-footer__menu-link kt-link">Public Site</a>
        <a href="<?php echo site_url('admin/settings'); ?>" class="kt-footer__menu-link kt-link">Settings</a>
      </div>
    </div>
  </div>
  <!-- end:: Footer -->

</div><!-- /kt-wrapper -->
</div><!-- /kt-page -->
</div><!-- /kt-root -->

<!-- KT Global Config -->
<script>
var KTAppOptions = {
  "colors": {
    "state": {
      "brand":   "#5d78ff", "dark": "#282a3c", "light": "#ffffff",
      "primary": "#5867dd", "success": "#34bfa3", "info": "#36a3f7",
      "warning": "#ffb822", "danger":  "#fd3995"
    },
    "base": {
      "label": ["#c5cbe3","#a1a8c3","#3d4465","#3e4466"],
      "shape": ["#f0f3ff","#d9dffa","#afb4d4","#646c9a"]
    }
  }
};
</script>

<!-- KT Mandatory Vendors -->
<script src="<?php echo base_url('assetsa/vendors/general/jquery/dist/jquery.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/popper.js/dist/umd/popper.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/js-cookie/src/js.cookie.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/moment/min/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/tooltip.js/dist/umd/tooltip.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/sticky-js/dist/sticky.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/wnumb/wNumb.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/select2/dist/js/select2.full.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/sweetalert2/dist/sweetalert2.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/waypoints/lib/jquery.waypoints.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/counterup/jquery.counterup.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/chart.js/dist/Chart.bundle.js'); ?>"></script>

<!-- KT Theme Bundle -->
<script src="<?php echo base_url('assetsa/js/demo1/scripts.bundle.js'); ?>"></script>

<!-- Farmstaff Admin JS -->
<script src="<?php echo base_url('assets/js/farmstaff.js'); ?>"></script>

<!-- Auto-init select2 on all selects -->
<script>
$(function(){
  $('select.form-control').not('.no-select2').each(function(){
    var cnt = $(this).find('option').length;
    if(cnt >= 6){
      $(this).select2({ width:'100%', allowClear: !$(this).prop('required') });
    }
  });
  // Auto-dismiss alerts after 5s
  setTimeout(function(){
    $('.alert.alert-success, .alert.alert-danger').fadeOut(500);
  }, 5000);
});
</script>

</body>
</html>
